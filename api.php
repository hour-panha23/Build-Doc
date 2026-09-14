<?php

use App\Http\Controllers\Formal\CourseController;
use App\Http\Controllers\Formal\CurriculaController;
use App\Http\Controllers\Formal\CourseListController;
use App\Http\Controllers\Tutor\TutorCoursesController;
use App\Http\Controllers\Formal\CourseTypeController;
use App\Http\Middleware\CustomRateLimiter;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ActivityController;
// use App\Http\Controllers\AudioController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Auth\GuardianAuthController;
use App\Http\Controllers\Auth\ThirdPartyIntegrationController;
// use App\Http\Controllers\MobileApi\HomePageController;

//use App\Http\Controllers\MobileApi\MobileApiController;

// use App\Http\Controllers\MobileSetting\BannerController;
// use App\Http\Controllers\MobileSetting\BookAndMaterialController;
// use App\Http\Controllers\MobileSetting\SocialMediaController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramLevelController;
use App\Http\Controllers\PromoteStudentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\Formal\ClassSessionController;
use App\Http\Controllers\SiblingDiscountController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentTestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LeaveInfoController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\Wis\PriceListController;
use App\Http\Controllers\Wis\PriceListController as WisPriceListController;
use App\Http\Controllers\EarlyPromotionController;
use App\Http\Controllers\OtherFeeController;
use App\Http\Controllers\MobileAppSettingsController;
use App\Http\Controllers\TestFeeController;
use App\Http\Controllers\SuspendController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Formal\HolidayController;
use App\Http\Controllers\MaterialItemController;
use App\Http\Controllers\AccountTransactionController;
//use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
//use App\Models\SMS;
//use App\Models\Notifier;

use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\CompanyController;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyProfileController;

// use App\Http\Controllers\MailController;
use App\Http\Controllers\DashboardController;
//use App\Http\Controllers\MobileAppSettingsController;
// use App\Http\Controllers\PromotionController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\Formal\TeacherController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\Formal\ClassController;
use App\Http\Controllers\Formal\GradeSheetController;
use App\Http\Controllers\Formal\ClassScheduleController;
use App\Http\Controllers\Formal\DailyScheduleController;
use App\Http\Controllers\ScheduleDayController;
use App\Http\Controllers\Formal\AttendanceTrackController;
use App\Http\Controllers\Formal\TrackShiftController;
use App\Http\Controllers\Formal\TrackActionController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\StudentPermissionController;
use App\Http\Controllers\ReviewController;

use App\Http\Controllers\InvoiceNotificationController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashCollectionController;
use App\Http\Controllers\DisCountByMonthController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\TeacherCommentController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\GroupPreparationController;
use App\Http\Controllers\GraduatedController;



//use App\Models\GeneralSettings;

//use XPublicStorage;
//use App\Models\SystemSetting;
// use App\Models\Patient;
// use App\Models\Inventory\Brand;

