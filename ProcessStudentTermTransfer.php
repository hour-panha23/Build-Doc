<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\StudentPromoteHelper;
use App\Services\SignalService;
use App\Models\Notifier;
use Vsd\Database\DBX;
use XValidationError;

class ProcessStudentTermTransfer implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $enroll;
    public $termInfo;
    public $promote_info;
    public $ss;
    public $promoteSessionId;
    public $taskId;
    public $lastEmitTime;
    public $senderSockerID;

    public function __construct(
        $enroll,
        $termInfo,
        $promote_info,
        $ss,
        $promoteSessionId,
        $taskId,
        $lastEmitTime,
        $senderSockerID
    ) {
        $this->enroll = $enroll;
        $this->termInfo = $termInfo;
        $this->promote_info = $promote_info;
        $this->ss = $ss;
        $this->promoteSessionId = $promoteSessionId;
        $this->taskId = $taskId;
        $this->lastEmitTime = $lastEmitTime;
        $this->senderSockerID = $senderSockerID;
    }

    public function handle()
    {
        // Cancel execution if the parent batch was cancelled due to an earlier failure
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        try {
            DB::transaction(function () {
                $enrollment_id = $this->enroll->enrollment_id;
                $term_id = $this->termInfo->term_id;
                $ss = $this->ss;

                $res = StudentPromoteHelper::promoteStudentV2(
                    (object) [
                        "enrollment_id" => $enrollment_id,
                        "student_id" => $this->enroll->student_id,
                        "program_id" => $this->enroll->program_id,
                        "academic_year" => $this->termInfo->academic_year,
                        "exept_student_ids" => null,
                        "promote_session_id" => $this->promoteSessionId,
                        'promote_info' => $this->promote_info
                    ],
                    $ss
                );

                if (($res->status ?? '') !== 'OK') {
                    Log::info(json_encode(['180' => $res->error_message ?? 'Promotion failed']));
                    XValidationError::raise($res->error_message ?? 'Promotion failed');
                }

                /* Atomic Progress Tracking */
                DB::table('sync_tasks')
                    ->where('id', $this->taskId)
                    ->update([
                        'progress_done' => DB::raw('progress_done + 1')
                    ]);

                $task = DB::table('sync_tasks')
                    ->where('id', $this->taskId)
                    ->first();

                $percent = ($task && $task->progress_total > 0)
                    ? round(($task->progress_done / $task->progress_total) * 100, 2)
                    : 0;

                DB::table('sync_tasks')
                    ->where('id', $this->taskId)
                    ->update([
                        'progress_percent' => $percent,
                        'updated_at' => now()
                    ]);

                /* Throttled Websocket Emission */
                $cacheKey = "task_last_emit_{$this->taskId}";
                $currentTime = microtime(true);
                $lastEmitTime = Cache::get($cacheKey, 0);

                if ($currentTime - $lastEmitTime >= 2) {
                    Cache::put($cacheKey, $currentTime, 5);

                    SignalService::taskEmit(
                        event: 'term_promoting',
                        payload: [
                            'progress_total' => $task->progress_total ?? 0,
                            'progress_done' => $task->progress_done ?? 0,
                            'progress_percent' => $percent,
                            "socket_id" => $this->senderSockerID,
                        ],
                        user_class: $ss->user_class ?? null,
                        socketId: $this->senderSockerID,
                    );
                }
            });

        } catch (\Throwable $e) {
            Log::error('ProcessStudentTermTransfer FAILED: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            throw $e; // Re-throw so the Bus batch catch handler picks up the failure
        }
    }
}