/*
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'apiLogin']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//begin:: Admin notifications
Route::middleware('auth.api', CustomRateLimiter::class)->group(function(){
    Route::post('pending-requests', [NotificationController::class, 'getPendingRequests']);
    Route::post('notifications', [NotificationController::class, 'getNotificationListByUser']);
    Route::post('unread-count',[NotificationController::class,'getUnreadCount']);
    // Route::post('mark-read-all',[NotificationController::class,'markReadAll']);
});
//end:: Admin Notification

//Clear trash => to delete expired data such as expired notitifications
Route::post('trash/clear', function (Request $req) {
    $res = \App\Models\Notifier::clearNotifications(null);
    return response()->json($res);
});

Route::get('clear-trash', function () {
    $res = \App\Models\Notifier::clearNotifications(null);
    return response()->json($res);
});

// Route::post('/student/attendance/access-scan', [StudentAttendanceController::class, 'checkAccessScan']);

//Route::post('/student/attendance-scan',[StudentAttendanceController::class,'scanAttendance']);
Route::post('/student/attendance/scan', [StudentAttendanceController::class, 'scanAttendance']);
Route::post('/student/attendance/last-scan', [StudentAttendanceController::class, 'getLastAttendanceScan'])->name('student.attendance.last.scan');
Route::post('/auth/guardian/login', [GuardianAuthController::class, 'guardianLogin']);

//Route::post('/auth/guardian/register', [MobileApiController::class, 'registerApp']);
Route::post('/auth/guardian/register', [GuardianController::class, 'selfRegister']);

//Route::post('/auth/guardian/delete-account', [MobileApiController::class, 'deleteAccount']);
Route::post('/auth/guardian/delete-account', [GuardianAuthController::class, 'deleteAccount']);

Route::post('/auth/guardian/profile', [GuardianAuthController::class, 'getProfile']);
Route::post('/auth/siksara/test', [ThirdPartyIntegrationController::class, 'sendDataToThirdParty']);
Route::post('/auth/guardian/change-password', [GuardianLoginController::class, 'changePasswords']);
Route::post('/auth/guardian/change-profile', [GuardianController::class, 'updateProfile']);

// Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('mobile')->group( function (){
//     //mobile api
//     Route::post('/notifications/read-all',[MobileApiController::class, 'markReadAll']);
//     Route::post('/notifications/unread-count',[MobileApiController::class, 'getUnreadCount']);
//     Route::post('/pickup-my-kids',[MobileApiController::class,'pickupMyKids']);
//     Route::post('/home-page',[MobileApiController::class,'homePage']);
//     Route::post('/student-attendance',[MobileApiController::class,'attendanceList']);
//     Route::post('/payment-invoice',[MobileApiController::class,'getChildrenInvoices']);
//     Route::post('/student-enrollments',[MobileApiController::class,'getStudentEnrollment']);
//     Route::post('/social-media',[MobileApiController::class,'getSocialMediaList']);
//     Route::post('/latest-invoice',[MobileApiController::class,'getChildLatestInvoice']);
//     Route::get('/term-conditions',[MobileApiController::class,'termConditionForm'])->name('termConditions');
//     Route::get('/privacy-url',[MobileApiController::class,'privacyURL']);
//    //end mobile api

// });

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('test-fee')->group(function () {
    Route::post('save', [TestFeeController::class, 'save']);
    Route::post('details', [TestFeeController::class, 'getDetails']);
    Route::post('form-options', [TestFeeController::class, 'getFormOptions']);
    Route::post('list-paginate', [TestFeeController::class, 'getListPaginate']);
    Route::post('receipt', [TestFeeController::class, 'getTestFeeReceipt']);
    Route::post('delete', [TestFeeController::class, 'delete']);
    Route::post('other-fee-amount', [TestFeeController::class, 'getOtherFeeAmount']);
});

Route::middleware([CustomRateLimiter::class])->prefix('student')->group(function () {
    Route::post('/pickup/reset', [GuardianController::class, 'resetPickup']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('student')->group(function () {
    //api endpoint for filter options on the Find Students Component
    //Route::post('/find-student/filter-options', [GeneralSettingsController::class, 'getOptions_academic_year']);


    // Route::post('/pickup/reset',[GuardianController::class,'resetPickup']);
    Route::post('/list-paginate', [StudentController::class, 'getStudents_paginate']);
    Route::post('/change-student-id', [StudentController::class, 'changeStudentId']);
    Route::post('/option-search', [StudentController::class, 'optionSearch']);
    Route::post('/filter-options', [GeneralSettingsController::class, 'getOptions_academic_year']);
    Route::post('/form-options', [StudentController::class, 'formOptions']);
    Route::post('/siblings', [StudentController::class, 'getSiblings']);
    Route::post('/groups', [StudentController::class, 'getActiveGroups']);
    Route::post('/card-info', [StudentController::class, 'getStudentCardInfo']);
    Route::post('/update-info', [StudentController::class, 'updateStudentInfo']);
    Route::post('/details-info', [StudentController::class, 'getStudentBasicInfoDetails']);
    Route::post('form-options-info', [StudentController::class, 'getFormOptionInfo']);
    Route::post('form-options-file', [StudentController::class, 'getFormOptionFile']);
    Route::post('/save-audio', [StudentController::class, 'saveAudioFile']);
    Route::post('/save-file', [StudentController::class, 'saveFile']);
    Route::post('/audio', [StudentController::class, 'getAudioFile']);
    Route::post('/delete-audio', [StudentController::class, 'deleteAudioFile']);
    //Create new student profile with parent info
    Route::post('/create', [StudentController::class, 'createStudent']);
    Route::post('/delete-special', [StudentController::class, 'deleteStudentSpecial']);
    Route::post('/delete-special-rank', [StudentController::class, 'deleteStudentSpecialRank']);
    //Route::post('/list-paginate',[StudentController::class,'studentPaginate']);
    Route::post('/delete-student-enrollment', [StudentController::class, 'deleteStudentEnrollment']);
    Route::post('/details-student', [StudentController::class, 'studentDetials']);
    Route::post('/delete-verified', [EnrollmentController::class, 'deleteVerifiedStudent']);
    Route::post('/information', [StudentController::class, 'studentInformation']);
    Route::post('/search', [StudentController::class, 'searchStudent']);
    Route::post('/set-referrer', [StudentController::class, 'setReferrer']);
    Route::post('/set-referal-commission', [StudentController::class, 'setReferalCommission']);
    Route::post('/enrollment-details', [StudentController::class, 'getStudentEnrollmentInfo']);

    //** PriceListController */
    Route::post('/invoice-list', [InvoiceController::class, 'studentInvoice']);
    Route::post('/generate-invoice', [InvoiceController::class, 'generateInvoice']);
    // Route::post('/find',[PriceListController::class,'findStudent']);
    Route::post('/find', [InvoiceController::class, 'findStudent']);
    // Route::post('/find',[InvoiceController::class,'findStudent']);
    Route::post('/generate-invoice/details', [PriceListController::class, 'generateInvoiceDetails']);
    Route::post('/invoice/receive', [InvoiceController::class, 'receive']);// schoolFeePay change to receive
    Route::post('/school-fee/pay-by-end-date', [InvoiceController::class, 'schoolFeePayByEndDate']);
    Route::post('/invoice-delete', [InvoiceController::class, 'deleteInvoice']);
    Route::post('/invoice-cancel', [InvoiceController::class, 'cancelInvoice']);
    // Route::post('/invoice-to-active',[PriceListController::class,'turnInvoiceToActive']);
    Route::post('/invoice-update', [InvoiceController::class, 'updateInvoice']);
    // Route::post('/invoice-items',[PriceListController::class,'getInvoiceItems']);

    //** AttendanceController */
    Route::post('/attendance-save', [StudentAttendanceController::class, 'saveAttendance']);

    Route::post('/test/save', [StudentTestController::class, 'saveTest']);
    Route::post('/test/section-save', [StudentTestController::class, 'scanAttendance']);

    //Route::post('/attendance/scanout',[StudentAttendanceController::class,'scanAttendanceOut']);
    Route::post('/attendance-list', [StudentAttendanceController::class, 'getAttendanceList']);
    Route::post('/attendance-report', [StudentAttendanceController::class, 'getAttendanceReport']);
    Route::post('/attendance-details', [StudentAttendanceController::class, 'attendanceDetails']);
    Route::post('/attendance-date-details', [StudentAttendanceController::class, 'getAttendanceDateDetails']);
    Route::post('/options-by-group', [StudentAttendanceController::class, 'studentListByGroup']);
    Route::post('/options-group', [StudentAttendanceController::class, 'optionsGroup']);
    Route::post('/options-attendance-types', [StudentAttendanceController::class, 'optionsAttendanceTypes']);
    Route::post('/attendance-list-report', [StudentAttendanceController::class, 'studentAttendanceListReport']);
    Route::post('/attendance-form-options', [StudentAttendanceController::class, 'getFormOptions']);


    Route::post('/family', [GuardianController::class, 'getFamily']);
    Route::post('original-discount', [StudentController::class, 'getStudentOriginalDiscountList']);
    Route::post('import', [StudentController::class, 'studentImport']);
    Route::post('import-unpaid', [StudentController::class, 'studentImportUnpaid']);
    Route::post('import-or-upgrade', [StudentController::class, 'importOrUpgrade']);
    Route::post('/change-family', [StudentController::class, 'changeFamily']);
    Route::post('/form-option-transfer', [StudentController::class, 'formOptionTransfer']);
    Route::post('tuition-trx-info', [StudentController::class, 'getStudentTransactionTuition']);
    Route::post('/delete-student-photo', [StudentController::class, 'deleteStudentPhoto']);
    Route::post('/transfer-options', [StudentController::class, 'getInvoiceTransferOptions']);
    Route::post('/transfer-to-account', [StudentController::class, 'transferToAccount']);
    Route::post('/view-info', [StudentController::class, 'viewStudentInfo']);
    Route::post('/edit-child-order', [StudentController::class, 'editChildOrder']);
    Route::post('/set-child', [StudentController::class, 'setChild']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('payment-method')->group(function () {
    Route::post('save', [PaymentMethodController::class, 'save']);
    Route::post('list', [PaymentMethodController::class, 'getList']);
    Route::post('details', [PaymentMethodController::class, 'getDetails']);
    Route::post('form-options', [PaymentMethodController::class, 'getFormOptions']);
    Route::post('delete', [PaymentMethodController::class, 'delete']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('activity')->group(function () {
    Route::post('/create-request-change', [ActivityController::class, 'createRequestChange']);
    Route::post('/send-request-change', [ActivityController::class, 'sendRequestChange']);
    Route::post('/request-change/list-paginate', [ActivityController::class, 'requestChangeListPaginateList']);
    Route::post('/request-discount', [ActivityController::class, 'requestDiscount']);
    Route::post('/create-request-discount', [ActivityController::class, 'createDiscountRequest']);
    Route::post('/create-discount-raw', [ActivityController::class, 'createDiscountRaw']);
    Route::post('/request-discount-details', [ActivityController::class, 'approvalDiscountDetails']);
    Route::post('/request-discount-form-options', [ActivityController::class, 'requestDiscountFormOptions']);
    Route::post('/send-request-discount', [ActivityController::class, 'sendRequestDiscount']);
    Route::post('/request-discount/list-paginate', [ActivityController::class, 'requestDiscountListPaginate']);
    Route::post('/request-change/delete', [ActivityController::class, 'deleteRequest']);
    Route::post('/request-discount/delete', [ActivityController::class, 'deleteRequestDiscount']);
    Route::post('/preview-request-payment', [ActivityController::class, 'previewRequestPaymentFee']);
    Route::post('/print', [ActivityController::class, 'printInfo']);
    Route::post('/approve-info', [ActivityController::class, 'approveInfo']);
    Route::post('/get-term-from-request', [ActivityController::class, 'getTermFromRequest']);
    Route::post('request-change-details', [ActivityController::class, 'requestChangeDetails']);
    Route::post('/request-change-from-options',[ActivityController::class, 'requestChangeFromOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('suspend')->group(function () {
    Route::post('/create-request-suspend-fee', [SuspendController::class, 'createRequestChange']);
    Route::post('/send-request-suspend-fee', [SuspendController::class, 'sendRequestSuspendFee']);
    Route::post('/send-request-suspend-fee-return', [SuspendController::class, 'sendRequestSuspendFeeReturn']);
    Route::post('/request-suspend-fee/list-paginate', [SuspendController::class, 'suspendFeeListPaginateList']);
    Route::post('/details', [SuspendController::class, 'getDetails']);
    Route::post('/form-options', [SuspendController::class, 'getFormOptions']);
    Route::post('/request-discount', [SuspendController::class, 'requestDiscount']);
    Route::post('/create-request-discount', [SuspendController::class, 'createDiscountRequest']);
    Route::post('/request-discount-details', [SuspendController::class, 'approvalDiscountDetails']);
    Route::post('/send-request-discount', [SuspendController::class, 'sendRequestDiscount']);
    Route::post('/request-discount/list-paginate', [SuspendController::class, 'requestDiscountListPaginate']);
    Route::post('/request-suspend-fee/delete', [SuspendController::class, 'deleteRequest']);
    Route::post('/request-discount/delete', [SuspendController::class, 'deleteRequestDiscount']);
    Route::post('/preview-request-suspend-fee', [SuspendController::class, 'previewRequestSuspendFee']);
    Route::post('/print', [SuspendController::class, 'printInfo']);
    Route::post('/approve-info', [SuspendController::class, 'approveInfo']);
});


Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('approval')->group(function () {
    Route::post('/pending/discount/count', [ActivityController::class, 'getPendingDiscountCount']);
    Route::post('/pending/leave/count', [ActivityController::class, 'getPendingLeaveCount']);
    Route::post('/pending/activity/count', [ActivityController::class, 'getPendingOtherRequestCount']);
    Route::post('/pending/suspend/count', [SuspendController::class, 'getPendingSuspendFeeCount']);
    Route::post('/pending/counts', [ActivityController::class, 'getPendingRequestCount']);
    Route::post('/request-change-list', [ActivityController::class, 'approvalActivityListPaginateList']);
    Route::post('/request-suspend-fee-list', [SuspendController::class, 'approvalSuspendFeeListPaginateList']);
    Route::post('/request-discount-list', [ActivityController::class, 'approvalDiscountListPaginate']);
    Route::post('/approve-request-change', [ActivityController::class, 'approveRequestChange']);
    Route::post('/approve-request-suspend-fee', [SuspendController::class, 'approveRequestSuspendFee']);
    Route::post('reject-request-suspend-fee', [SuspendController::class, 'rejectRequestSuspendFee']);
    Route::post('/approve-request-discount', [ActivityController::class, 'approveRequestDiscount']);
    Route::post('/reject-request-change', [ActivityController::class, 'rejectRequestChange']);
    Route::post('/reject-request-discount', [ActivityController::class, 'rejectRequestDiscount']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('promote')->group(function () {
    Route::post('/students', [PromoteStudentController::class, 'promoteStudents']);
    Route::post('/verify', [PromoteStudentController::class, 'verifyPromotedStudent']);
    Route::post('/delete', [PromoteStudentController::class, 'deleteNewPromoted']);
    Route::post('/student-list-paginate', [PromoteStudentController::class, 'getList_paginate']);
    Route::post('/form-options', [PromoteStudentController::class, 'getFormOptions']);
    Route::post('/parent-info', [PromoteStudentController::class, 'getParentInfo']);
    Route::post('/family', [PromoteStudentController::class, 'getParentInfo']);
    Route::post('/options', [PromoteStudentController::class, 'promoteOption']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('fee-type')->group(function () {
    Route::post('/save', [FeeTypeController::class, 'save']);
    Route::post('/details', [FeeTypeController::class, 'getDetails']);
    Route::post('/list', [FeeTypeController::class, 'getList']);
    Route::post('/form-options', [FeeTypeController::class, 'getFormOptions']);
    Route::post('/delete', [FeeTypeController::class, 'delete']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('sessions')->group(function () {
    Route::post('/update', [SessionController::class, 'updateSession']);
    Route::post('/session-program-update', [SessionController::class, 'updateSessionProgram']);
    Route::post('/details', [SessionController::class, 'details']);
    Route::post('/session-program-details', [SessionController::class, 'SessionProgramDetails']);
    Route::post('/form-option', [SessionController::class, 'getFormOptions']);
    Route::post('/list', [SessionController::class, 'list']);
    Route::post('/list-session-program', [SessionController::class, 'listSessionProgram']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('class-sessions')->group(function () {
    Route::post('/save', [ClassSessionController::class, 'save']);
    Route::post('/details', [ClassSessionController::class, 'details']);
    Route::post('/form-option', [ClassSessionController::class, 'getFormOptions']);
    Route::post('/list', [ClassSessionController::class, 'list']);
    Route::post('/list-class-session', [ClassSessionController::class, 'listClassSession']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->group(function () {
    Route::post('/form-option', [GeneralSettingsController::class, 'select_options']);//180
});


Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('option')->group(function () {
    Route::post('/prev-program', [GeneralSettingsController::class, 'options_program']);
    Route::post('/prev-program-level', [GeneralSettingsController::class, 'options_level']);
    Route::post('/other-fee', [GeneralSettingsController::class, 'otherFeeFormOptions']);
    Route::post('/other-fee-info', [GeneralSettingsController::class, 'getFeeTypeInfo']);
    Route::post('/other-fee-info-by-end-date', [GeneralSettingsController::class, 'getFeeTypeInfoByEndDate']);
    Route::post('/request-type', [GeneralSettingsController::class, 'requestTypeOptions']);
    Route::post('/request-status', [GeneralSettingsController::class, 'requestStatusOptions']);
    Route::post('/discount-type', [GeneralSettingsController::class, 'requestDiscountOptions']);
    Route::post('/request-type-input', [GeneralSettingsController::class, 'requestTypeTnput']);
    Route::post('/request-type-dialog', [GeneralSettingsController::class, 'requestTypeDialog']);
    Route::post('/student-request-info', [GeneralSettingsController::class, 'optionsStudentRequest']);
    Route::post('/student-enrollment', [GeneralSettingsController::class, 'optionStudentEnrollments']);
    Route::post('/group-time', [GeneralSettingsController::class, 'optionSessionTime']);
    Route::post('/session-time', [GeneralSettingsController::class, 'optionSessionTimeByShortCut']);
    Route::post('/session-program-time', [GeneralSettingsController::class, 'optionSessionProgramTime']);
    Route::post('/fee-types', [GeneralSettingsController::class, 'optionFeeType']);
    Route::post('/test-fee', [GeneralSettingsController::class, 'optionTestFee']);
    Route::post('/term-academic-year', [GeneralSettingsController::class, 'optionsTermAcademicYear']);
    Route::post('/discount-type-by-fee-type', [GeneralSettingsController::class, 'optionsDiscountTypeByFeeType']);
    Route::post('/program-by-department', [GeneralSettingsController::class, 'optionsProgramByDepartment']);
    Route::post('admission-date', [GeneralSettingsController::class, 'optionsAdmissionDate']);
    Route::post('/level-request', [GeneralSettingsController::class, 'optionsLevelRequest']);
});


Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('settings')->group(function () {
    Route::post('/options-bank', [GeneralSettingsController::class, 'optionsBank']);
    Route::post('/options-level', [GeneralSettingsController::class, 'getOptions_level']);
    // Route::post('/options-program', [StudentController::class, 'getOptions_program']);
    Route::post('/options-academic-year', [GeneralSettingsController::class, 'getOptions_academic_year']);
    Route::post('/options-academic-years', [GeneralSettingsController::class, 'get_academic_years']);
    //Route::post('/options-term', [StudentController::class, 'getOptions_term']);
    Route::post('/options-term', [GeneralSettingsController::class, 'getOptions_term']);
    Route::post('/options-program', [GeneralSettingsController::class, 'getOptions_program']);
    Route::post('/options-prepayment-term', [GeneralSettingsController::class, 'getStudentPrepaymentTerm']);
    Route::post('/options-track', [GeneralSettingsController::class, 'getOptions_track']);
    Route::post('/options-track_shifts', [GeneralSettingsController::class, 'getOptions_track_shifts']);

    Route::post('/imported-file-history', [StudentController::class, 'importedFileHistory']);
    Route::post('/form-prepaid-invoice', [GeneralSettingsController::class, 'getFormPrePaidInvoice']);
    Route::post('/form-generate-invoice', [GeneralSettingsController::class, 'getFormGenerateInvoice']);
    Route::post('/options-general-invoice', [GeneralSettingsController::class, 'getOptionsGeneralInvoice']);
    Route::post('/options-term-next-term', [GeneralSettingsController::class, 'getCurTermAndNextByEnrollment']);
    Route::post('/options-group-by-info', [GeneralSettingsController::class, 'getGroupByInfo']);
    Route::post('/options-prev-track-action', [GeneralSettingsController::class, 'getOptions_prev_track_action']);


    Route::post('/options-active-student', [GeneralSettingsController::class, 'getOptions_active_student']);
    Route::post('/payment-options', [GeneralSettingsController::class, 'paymentOptions']);
    Route::post('/status-options', [GeneralSettingsController::class, 'paymentStatusOptions']);
    Route::post('/deposit-options', [GeneralSettingsController::class, 'depositFormOptions']);
    Route::post('/invoice-class', [GeneralSettingsController::class, 'optionsInvoiceClass']);
    Route::post('/receipt-class', [GeneralSettingsController::class, 'optionsReceiptClass']);
    Route::post('/options-term', [GeneralSettingsController::class, 'getOptions_term']);
    Route::post('/options-year', [GeneralSettingsController::class, 'getOptions_year']);


    Route::post('/options-campus', [GeneralSettingsController::class, 'optionsCampus']);
    Route::post('/report-filter-options', [GeneralSettingsController::class, 'getReportFilter_options']);
    Route::post('/options-nationality', [GeneralSettingsController::class, 'getComboItems_nationality']);
    Route::post('/options-pmt-method', [GeneralSettingsController::class, 'getComboItems_pmt_method']);
    Route::post('/options-pmt', [GeneralSettingsController::class, 'getOptions_pmt']);

    Route::post('/options-school', [GeneralSettingsController::class, 'getOptions_school']);
    Route::get('/options-level', [GeneralSettingsController::class, 'getOptions_level']);
    Route::get('/options-program', [GeneralSettingsController::class, 'getOptions_program']);
    Route::get('/options-group-all', [GeneralSettingsController::class, 'getOptions_group']);
    Route::post('/options-group', [GeneralSettingsController::class, 'getOptions_group']);
    Route::post('/options-student-group', [GeneralSettingsController::class, 'getOptions_student_group']);
    Route::post('/options-other-fee', [GeneralSettingsController::class, 'getOptions_other_fee']);
    Route::post('/school/save', [GeneralSettingsController::class, 'saveOption_school']);
    Route::post('/school/delete', [GeneralSettingsController::class, 'deleteOption_school']);
    Route::post('/original-discount-details', [GeneralSettingsController::class, 'getStudentOriginalPriceList']);

    Route::post('/get-referral', [GeneralSettingsController::class, 'getReferralFee']);
    Route::post('/get-referral/list-paginate', [GeneralSettingsController::class, 'getReferralFeeListPaginate']);
    Route::post('/set-referral', [GeneralSettingsController::class, 'setReferralFee']);
    Route::post('/referral/form-options', [GeneralSettingsController::class, 'getReferralFeeFormOptions']);
    Route::post('/option-family-code', [GeneralSettingsController::class, 'getOptionFamilyCode']);

    Route::post('/academic-year-by-program', [GeneralSettingsController::class, 'getAcademicYearByProgram']);
    Route::post('/city-by-country', [GeneralSettingsController::class, 'getCityByCountry']);
    Route::post('/district-by-city', [GeneralSettingsController::class, 'getDistrictByCity']);
    Route::post('/commune-by-district', [GeneralSettingsController::class, 'getCommuneByDistrict']);
    Route::post('/village-by-commune', [GeneralSettingsController::class, 'getVillageByCommune']);

    Route::post('/options-department', [GeneralSettingsController::class, 'getOptions_department']);
    Route::post('/options-track-by', [GeneralSettingsController::class, 'getOptions_track_by']);
    Route::post('/options-track_shifts-by-group', [GeneralSettingsController::class, 'getOptions_track_shifts_by_group']);
    Route::post('/options-track-action-by', [GeneralSettingsController::class, 'getOptions_track_action_by']);
    Route::post('/option-by-program', [GeneralSettingsController::class, 'getOptions_by_program']);

    Route::post('/courses-by-group', [GeneralSettingsController::class, 'getOptions_courses']);
    Route::post('/groups-by-campus', [GeneralSettingsController::class, 'getOptions_groups']);
    Route::post('/cashier-by-accountant', [GeneralSettingsController::class, 'cashierByAccountant']);
    Route::post('/options-student', [GeneralSettingsController::class, 'optionsStudentList']);

    Route::post('/options-course', [GeneralSettingsController::class, 'getOptions_course']);
    Route::post('/options-class-by-level', [GeneralSettingsController::class, 'getOptionsClassByLevel']);
    Route::post('/options-class-by', [GeneralSettingsController::class, 'getOptionsClassBy']);
    Route::post('/options-get-sibling-by', [GeneralSettingsController::class, 'getOptionsSiblingBy']);
    Route::post('/options-teacher', [GeneralSettingsController::class, 'getOptions_teacher']);
    Route::post('/options-session', [GeneralSettingsController::class, 'options_session']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->group(function () {
    Route::post('calculate-fee', [PriceListController::class, 'calculateFee']);
    Route::post('tuition-by-end-date', [PriceListController::class, 'testTuitionFeeCal']);
    Route::post('invoice-by-end-date/preview', [InvoiceController::class, 'previewSplitInvoice']);
    Route::post('options/payment-method', [InvoiceController::class, 'paymentMethodOptions']);
    //api endpoint for filter options on the Find Students Component
    Route::post('/find-student/filter-options', [GeneralSettingsController::class, 'getOptions_academic_year']);
    Route::post('student-price-list/form-options', [PriceListController::class, 'setPriceListDiscount']);
    //  Route::post('report-center/report-list', [ReportController::class, 'getReportList']);
    //  Route::post('report-center/filter-options', [WebReportController::class, 'getReportFilterOptions']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('invoice-notification')->group(function () {
    Route::post('/list-paginate', [InvoiceNotificationController::class, 'invoiceNotification']);
    Route::post('/renew-invoice', [InvoiceNotificationController::class, 'CreateRenewalInvoice']);
    Route::post('/bulk-create-renew-invoice', [InvoiceNotificationController::class, 'BulkCreateRenewalInvoices']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('invoice')->group(function () {
    Route::post('/list', [InvoiceController::class, 'studentInvoice']);
    Route::post('/generate', [InvoiceController::class, 'generateInvoice']);
    //Route::post('/find',[InvoiceController::class,'findStudent']);
    Route::post('/generate-details', [InvoiceController::class, 'generateInvoiceDetails']);
    Route::post('/receive', [InvoiceController::class, 'receive']);// schoolFeePay change to receive
    Route::post('/delete', [InvoiceController::class, 'deleteInvoice']);
    Route::post('/set-active', [InvoiceController::class, 'turnInvoiceToActive']);
    Route::post('/update', [InvoiceController::class, 'updateInvoice']);
    Route::post('/get-duration', [InvoiceController::class, 'getPaymentDuration']);
    Route::post('/items', [InvoiceController::class, 'getInvoiceItems']);
    Route::post('/send-invoice', [InvoiceController::class, 'sendGeneratedInvoice']);
    Route::post('/total-details', [InvoiceController::class, 'getTotalInvoicetDetails']);
    Route::post('/external', [InvoiceController::class, 'printExternalInvoice']);
    Route::post('/generate/by-end-date', [InvoiceController::class, 'generateInvoiceByEndDate']);
    Route::post('/generate/by-end-date/details', [InvoiceController::class, 'generateInvoiceByEndDateDetails']);
    Route::post('/form-options/recive-payment', [InvoiceController::class, 'getFormOptionsReceivePayment']);
    Route::post('/re-calculate-other-fee', [InvoiceController::class, 'reCalculateOtherFee']);
    Route::post('/view-receiver', [InvoiceController::class, 'viewInvoiceReceiver']);
    Route::post('/payment-history', [InvoiceController::class, 'viewPaymentHistory']);
    Route::post('/update-prepaid-to-tuition-fee', [InvoiceController::class, 'updatePrepaidToTuitionFee']);
    Route::post('/update-paid-admin-fee', [InvoiceController::class, 'updatePartiallyPaidToAdminPaid']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('receipt')->group(function () {
    Route::post('/list', [ReceiptController::class, 'studentReceipt']);
    Route::post('/list-all', [ReceiptController::class, 'studentReceiptAll']);
    //Route::post('/find',[InvoiceController::class,'findStudent']);
    Route::post('/form-options', [ReceiptController::class, 'getFormOptions']);
    Route::post('/summary', [ReceiptController::class, 'getReceiptSummary']);
    Route::post('/cash-out', [ReceiptController::class, 'cashOut']);
    Route::post('/external', [ReceiptController::class, 'printExternalReceipt']);
    Route::post('/cancel', [ReceiptController::class, 'cancelReceipt']);
    Route::post('/options/modify-receipt', [ReceiptController::class, 'optionsModifyReceipt']);
    Route::post('/modify-receipt-breakdown', [ReceiptController::class, 'modifyReceiptBreakdown']);
    Route::post('/excel-export', [ReceiptController::class, 'exportReceiptsToExcel']);
    Route::post('/count-receipts-options', [ReceiptController::class, 'countReceiptsOptions']);
    Route::post('/withdraw', [ReceiptController::class, 'withdrawReceipt']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('cash-collection')->group(function () {
    Route::post('/list', [CashCollectionController::class, 'receiptCashCollection']);
    Route::post('/print-list', [CashCollectionController::class, 'receiptCashCollectionForPrint']);
    Route::post('/details', [CashCollectionController::class, 'details']);
    Route::post('/form-options', [CashCollectionController::class, 'getFormOptions']);
    Route::post('/clearence-receipt', [CashCollectionController::class, 'clearenceReceipt']);
    Route::post('/approve', [CashCollectionController::class, 'approve']);
    Route::post('/cancel', [CashCollectionController::class, 'cancel']);
    Route::post('/excel-export', [CashCollectionController::class, 'exportCashCollectionToExcel']);
    Route::post('/count-by-options', [CashCollectionController::class, 'countByOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('enrollment')->group(function () {
    //Route::post('/registration', [StudentController::class, 'studentRegistration']);
    Route::post('/form-options', [EnrollmentController::class, 'getFormOptions']);
    Route::post('/list-paginate', [EnrollmentController::class, 'list_paginate']);
    Route::post('/list-all', [EnrollmentController::class, 'list_all']);
    Route::post('/delete', [EnrollmentController::class, 'deleteEnrollment']);
    Route::post('/delete-specail', [EnrollmentController::class, 'deleteSpecialEnrollment']);
    Route::post('/student-info', [EnrollmentController::class, 'studentDetials']);
    Route::post('/delete-verified', [EnrollmentController::class, 'deleteVerifiedEnrollment']);
    Route::post('/save', [EnrollmentController::class, 'saveEnrollment']);
    Route::post('/save-final', [EnrollmentController::class, 'saveEnrollmentFinal']);
    Route::post('/details', [EnrollmentController::class, 'getEnrollmentDetails']);
    Route::post('/finalize', [EnrollmentController::class, 'finalizeEnrollment']);
    Route::post('/get-guardian-info', [EnrollmentController::class, 'optionsGetGuardianByFamilyCode']);
    Route::post('/family-info', [EnrollmentController::class, 'optionsGetGuardianByFamilyCode']);
    Route::post('/test', [EnrollmentController::class, 'test']);
    Route::post('/validate-parent-info', [EnrollmentController::class, 'validateParentInfo']);
    Route::post('/last', [GeneralSettingsController::class, 'getStudentLastEnrollment']);
    Route::post('/enrollment/summary', [EnrollmentController::class, 'getEnrollmentSummary']);
    Route::post('/student-statistic-by-campus', [EnrollmentController::class, 'studentStatisticByCampus']);
    Route::post('/student-statistic-by-class', [EnrollmentController::class, 'studentStatisticByClass']);
    Route::post('/exist-family', [EnrollmentController::class, 'existFamily']);
    Route::post('/fix-group-data',[EnrollmentController::class, 'fixGroupData']);


    //These may be report endpoints
    // Route::post('/dropped-out-students',[ReportController::class,'getLeaveStudent']);
    // Route::post('/leave-student',[ReportController::class,'getLeaveStudent']);
    // Route::post('/student-referrers',[ReportController::class,'getStudentReferers']);
    // // Route::post('/family-list',[ReportController::class,'getFamilyList']);
    // Route::post('/family-list',[ReportController::class,'getFamilyInfoList']);
    // Route::post('/activities',[ReportController::class,'getActivities']);
    // // Route::post('/attendance-summary',[ReportController::class,'getAttendanceSummary']);
    // Route::post('/attendance-summary',[ReportController::class,'getAttendanceList']);
    // Route::post('/student-list',[ReportController::class,'getStudentList']);
    // Route::post('/new-students',[ReportController::class,'getNewStudents']);

    // Route::post('/request-change',[ReportController::class,'studentRequestChange']);

    // //** */
    // Route::post('/student-info',[ReportController::class,'getStudentInfoList']);
    // Route::post('/family-info',[ReportController::class,'getFamilyInfoList']);
    // Route::post('/attendance/list',[ReportController::class,'getAttendanceList']);
    // //** */
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('leave')->group(function () {
    Route::post('/list-paginate', [LeaveInfoController::class, 'getList_paginate']);
    Route::post('/form-options', [LeaveInfoController::class, 'getFormOptions']);
    Route::post('/details', [LeaveInfoController::class, 'leaveDetails']);
    Route::post('/form-options/comeback', [LeaveInfoController::class, 'getOptionsComeBack']);
    Route::post('/save', [LeaveInfoController::class, 'saveLeave']);
    Route::post('/approve', [LeaveInfoController::class, 'approveLeave']);
    Route::post('/convert-to-prepayment', [LeaveInfoController::class, 'convertToPrepayment']);
    Route::post('/delete', [LeaveInfoController::class, 'deleteLeave']);
    Route::post('/remaining-info', [LeaveInfoController::class, 'getLeaveRemainingInfo']);
    Route::post('/comeback/form-options', [LeaveInfoController::class, 'getFormOptions_come_back']);
    Route::post('/comeback/save', [LeaveInfoController::class, 'saveReturn']);
    //Route::post('/save-return',[LeaveInfoController::class,'saveReturn']);
    Route::post('/comeback/finalize', [LeaveInfoController::class, 'finalizeReturn']);
    Route::post('/leave/summary', [LeaveInfoController::class, 'getLeaveSummary']);
    Route::post('/comeback-list', [LeaveInfoController::class, 'comeBackList']);
    Route::post('/get-enrollment-info', [LeaveInfoController::class, 'getEnrollmentInfo']);
    Route::post('/not-enrolled-form-options',[LeaveInfoController::class, 'notEnrolledFormOptions']);
    Route::post('/save-not-enroll',[LeaveInfoController::class, 'saveNotEnroll']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('deposit')->group(function () {
    Route::post('/save', [DepositController::class, 'save']);
    Route::post('/details', [DepositController::class, 'getDetails']);
    Route::post('/form-options', [DepositController::class, 'getFormOptions']);
    Route::post('/list-paginate', [DepositController::class, 'getList_paginate']);
    Route::post('/delete', [DepositController::class, 'delete']);
    Route::post('/student-info', [DepositController::class, 'getOldStudentInfo']);
    Route::post('/get-receipt', [DepositController::class, 'getReceipt']);
    Route::post('/authorized', [DepositController::class, 'authorized']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('campus')->group(function () {
    Route::post('/save', [CampusController::class, 'save']);
    Route::post('/list', [CampusController::class, 'getList']);
    Route::post('/list-paginate', [CampusController::class, 'getList_paginate']);
    Route::post('/details', [CampusController::class, 'getDetails']);
    Route::post('/delete', [CampusController::class, 'delete']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('academic-year')->group(function () {
    Route::post('/save', [AcademicYearController::class, 'save']);
    Route::post('/list', [AcademicYearController::class, 'getList']);
    Route::post('/list-paginate', [AcademicYearController::class, 'getList_paginate']);
    Route::post('/details', [AcademicYearController::class, 'getDetails']);
    Route::post('/delete', [AcademicYearController::class, 'delete']);
    Route::post('/form-options', [AcademicYearController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('student-group')->group(function () {
    Route::post('/save', [StudentGroupController::class, 'save']);
    Route::post('/save-groups', [StudentGroupController::class, 'saveGroups']);
    Route::post('/list', [StudentGroupController::class, 'getList']);
    Route::post('/list-paginate', [StudentGroupController::class, 'getList_paginate']);
    Route::post('/details', [StudentGroupController::class, 'getDetails']);
    Route::post('/delete', [StudentGroupController::class, 'delete']);
    Route::post('/form-options', [StudentGroupController::class, 'getFormOptions']);
    Route::post('/form-options-transfer', [StudentGroupController::class, 'getFormOptions_transfer']);
    Route::post('/assign', [StudentGroupController::class, 'assignStudentToGroup']);
    Route::post('/transfer', [StudentGroupController::class, 'transferGroupMember']);
    Route::post('/allow', [StudentGroupController::class, 'allowGroupScan']);
    Route::post('/class-list-by-group', [StudentGroupController::class, 'getClassByGroup']);
    Route::post('/print-score-list', [StudentGroupController::class, 'printScoreList']);
    Route::post('/student-list', [StudentGroupController::class, 'getStudentList']);
    Route::post('/filter-options', [StudentGroupController::class, 'getFilterOptions']);
    Route::post('/change-campus', [StudentGroupController::class, 'changeCampus']);
    Route::post('/save-movement', [StudentGroupController::class, 'saveMovement']);
    Route::post('/movement-list', [StudentGroupController::class, 'getMovementList']);
    Route::post('/movement-details', [StudentGroupController::class, 'getMovementDetails']);
    Route::post('/delete-movement', [StudentGroupController::class, 'deleteMovement']);
    Route::post('/moment-form-options', [StudentGroupController::class, 'getMovementFormOptions']);
    Route::post('/get-group-list', [StudentGroupController::class, 'getGroupList']);
    Route::post('/exist-preparation', [StudentGroupController::class, 'checkExistPreparation']);
    Route::post('/movement-item-list', [StudentGroupController::class, 'getMovementItemList']);
    Route::post('/correct-capus-transfer-old', [StudentGroupController::class, 'correctCampusTransferOld']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('term')->group(function () {
    Route::post('/save', [TermController::class, 'save']);
    Route::post('/list', [TermController::class, 'getList']);
    Route::post('/list-paginate', [TermController::class, 'getList_paginate']);
    Route::post('/details', [TermController::class, 'getDetails']);
    Route::post('/delete', [TermController::class, 'delete']);
    Route::post('/set-status', [TermController::class, 'setTermStatus']);
    Route::post('/start', [TermController::class, 'startTerm']);
    Route::post('/start-all', [TermController::class, 'startTermAll']);
    Route::post('/prepare', [TermController::class, 'prepareTerm']);
    Route::post('/prepare-all', [TermController::class, 'prepareTermAll']);
    Route::post('/finish', [TermController::class, 'setTermFinish']);
    Route::post('/reverse', [TermController::class, 'reverse']);
    //term/form-options
    Route::post('/form-options', [TermController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('program')->group(function () {
    Route::post('/save', [ProgramController::class, 'save']);
    Route::post('/list', [ProgramController::class, 'getList']);
    Route::post('/list-paginate', [ProgramController::class, 'getList_paginate']);
    Route::post('/details', [ProgramController::class, 'getDetails']);
    Route::post('/form-options', [ProgramController::class, 'getFormOptions']);
    Route::post('/reset-order-all', [ProgramController::class, 'resetOrderAll']);
    Route::post('/reset-order', [ProgramController::class, 'resetOrder']);
    Route::post('/delete', [ProgramController::class, 'delete']);
    Route::post('/levels', [ProgramController::class, 'get_levels_by_program']);
    Route::post('/sub-filters', [ProgramController::class, 'getSubFilters']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('program-level')->group(function () {
    Route::post('/save', [ProgramLevelController::class, 'save']);
    Route::post('/list', [ProgramLevelController::class, 'getList']);
    Route::post('/details', [ProgramLevelController::class, 'getDetails']);
    Route::post('/form-options', [ProgramLevelController::class, 'getFormOptions']);
    Route::post('/delete', [ProgramLevelController::class, 'delete']);
    Route::post('/reset-order-all', [ProgramLevelController::class, 'resetOrderAll']);
    Route::post('/reset-order', [ProgramLevelController::class, 'resetOrder']);
});

Route::middleware((['auth.api', CustomRateLimiter::class]))->prefix('dashboard')->group(function () {
    Route::post('/summaries', [DashboardController::class, 'summarizeEnrollments']);
    Route::post('/summaries-new', [DashboardController::class, 'summarizeEnrollmentsNew']);
    Route::post('/filter-options', [DashboardController::class, 'getFilterOptions']);
    Route::post('/overview', [DashboardController::class, 'overview']);
    Route::post('/financial', [DashboardController::class, 'financial']);
    Route::post('/enrollment', [DashboardController::class, 'enrollment']);
    Route::post('/academic', [DashboardController::class, 'academic']);
    Route::post('/collection-health', [DashboardController::class, 'collectionHealth']);

});


Route::middleware((['auth.api', CustomRateLimiter::class]))->prefix('guardian')->group(function () {
    Route::post('/find', [GuardianController::class, 'findParents']);
    Route::post('/save', [GuardianController::class, 'save']);
    Route::post('/list', [GuardianController::class, 'list']);
    Route::post('/delete', [GuardianController::class, 'delete']);
    Route::post('/details', [GuardianController::class, 'getDetails']);
    Route::post('/form-options', [GuardianController::class, 'getFormOptions']);
    Route::post('/request-account', [GuardianController::class, 'parentRequestAccount']);
    Route::post('/children', [GuardianController::class, 'getChildren']);
    Route::post('/options-family', [GuardianController::class, 'optionFamily']);
    Route::post('/swap-login', [GuardianController::class, 'setLoginParent']);
    Route::post('/family-members', [GuardianController::class, 'getFamilyMembers']);
    Route::post('/set-as-mobile-login', [GuardianController::class, 'setAsMobileLogin']);
    Route::post('/update-family-code', [GuardianController::class, 'updateFamilyCode']);
});

Route::middleware((['auth.api', CustomRateLimiter::class]))->prefix('currency')->group(function () {
    Route::post('details', [CurrencyController::class, 'getCurrencyDetails']);
    Route::post('save', [CurrencyController::class, 'saveCurrency']);
    Route::post('delete', [CurrencyController::class, 'deleteCurrency']);
    Route::post('list', [CurrencyController::class, 'getCurrencies']);
    Route::post('history', [CurrencyController::class, 'getExchangeRateInfo']);
});


Route::middleware((['auth.api', CustomRateLimiter::class]))->prefix('x-rate')->group(function () {
    Route::post('options-month', [ExchangeRateController::class, 'getComboItems_x_month']);
    Route::post('create-currency-pair', [ExchangeRateController::class, 'createCurrencyPair']);
    Route::post('delete-currency-pair', [ExchangeRateController::class, 'deleteCurrencyPair']);
    Route::post('currency-pair-list', [ExchangeRateController::class, 'getCurrencyPairs']);
    Route::post('currency-pairs', [ExchangeRateController::class, 'getCurrencyPairs']);
    Route::post('info', [ExchangeRateController::class, 'getExchangeRateInfo']);
    Route::post('delete', [ExchangeRateController::class, 'deleteExchangeRate']);
    Route::post('list', [ExchangeRateController::class, 'getExchangeRates']);
    Route::post('save', [CurrencyController::class, 'saveExchangeRate']);
    Route::post('apply', [ExchangeRateController::class, 'applyExchangeRate']);
    Route::post('apply-rate', [ExchangeRateController::class, 'applyExchangeRate']);
});

Route::middleware((['auth.api', CustomRateLimiter::class]))->prefix('other-fee')->group(function () {
    Route::post('/form-options', [OtherFeeController::class, 'getFormOptions']);
    Route::post('/list', [OtherFeeController::class, 'getList']);
    Route::post('/list-paginate', [OtherFeeController::class, 'getList_paginate']);
    Route::post('/delete', [OtherFeeController::class, 'deleteOtherFee']);
    Route::post('/save', [OtherFeeController::class, 'saveOtherFee']);
    Route::post('/details', [OtherFeeController::class, 'getDetails']);
    Route::post('/options-fee-type', [OtherFeeController::class, 'optionsFeeType']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('reports')->group(function () {
    Route::post('/list', [ReportController::class, 'getReportList']);
    Route::post('/filter-options', [ReportController::class, 'getReportFilterOptions']);
    Route::post('/options-receiver', [ReportController::class, 'getReceivers']);

    Route::post('finance/activities', [ReportController::class, 'getActivities']);
    Route::post('finance/payments', [ReportController::class, 'getInvoicePayments']);
    Route::post('finance/invoice-list', [ReportController::class, 'getInvoiceList']);
    Route::post('finance/invoice-paid', [ReportController::class, 'getPaidInvoices']);
    Route::post('finance/expired-students', [ReportController::class, 'getExpiredStudents']);
    Route::post('finance/students-with-sepcial-discount', [ReportController::class, 'getStudentsWithSpecialDiscount']);
    Route::post('finance/student-counts-by-pmt-option', [ReportController::class, 'countStudentsByPmtOptions']);
    Route::post('daily-cash', [ReportController::class, 'getDailyCashList']);
    Route::post('monthly-cash', [ReportController::class, 'getMonthlyCashList']);
    Route::post('referral', [ReportController::class, 'getReferalFeeList']);
    Route::post('finance/school-fee-list', [ReportController::class, 'getSchoolFee']);
    Route::post('finance/non-tuition-fee-list', [ReportController::class, 'getNonTuitionFeeList']);
    Route::post('income_by_category', [ReportController::class, 'getIncomeByCategories']);
    Route::post('income_by_class', [ReportController::class, 'getIncomeByClass']);
    Route::post('finance/deposite-list', [ReportController::class, 'getStudentDepositeList']);
    Route::post('finance/total-by-month', [ReportController::class, 'getTotalPaymentByMonth']);
    Route::post('finance/total-by-year', [ReportController::class, 'getTotalPaymentByYear']);
    Route::post('finance/total-payment-history-year', [ReportController::class, 'getTotalPaymentHistoryByYear']);
    Route::post('finance/total-student-payment-history', [ReportController::class, 'getTotalStudentPaymentHistory']);
    Route::post('finance/student-payment-history', [ReportController::class, 'getStudentPaymentHistory']);
    Route::post('finance/cross-year-payment', [ReportController::class, 'crossYearReceipt']);
    Route::post('finance/upgrade-fee', [ReportController::class, 'upgradeFee']);
    Route::post('ar_list',[ReportController::class,'getAccountReceivable']);
    Route::post('student_payments',[ReportController::class,'getStudentPayments']);
    Route::post('promoted_students',[ReportController::class,'getPromotedStudents']);

    Route::post('enrollment/dropped-out-students', [ReportController::class, 'getLeaveStudent']);
    Route::post('enrollment/comeback-students', [ReportController::class, 'getComeBackStudents']);
    Route::post('enrollment/leave-student', [ReportController::class, 'getLeaveStudent']);
    Route::post('enrollment/student-referrers', [ReportController::class, 'getStudentReferers']);
    // Route::post('enrollment/family-list',[ReportController::class,'getFamilyList']);
    Route::post('enrollment/family-list', [ReportController::class, 'getFamilyInfoList']);
    Route::post('enrollment/activities', [ReportController::class, 'getActivities']);
    // Route::post('/attendance-summary',[ReportController::class,'getAttendanceSummary']);
    Route::post('enrollment/attendance-summary', [ReportController::class, 'getAttendanceList']);
    Route::post('enrollment/student-list', [ReportController::class, 'getStudentList']);
    Route::post('enrollment/new-students', [ReportController::class, 'getNewStudents']);

    Route::post('enrollment/request-change', [ReportController::class, 'studentRequestChange']);

    //** */
    Route::post('enrollment/student-info', [ReportController::class, 'getStudentInfoList']);
    Route::post('enrollment/family-info', [ReportController::class, 'getFamilyInfoList']);
    Route::post('enrollment/attendance/list', [ReportController::class, 'getAttendanceList']);
    //** */
    Route::post('student-statistic-by-campus', [ReportController::class, 'studentStatisticByCampus']);
    Route::post('student-statistic-by-class', [ReportController::class, 'studentStatisticByClass']);
    Route::post('score_list', [ReportController::class, 'getScoreList']);
    Route::post('student_profile', [ReportController::class, 'getStudentProfile']);
    Route::post('student_contact_list', [ReportController::class, 'getStudentContactList']);
    Route::post('student_absent_record_by_name', [ReportController::class, 'getStudentAbsentRecordByName']);
    Route::post('student_absent_record_by_grade', [ReportController::class, 'getStudentAbsentRecordByGrade']);
    Route::post('class_attendence_list', [ReportController::class, 'getClassAttendenceList']);
    Route::post('print_attendence_list', [ReportController::class, 'printClassAttendenceList']);
    Route::post('student_contact_list', [ReportController::class, 'studentContactList']);

    Route::post('consolidated_collection_report', [ReportController::class, 'getConsultantCollection']);
    Route::post('each_campus_collection_report', [ReportController::class, 'getEachCampusCollection']);
    Route::post('enrollment_count_by_academic_year', [ReportController::class, 'getEnrollmentCountByAcademicYear']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('mobile-settings')->group(function () {
    Route::post('/options-mobile-app', [GeneralSettingsController::class, 'options_mobile_app']);
    Route::post('/privacy-content', [MobileAppSettingsController::class, 'getPrivacyContent']);
    Route::post('/save-privacy-content', [MobileAppSettingsController::class, 'savePrivacyContent']);
    Route::post('/save-terms-and-conditions', [MobileAppSettingsController::class, 'saveTermsAndConditions']);
});

//  //begin::MobileSettingsController
//  Route::middleware(['auth.api',CustomRateLimiter::class])->prefix('mobile-settings')->group(function(){
//     Route::post('/banner-save',[BannerController::class,'save']);
//     Route::post('/banner-list',[BannerController::class,'list']);
//     Route::post('/banner-details',[BannerController::class,'details']);
//     Route::post('/banner-delete',[BannerController::class,'delete']);

//     // Social Media
//     Route::post('/social-media-save',[SocialMediaController::class,'save']);
//     Route::post('/social-media-list',[SocialMediaController::class,'list']);
//     Route::post('/social-media-details',[SocialMediaController::class,'details']);
//     Route::post('/social-media-delete',[SocialMediaController::class,'delete']);

//     // Book And Material
//     Route::post('/book-material/save',[BookAndMaterialController::class,'save']);
//     Route::post('/book-material/list',[BookAndMaterialController::class,'list']);
//     Route::post('/book-material/details',[BookAndMaterialController::class,'details']);
//     Route::post('/book-material/remove-img',[BookAndMaterialController::class,'removeImage']);
//     Route::post('/book-material/delete',[BookAndMaterialController::class,'delete']);
// });
// //end::MobileSettingsController



//begin::SiblingController
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('sibling-discount')->group(function () {
    Route::post('/save', [SiblingDiscountController::class, 'save']);
    Route::post('/list-paginate', [SiblingDiscountController::class, 'listPaginate']);
    Route::post('/details', [SiblingDiscountController::class, 'details']);
    Route::post('/delete', [SiblingDiscountController::class, 'delete']);
    Route::post('/form-options', [SiblingDiscountController::class, 'getFormOptions']);
    Route::post('/get-sibling', [SiblingDiscountController::class, 'getSibling']);


});
//end::SiblingController

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('tuition-review')->group(function () {
    Route::post('/form-options', [PriceListController::class, 'getPreviewPaymentOptions']);
    //payment review
    Route::post('/list-paginate', [PriceListController::class, 'getTuitionReviewList']);
    Route::post('/b/list-paginate', [WisPriceListController::class, 'getTuitionReviewList']);
    //Return discount information for Old student only
    Route::post('/student-discount', [PriceListController::class, 'getStudentDiscountInfo']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('price-list')->group(function () {
    Route::post('/select-options', [GeneralSettingsController::class, 'getOptions_price_list']);
    Route::post('/options-year', [PriceListController::class, 'getOptions_year']);
    Route::post('/options', [GeneralSettingsController::class, 'getOptions_price_list']);
    Route::post('/list-paginate', [PriceListController::class, 'getPriceList_paginate']);
    Route::post('/delete', [PriceListController::class, 'deletePriceList']);
    Route::post('/save', [PriceListController::class, 'savePriceList']);
    Route::post('/details', [PriceListController::class, 'getPriceListDetails']);
    Route::post('/items', [PriceListController::class, 'getPriceListItems']);
    Route::post('/save-item', [PriceListController::class, 'saveItem']);
    Route::post('/delete-item', [PriceListController::class, 'deleteItem']);
    Route::post('/item-details', [PriceListController::class, 'getPriceListItemDetails']);
    Route::post('/item-options', [PriceListController::class, 'getItemFormOptions']);
    Route::post('/price-options', [PriceListController::class, 'getPriceFormOptions']);

    Route::post('/preview/pending-payment', [PriceListController::class, 'previewPendingPaymentDetails']);
    Route::post('/update/pending-payment', [PriceListController::class, 'updatePendingPayment']);
    Route::post('/pending-payment/details', [PriceListController::class, 'getStudentPendingPaymentDetails']);

    Route::post('/weekly', [PriceListController::class, 'weeklyFee']);

    Route::post('/monthly', [PriceListController::class, 'monthlyFee']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('price-list-b')->group(function () {
    Route::post('/select-options', [GeneralSettingsController::class, 'getOptions_price_list']);
    Route::post('/options-year', [WisPriceListController::class, 'getOptions_year']);
    Route::post('/options', [GeneralSettingsController::class, 'getOptions_price_list']);
    Route::post('/list-paginate', [WisPriceListController::class, 'getPriceList_paginate']);
    Route::post('/delete', [WisPriceListController::class, 'deletePriceList']);
    Route::post('/save', [WisPriceListController::class, 'savePriceList']);
    Route::post('/details', [WisPriceListController::class, 'getPriceListDetails']);
    Route::post('/items', [WisPriceListController::class, 'getPriceListItems']);
    Route::post('/save-item', [WisPriceListController::class, 'saveItem']);
    Route::post('/delete-item', [WisPriceListController::class, 'deleteItem']);
    Route::post('/item-details', [WisPriceListController::class, 'getPriceListItemDetails']);
    Route::post('/item-options', [WisPriceListController::class, 'getItemFormOptions']);
    Route::post('/price-options', [WisPriceListController::class, 'getPriceFormOptions']);

    Route::post('/preview/pending-payment', [WisPriceListController::class, 'previewPendingPaymentDetails']);
    Route::post('/update/pending-payment', [WisPriceListController::class, 'updatePendingPayment']);
    Route::post('/pending-payment/details', [WisPriceListController::class, 'getStudentPendingPaymentDetails']);

    Route::post('/weekly', [WisPriceListController::class, 'weeklyFee']);

    Route::post('/monthly', [WisPriceListController::class, 'monthlyFee']);

    Route::post('/student-discount', [WisPriceListController::class, 'getStudentDiscountInfo']);
    Route::post('/pmt-options-by-level', [WisPriceListController::class, 'getPaymentOptionsByLevel']);
    Route::post('/pmt-options-by-program', [WisPriceListController::class, 'getPaymentOptionsByProgram']);

});

//begin::MobileAppSettingController
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('mobile-settings')->group(function () {
    Route::post('/brand-images', [MobileAppSettingsController::class, 'getBrandImages']);

    Route::post('/save-brand-image', [MobileAppSettingsController::class, 'saveBrandImage']);
    Route::post('/delete-brand-image', [MobileAppSettingsController::class, 'deleteBrandImage']);
    Route::post('/connect-with-us', [CompanyProfileController::class, 'getConnectWithUsInfo']);
    Route::post('/terms-and-conditions', [MobileAppSettingsController::class, 'getTermsAndConditions']);
    Route::post('/save-terms-and-conditions', [MobileAppSettingsController::class, 'saveTermsAndConditions']);
    Route::post('/privacy-content', [MobileAppSettingsController::class, 'getPrivacyContent']);
    Route::post('/save-privacy-content', [MobileAppSettingsController::class, 'savePrivacyContent']);
});
//end::MobileAppsettingsController

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('early-promotion')->group(function () {
    Route::post('/list-paginate', [EarlyPromotionController::class, 'getDiscountList_paginate']);
    Route::post('/delete', [EarlyPromotionController::class, 'deleteDiscount']);
    Route::post('/save', [EarlyPromotionController::class, 'saveDiscount']);
    Route::post('/details', [EarlyPromotionController::class, 'getDetails']);
    Route::post('/form-options', [EarlyPromotionController::class, 'getFormOptions']);
    Route::post('/get-value', [EarlyPromotionController::class, 'getEarlyPromotion']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('dis-by-month')->group(function () {
    Route::post('/save', [DisCountByMonthController::class, 'save']);
    Route::post('/list-paginate', [DisCountByMonthController::class, 'list_paginate']);
    Route::post('/details', [DisCountByMonthController::class, 'getDetails']);
    Route::post('/form-options', [DisCountByMonthController::class, 'getFormOptions']);
    Route::post('/delete', [DisCountByMonthController::class, 'delete']);
    Route::post('/get-value', [DisCountByMonthController::class, 'getDisCountByMonth']);
});


Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('company')->group(function () {
    //begin::CompanyProfileController
    Route::post('/save-logo', [CompanyProfileController::class, 'saveCompanyLogo']);
    Route::post('/logo-url', [CompanyProfileController::class, 'getCompanyLogo']);
    Route::post('/delete-logo', [CompanyProfileController::class, 'deleteCompanyLogo']);
    Route::post('/save-details', [CompanyProfileController::class, 'saveCompanyInfo']);
    Route::post('/details', [CompanyProfileController::class, 'getCompanyInfo']);
    Route::post('/info', [CompanyProfileController::class, 'getCompanyInfo']);
    //end::CompanyProfileController
});

Route::post('getComboItems_price_list', [SystemSettingController::class, 'getComboItems_price_list']);


//begin:: PromotionController
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('promotion')->group(function () {
    Route::post('/list', [PromotionController::class, 'getPromotionList']);
    Route::post('/save', [PromotionController::class, 'savePromotion']);
    Route::post('/details', [PromotionController::class, 'getPromotionInfo']);
    Route::post('delete', [PromotionController::class, 'deletePromotion']);
    Route::post('/options-mobile-app', [PromotionController::class, 'options_mobile_app']);
    Route::post('/form-options', [PromotionController::class, 'getFormOptions']);
});
//end::PromotionController


Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('item')->group(function () {
    Route::post('/list', [MaterialItemController::class, 'getItemList']);
    Route::post('/save', [MaterialItemController::class, 'saveItem']);
    //Route::post('/details', [MaterialItemController::class, 'getDetails']);
    Route::post('delete', [MaterialItemController::class, 'deleteItem']);
    Route::post('/options-category', [MaterialItemController::class, 'options_category']);
    Route::post('/form-options', [MaterialItemController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('announcement')->group(function () {
    Route::post('/list', [AnnouncementController::class, 'getList']);
    Route::post('/save', [AnnouncementController::class, 'save']);
    //Route::post('/details', [AnnouncementController::class, 'getDetails']);
    Route::post('delete', [AnnouncementController::class, 'delete']);
    Route::post('/options-category', [AnnouncementController::class, 'options_category']);
    Route::post('/form-options', [AnnouncementController::class, 'getFormOptions']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('courses')->group(function () {
    Route::post('/list', [CourseController::class, 'getList']);
    Route::post('/list-all', [CourseController::class, 'courseLists']);
    Route::post('/list-by-level', [CourseController::class, 'courseListByLevel']);
    Route::post('/save', [CourseController::class, 'save']);
    Route::post('delete', [CourseController::class, 'delete']);
    Route::post('/details', [CourseController::class, 'getDetails']);
    Route::post('/form-options', [CourseController::class, 'getFormOptions']);
    Route::post('/template-save', [CourseController::class, 'templateSave']);
    Route::post('/category-save', [CourseController::class, 'saveCategory']);
    Route::post('/category-delete', [CourseController::class, 'deleteCategory']);
    Route::post('/category-item-save', [CourseController::class, 'saveCategoryItem']);
    Route::post('/category-item-delete', [CourseController::class, 'deleteCategoryItem']);
    Route::post('/template-form-options', [CourseController::class, 'getTemplateFormOptions']);
    Route::post('/copy-template-from', [CourseController::class, 'copyTemplateFrom']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('course_type')->group(function () {
    Route::post('/list', [CourseTypeController::class, 'getList']);
    Route::post('/save', [CourseTypeController::class, 'save']);
    Route::post('delete', [CourseTypeController::class, 'delete']);
    Route::post('/details', [CourseTypeController::class, 'getDetails']);
    Route::post('/form-options', [CourseTypeController::class, 'getFormOptions']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('subject')->group(function () {
    Route::post('/list', [SubjectController::class, 'getList']);
    Route::post('/save', [SubjectController::class, 'save']);
    Route::post('delete', [SubjectController::class, 'delete']);
    Route::post('/details', [SubjectController::class, 'getDetails']);
    Route::post('/form-options', [SubjectController::class, 'getFormOptions']);
    Route::post('/list-all', [SubjectController::class, 'subjectLists']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('day')->group(function () {
    Route::post('/list', [ScheduleDayController::class, 'getList']);
    Route::post('/save', [ScheduleDayController::class, 'save']);
    Route::post('delete', [ScheduleDayController::class, 'delete']);
    Route::post('/details', [ScheduleDayController::class, 'getDetails']);
    Route::post('/form-options', [ScheduleDayController::class, 'getFormOptions']);
    Route::post('/list-all', [ScheduleDayController::class, 'dayDayLists']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('teacher')->group(function () {
    Route::post('/list', [TeacherController::class, 'getList']);
    Route::post('/save', [TeacherController::class, 'save']);
    Route::post('delete', [TeacherController::class, 'delete']);
    Route::post('/details', [TeacherController::class, 'getDetails']);
    Route::post('/form-options', [TeacherController::class, 'getFormOptions']);
    Route::post('/list-all', [TeacherController::class, 'teacherLists']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('room')->group(function () {
    Route::post('/list', [RoomController::class, 'getList']);
    Route::post('/save', [RoomController::class, 'save']);
    Route::post('delete', [RoomController::class, 'delete']);
    Route::post('/details', [RoomController::class, 'getDetails']);
    Route::post('/form-options', [RoomController::class, 'getFormOptions']);
    Route::post('/update-status', [RoomController::class, 'updateStatus']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('room-type')->group(function () {
    Route::post('/list', [RoomTypeController::class, 'getList']);
    Route::post('/save', [RoomTypeController::class, 'save']);
    Route::post('delete', [RoomTypeController::class, 'delete']);
    Route::post('/details', [RoomTypeController::class, 'getDetails']);
    Route::post('/form-options', [RoomTypeController::class, 'getFormOptions']);
    Route::post('/list-all', [RoomTypeController::class, 'RoomTypeLists']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('building')->group(function () {
    Route::post('/list', [BuildingController::class, 'index']);
    Route::post('/save', [BuildingController::class, 'addBuilding']);
    Route::post('/details', [BuildingController::class, 'getDetails']);
    Route::post('/delete', [BuildingController::class, 'delete']);
    Route::post('/form-options', [BuildingController::class, 'getFormOptions']);

});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('schedule')->group(function () {
    Route::post('/list', [ClassScheduleController::class, 'getList']);
    Route::post('/save', [ClassScheduleController::class, 'save']);
    Route::post('delete', [ClassScheduleController::class, 'delete']);
    Route::post('/details', [ClassScheduleController::class, 'getDetails']);
    Route::post('/form-options', [ClassScheduleController::class, 'getFormOptions']);
    Route::post('/render-schedule', [ClassScheduleController::class, 'renderSchedule']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('daily-schedule')->group(function () {
    Route::post('/list', [DailyScheduleController::class, 'getList']);
    Route::post('/save', [DailyScheduleController::class, 'save']);
    Route::post('delete', [DailyScheduleController::class, 'delete']);
    Route::post('/details', [DailyScheduleController::class, 'getDetails']);
    Route::post('/form-options', [DailyScheduleController::class, 'getFormOptions']);
    Route::post('/list-all', [DailyScheduleController::class, 'dailyScheduleLists']);
    Route::post('/change-class-session', [DailyScheduleController::class, 'changeClassSession']);
    Route::post('/cancel', [DailyScheduleController::class, 'cancel']);
    Route::post('/make-up-class', [DailyScheduleController::class, 'makeUpClass']);
    Route::post('/substitute-teacher', [DailyScheduleController::class, 'substituteTeacher']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('classes')->group(function () {
    Route::post('/save', [ClassController::class, 'save']);
    Route::post('/list', [ClassController::class, 'getList']);
    Route::post('/delete', [ClassController::class, 'delete']);
    Route::post('/details', [ClassController::class, 'getDetails']);
    Route::post('/form-options', [ClassController::class, 'getFormOptions']);
    Route::post('/assign-group', [ClassController::class, 'assignGroup']);
    Route::post('/student-list-by-class', [ClassController::class, 'getStudentByClass']);
});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('grade-sheet')->group(function () {
    Route::post('/save', [GradeSheetController::class, 'save']);
    Route::post('/save-score', [GradeSheetController::class, 'saveClassScore']);
    Route::post('/save-comment', [GradeSheetController::class, 'saveClassComment']);
    Route::post('/apply-change', [GradeSheetController::class, 'applyChange']);
    Route::post('/apply-template', [GradeSheetController::class, 'applyTemplate']);
    Route::post('/list', [GradeSheetController::class, 'getList']);
    Route::post('/delete-col', [GradeSheetController::class, 'deleteCol']);
    Route::post('/delete-score', [GradeSheetController::class, 'deleteScore']);
    Route::post('/details', [GradeSheetController::class, 'getDetails']);
    Route::post('/col-form-options', [GradeSheetController::class, 'getColFormOptions']);
    Route::post('/lock-all-period', [GradeSheetController::class, 'lockAllPeriod']);
    Route::post('/lock-period', [GradeSheetController::class, 'lockPeriod']);
    Route::post('/unlock-period', [GradeSheetController::class, 'unlockPeriod']);
    Route::post('/calculate-all-class-by-group', [GradeSheetController::class, 'calculateAllClassByGroup']);
    Route::post('/calculate-all-period', [GradeSheetController::class, 'calculateAllPeriod']);
    Route::post('/calculate', [GradeSheetController::class, 'calculate']);
    Route::post('/form-options', [GradeSheetController::class, 'getFormOptions']);
    Route::post('/end-of-year-report', [GradeSheetController::class, 'getEndOfYearReport']);
    Route::post('/report-by-period', [GradeSheetController::class, 'getReportByPeriod']);
    Route::post('/year-result-list', [GradeSheetController::class, 'getYearResultList']);
    Route::post('/options-period', [GradeSheetController::class, 'getOptionsPeriod']);

});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('holiday')->group(function () {
    Route::post('/save', [HolidayController::class, 'saveHoliday']);
    Route::post('/list-paginate', [HolidayController::class, 'getHolidayListPaginate']);
    Route::post('/details', [HolidayController::class, 'getDetails']);
    Route::post('/delete', [HolidayController::class, 'deleteHoliday']);
    Route::post('/form-options', [HolidayController::class, 'getFormOptions']);
    Route::post('/list', [HolidayController::class, 'getHolidayList']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('curricula')->group(function () {
    Route::post('/list', [CurriculaController::class, 'getList']);
    Route::post('/save', [CurriculaController::class, 'save']);
    Route::post('/details', [CurriculaController::class, 'getDetails']);
    Route::post('/delete', [CurriculaController::class, 'delete']);
    Route::post('/form-options', [CurriculaController::class, 'getFormOptions']);
    Route::post('/courses', [CurriculaController::class, 'getCourses']);
    Route::post('/set-current-status', [CurriculaController::class, 'setCurrentStatus']);

});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('attendance-tracks')->group(function () {
    Route::post('/save', [AttendanceTrackController::class, 'save']);
    Route::post('/list', [AttendanceTrackController::class, 'getList']);
    Route::post('/delete', [AttendanceTrackController::class, 'delete']);
    Route::post('/details', [AttendanceTrackController::class, 'getDetails']);
    Route::post('/form-options', [AttendanceTrackController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('track_shifts')->group(function () {
    Route::post('/save', [TrackShiftController::class, 'save']);
    Route::post('/list', [TrackShiftController::class, 'getList']);
    Route::post('/delete', [TrackShiftController::class, 'delete']);
    Route::post('/details', [TrackShiftController::class, 'getDetails']);
    Route::post('/form-options', [TrackShiftController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('track-actions')->group(function () {
    Route::post('/save', [TrackActionController::class, 'save']);
    Route::post('/list', [TrackActionController::class, 'getList']);
    Route::post('/delete', [TrackActionController::class, 'delete']);
    Route::post('/details', [TrackActionController::class, 'getDetails']);
    Route::post('/form-options', [TrackActionController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('tu-course')->group(function () {
    Route::post('/list', [TutorCoursesController::class, 'getList']);
    Route::post('/save', [TutorCoursesController::class, 'save']);
    Route::post('delete', [TutorCoursesController::class, 'delete']);
    Route::post('/details', [TutorCoursesController::class, 'getDetails']);
    Route::post('/form-options', [TutorCoursesController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('scholarship')->group(function () {
    Route::post('/save', [ScholarshipController::class, 'save']);
    Route::post('/list', [ScholarshipController::class, 'getList']);
    Route::post('/delete', [ScholarshipController::class, 'delete']);
    Route::post('/details', [ScholarshipController::class, 'getDetails']);
    Route::post('/form-options', [ScholarshipController::class, 'getFormOptions']);
    Route::post('/authorized', [ScholarshipController::class, 'authorizeScholarship']);
    Route::post('/date-range', [ScholarshipController::class, 'getScholarshipDateRange']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('student-permission')->group(function () {
    Route::post('/save', [StudentPermissionController::class, 'save']);
    Route::post('/list', [StudentPermissionController::class, 'getList']);
    Route::post('/delete', [StudentPermissionController::class, 'delete']);
    Route::post('/details', [StudentPermissionController::class, 'getDetails']);
    Route::post('/form-options', [StudentPermissionController::class, 'getFormOptions']);
    Route::post('/list-details', [StudentPermissionController::class, 'getListDetails']);
    Route::post('/authorized', [StudentPermissionController::class, 'authorized']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('review')->group(function () {
    Route::post('/list', [ReviewController::class, 'getList']);
    Route::post('/save', [ReviewController::class, 'save']);
    //Route::post('/details', [AnnouncementController::class, 'getDetails']);
    Route::post('delete', [ReviewController::class, 'delete']);
    Route::post('/options-category', [ReviewController::class, 'options_category']);
    Route::post('/form-options', [ReviewController::class, 'getFormOptions']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('cash-account')->group(function () {
    Route::post('/save', [CashAccountController::class, 'saveAccount']);
    Route::post('list-paginate', [CashAccountController::class, 'getStudentAccountList']);
    Route::post('/view_transaction', [CashAccountController::class, 'viewTransaction']);
    Route::post('/details', [CashAccountController::class, 'getDetails']);
    Route::post('/form-options', [CashAccountController::class, 'getFormOptions']);
    Route::post('/transfer', [CashAccountController::class, 'transfer']);
    Route::post('/transfer-remarks', [CashAccountController::class, 'transferRemarks']);
    Route::post('/withdraw', [CashAccountController::class, 'withdraw']);
    Route::post('/receive-ar-amount', [CashAccountController::class, 'receiveArAmount']);
    Route::post('/edit-remarks', [CashAccountController::class, 'editRemarks']);
    Route::post('/deposit', [CashAccountController::class, 'deposit']);

});
Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('account-transaction')->group(function () {
    Route::post('/list-paginate', [AccountTransactionController::class, 'getListPaginate']);
    Route::post('/testing-fee/list', [AccountTransactionController::class, 'getListTestingFee']);
    Route::post('/non-tuition/list', [AccountTransactionController::class, 'getListNonTuitionFee']);
    Route::post('/tuition/list', [AccountTransactionController::class, 'getListTuitionFee']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('occupation')->group(function () {
    Route::post('/save', [OccupationController::class, 'save']);
    Route::post('/list-paginate', [OccupationController::class, 'getList_paginate']);
    Route::post('/details', [OccupationController::class, 'getDetails']);
    Route::post('/form-options', [OccupationController::class, 'getFormOptions']);
    Route::post('/delete', [OccupationController::class, 'delete']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('teacher-comment')->group(function () {
    Route::post('/save', [TeacherCommentController::class, 'save']);
    Route::post('/save-item', [TeacherCommentController::class, 'saveItem']);
    Route::post('/list', [TeacherCommentController::class, 'getList']);
    Route::post('/details', [TeacherCommentController::class, 'getDetails']);
    Route::post('/form-options', [TeacherCommentController::class, 'getFormOptions']);
    Route::post('/delete', [TeacherCommentController::class, 'delete']);
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('class-attendance')->group(function () {
    Route::post('/save', [ClassAttendanceController::class, 'save']);
    Route::post('/list-paginate', [ClassAttendanceController::class, 'getList_paginate']);
    Route::post('/details', [ClassAttendanceController::class, 'getDetails']);
    Route::post('/form-options', [ClassAttendanceController::class, 'getFormOptions']);
    Route::post('/save-all-present', [ClassAttendanceController::class, 'saveAllPresent']);
});

Route::middleware('auth.api')->get('/signal-ticket', function (Request $request) {
    $appId = getAppIdByUserClass($request->user->user_class);
    $secret = config('signal.signal_secret');
    $timestamp = (string) time();
    $projectId = config('signal.signal_project_id');
    $userId = (string) $request->user->id;

    $signature = hash_hmac('sha256', "{$appId}.{$timestamp}.{$projectId}.{$userId}", $secret);

    return response()->json(compact('appId', 'timestamp', 'projectId', 'userId', 'signature'));
});

Route::get('/scan-attendance-signal-ticket', function (Request $request) {
    \Log::info('Signal Ticket Request: ' . json_encode($request->all()));
    $appId = '8AE496F4C88EB47721B5B202EBDBC546';// getAppIdByUserClass($request->user->user_class);
    $secret = config('signal.signal_secret');
    $timestamp = (string) time();
    $projectId = config('signal.signal_project_id');
    $userId = '1';

    $signature = hash_hmac('sha256', "{$appId}.{$timestamp}.{$projectId}.{$userId}", $secret);

    return response()->json(compact('appId', 'timestamp', 'projectId', 'userId', 'signature'));
});

Route::middleware(['auth.api', CustomRateLimiter::class])->prefix('graduated')->group(function () {
    Route::post('/list-paginate', [GraduatedController::class, 'getList_paginate']);
    Route::post('/details', [GraduatedController::class, 'getDetails']);
    Route::post('/form-options', [GraduatedController::class, 'getFormOptions']);
    Route::post('/re-enroll', [GraduatedController::class, 'reEnroll']);
});
