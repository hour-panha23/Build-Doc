/**
 * Global Signal Manager
 */
const SignalManager = (() => {
    const mThis = {};
    // Flag to enable/disable logging globally
    let ENABLE_LOGS = true;

    let ticketData = null;
    let syncTimer = null;
    let isRetrying = true;
    // Persistent room target list vs currently active joined rooms per connection
    const targetRooms = new Set();
    const activeJoinedRooms = new Set();

    const projectId = window.APP_CONFIG?.projectId || "";
    const appId =
        document
            .querySelector('meta[name="app_id"]')
            ?.getAttribute("content") || "";
    const userId =
        document
            .querySelector('meta[name="sess_user_id"]')
            ?.getAttribute("content") || "";

    // Centralized Logger Functions
    const logger = {
        log: (...args) => ENABLE_LOGS && console.log(...args),
        warn: (...args) => ENABLE_LOGS && console.warn(...args),
        error: (...args) => ENABLE_LOGS && console.error(...args)
    };

    // Helper: Execute socket tasks cleanly once connected
    async function ensureConnected(action) {
        if (!window.socket) {
            logger.warn(
                "[Signal Debug] Socket instance missing. Auto-initializing..."
            );
            await SignalManager.init();
        }

        if (!window.socket) {
            logger.error(
                "[Signal Debug] Socket creation failed. Action aborted."
            );
            return;
        }

        if (window.socket.connected) {
            action();
        } else {
            logger.warn(
                "[Signal Debug] Socket waiting for connection to establish..."
            );
            await new Promise(resolve => {
                const onConnect = () => {
                    window.socket.off("connect_error", onError);
                    resolve();
                };
                const onError = () => {
                    window.socket.off("connect", onConnect);
                    resolve();
                };
                window.socket.once("connect", onConnect);
                window.socket.once("connect_error", onError);
            });

            if (window.socket?.connected) {
                action();
            }
        }
    }

    const refreshPendingNotificationCounts = () => {
        vsapi
            .call(`${main_view.base_url}/api/unread-count`, null, false)
            .then(res => {
                let d = res.status_code === 200 ? res.data : {};
                // d.count += 1;
                console.log(22, d);

                renderNotificationCounts(d);
            });
    };

    const renderNotificationCounts = d => {
        const el_notif_count = document.querySelector("#_main_notif_count");
        if (el_notif_count) {
            console.log(23, d);
            el_notif_count.parentNode.style.display =
                d.count <= 0 ? "none" : "block";
            el_notif_count.textContent = d.count;
        }
    };

    const refreshPendingRequestCounts = () => {
        vsapi
            .call(
                `${main_view.base_url}/api/approval/pending/counts`,
                null,
                false
            )
            .then(res => {
                let d = res.status_code === 200 ? res.data : {};
                renderRequestCounts(d);
            });
    };

    const renderRequestCounts = d => {
        const el_request_count = document.querySelector("#_main_request_count");
        if (el_request_count) {
            el_request_count.parentNode.style.display =
                d.total_count <= 0 ? "none" : "block";
            el_request_count.textContent = d.total_count;
        }
        // querySelectorAll returns a NodeList (never null)
        const approvalLinks = main_view.side_menus.querySelectorAll(
            ".lnk_approval"
        );

        // Check if no elements were found or if count_fields isn't set yet
        if (!mThis.count_fields || mThis.count_fields.length === 0) {
            mThis.count_fields = [];

            if (approvalLinks.length > 0) {
                mThis.count_fields = Array.from(approvalLinks);
            } else {
                if (!mThis.li_counts) {
                    mThis.li_counts = main_view.side_menus.querySelector(
                        "._main_lnk_approval"
                    );
                }

                mThis.li_counts
                    ?.querySelectorAll(".pending-count")
                    .forEach(el => {
                        mThis.count_fields.push(el);
                    });
            }
        }

        // Safely iterate over count_fields using .forEach()
        mThis.count_fields.forEach(el => {
            const f = el.querySelector(".pending-count").dataset.field;
            console.log(123, f);
            console.log(124, d[f]);

            if (f && d[f] !== undefined) {
                el.querySelector(".pending-count").parentNode.style.display =
                    d[f] <= 0 ? "none" : "block";
                el.querySelector(".pending-count").textContent = d[f];
            }
        });
    };

    return {
        /**
         * Toggle logging on or off dynamically
         * @param {boolean} flag
         */
        setEnableLogs(flag) {
            ENABLE_LOGS = Boolean(flag);
        },

        syncRooms(delayMs = 500) {
            if (syncTimer) {
                logger.log(
                    "[Signal Debug] [syncRooms] Existing sync timer reset."
                );
                clearTimeout(syncTimer);
            }

            logger.log(
                `[Signal Debug] [syncRooms] Delaying room sync by ${delayMs}ms to let backend auto-join settle...`
            );

            syncTimer = setTimeout(() => {
                logger.log(
                    `[Signal Debug] [syncRooms] Executing delayed room sync. Socket ID: ${window.socket?.id}`
                );

                if (!window.socket?.connected) {
                    logger.warn(
                        "[Signal Debug] [syncRooms] Aborted — Socket disconnected during delay period."
                    );
                    return;
                }

                targetRooms.forEach(roomId => {
                    if (activeJoinedRooms.has(roomId)) {
                        logger.log(
                            `[Signal Debug] [syncRooms] Room "${roomId}" already joined on current socket. Skipping.`
                        );
                    } else {
                        const roomPayload = {
                            socketId: window.socket.id,
                            projectId: ticketData?.projectId || projectId,
                            appId: ticketData?.appId || appId,
                            roomId: roomId
                        };
                        logger.log(
                            `[Signal Debug] [syncRooms] Emitting "join_room" for "${roomId}":`,
                            roomPayload
                        );
                        window.socket.emit("join_room", roomPayload);
                        activeJoinedRooms.add(roomId);
                    }
                });
                isRetrying = false;

                logger.log(
                    "[Signal Debug] [syncRooms] Room sync complete. Currently active joined rooms:",
                    Array.from(activeJoinedRooms)
                );
            }, delayMs);
        },

        async bindDefaultEvents() {
            logger.log(
                "[Signal Debug] Registering default application listeners..."
            );

            await this.addListener("term_promoting", null, rawPayload => {
                const payload =
                    typeof rawPayload === "string"
                        ? JSON.parse(rawPayload)
                        : rawPayload;

                const done = Number(payload?.progress_done ?? 0);
                const total = Number(payload?.progress_total ?? 0);
                const percent =
                    payload?.progress_percent !== undefined
                        ? Number(payload.progress_percent)
                        : total > 0
                        ? Math.round((done / total) * 100)
                        : 0;

                const currentSocketId = window.socket?.id;
                const isOwner =
                    payload?.socket_id && currentSocketId
                        ? payload.socket_id === currentSocketId
                        : true;

                logger.log("Is Owner:", isOwner, "Socket ID:", currentSocketId);

                const wasPromoting =
                    localStorage.getItem("is_term_promoting") === "true";

                if (percent < 100) {
                    if (!wasPromoting) {
                        localStorage.setItem("is_term_promoting", "true");
                        window.dispatchEvent(
                            new CustomEvent("term_promoting_changed", {
                                detail: { isPromoting: true }
                            })
                        );
                    }
                } else {
                    if (wasPromoting) {
                        localStorage.removeItem("is_term_promoting");
                        window.dispatchEvent(
                            new CustomEvent("term_promoting_changed", {
                                detail: { isPromoting: false }
                            })
                        );
                    }
                }

                const isBlockedDialog =
                    Swal.isVisible() &&
                    document
                        .querySelector(".swal2-popup")
                        ?.innerHTML.includes("Action Blocked");

                if (!isOwner) {
                    if (percent < 100) {
                        if (isBlockedDialog) return;

                        const isToastActive =
                            Swal.isVisible() &&
                            document.querySelector(".swal2-toast");

                        if (!isToastActive) {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "info",
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                html: `
                        <div style="font-size: 13px; font-weight: 500; color: #1e293b; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                            <span>Term Promoting:</span>
                            <b id="swal-toast-percent" style="color: #2563eb;">${percent}%</b>
                            <span id="swal-toast-counts" style="color: #64748b; font-size: 12px;">(${done}/${total})</span>
                        </div>
                    `,
                                didOpen: toast => {
                                    toast.style.padding = "8px 12px";
                                    toast.style.minHeight = "auto";
                                    toast.style.width = "auto";
                                    toast.style.alignItems = "center";
                                    const icon = toast.querySelector(
                                        ".swal2-icon"
                                    );
                                    if (icon) {
                                        icon.style.margin = "0 8px 0 0";
                                        icon.style.transform = "scale(0.7)";
                                    }
                                }
                            });
                        } else {
                            const percentElem = document.getElementById(
                                "swal-toast-percent"
                            );
                            const countsElem = document.getElementById(
                                "swal-toast-counts"
                            );

                            if (percentElem && countsElem) {
                                percentElem.textContent = `${percent}%`;
                                countsElem.textContent = `(${done}/${total})`;
                            }
                        }
                    } else {
                        TermComponent?.termListView?.showPage(
                            TermComponent?.getFilterData()
                        );
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "Term promotion completed",
                            timer: 3500,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    }
                    return;
                }

                const updateSwalUI = (pDone, pTotal, pPercent) => {
                    const container = Swal.getHtmlContainer();
                    if (container) {
                        const bar = container.querySelector(
                            "#swal-progress-bar"
                        );
                        const count = container.querySelector(
                            "#swal-progress-count"
                        );
                        const totalElem = container.querySelector(
                            "#swal-progress-total"
                        );
                        const percentText = container.querySelector(
                            "#swal-progress-percent"
                        );

                        if (bar) bar.style.width = `${pPercent}%`;
                        if (count) count.textContent = pDone;
                        if (totalElem) totalElem.textContent = pTotal;
                        if (percentText)
                            percentText.textContent = `${pPercent}%`;
                    }
                };

                if (percent < 100) {
                    if (
                        !Swal.isVisible() ||
                        document.querySelector(".swal2-toast")
                    ) {
                        Swal.fire({
                            title: `
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 6px;">
                        <span style="display: inline-block; width: 10px; height: 10px; background-color: #2563eb; border-radius: 50%; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2); animation: pulse 1.5s infinite;"></span>
                        <span style="font-size: 18px; font-weight: 700; color: #0f172a;">Promoting Terms...</span>
                    </div>
                `,
                            html: `
                    <style>
                        @keyframes pulse {
                            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
                            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(37, 99, 235, 0); }
                            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
                        }
                        .swal-progress-container {
                            background: #f8fafc;
                            border: 1px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 16px;
                            margin-top: 12px;
                        }
                    </style>
                    <div class="swal-progress-container">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13px; font-weight: 500; color: #475569;">
                            <span>Overall Progress</span>
                            <span id="swal-progress-percent" style="font-weight: 700; color: #1d4ed8; background: #eff6ff; border: 1px solid #dbeafe; padding: 2px 10px; border-radius: 9999px;">
                                ${percent}%
                            </span>
                        </div>
                        <div style="width: 100%; height: 10px; background-color: #cbd5e1; border-radius: 9999px; overflow: hidden; margin-bottom: 12px; position: relative;">
                            <div id="swal-progress-bar" style="width: ${percent}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); border-radius: 9999px; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #64748b;">
                            <span>Processed: <b id="swal-progress-count" style="color: #0f172a; font-size: 13px;">${done}</b> of <b id="swal-progress-total" style="color: #0f172a; font-size: 13px;">${total}</b></span>
                            <span style="font-size: 11px; color: #94a3b8; font-weight: 500;">Do not close window</span>
                        </div>
                    </div>
                `,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            width: 480,
                            padding: "1.5rem"
                        });
                    } else {
                        updateSwalUI(done, total, percent);
                    }

                    logger.log(
                        `Promotion progress: ${done}/${total} (${percent}%).`
                    );
                } else {
                    updateSwalUI(total, total, 100);
                    TermComponent?.termListView?.showPage(
                        TermComponent?.getFilterData()
                    );

                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            iconColor: "#16a34a",
                            title:
                                '<span style="font-size: 20px; font-weight: 700; color: #0f172a;">Promotion Complete</span>',
                            html: `
                    <p style="color: #475569; font-size: 14px; margin-top: 4px; margin-bottom: 16px;">All students have been successfully promoted.</p>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; display: flex; justify-content: space-around; align-items: center;">
                        <div style="text-align: center;">
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #166534; font-weight: 600;">Status</span>
                            <span style="font-size: 14px; font-weight: 700; color: #15803d;">Successful</span>
                        </div>
                        <div style="height: 24px; width: 1px; background: #bbf7d0;"></div>
                        <div style="text-align: center;">
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #166534; font-weight: 600;">Total Students</span>
                            <span style="font-size: 14px; font-weight: 700; color: #15803d;">${total}</span>
                        </div>
                    </div>
                `,
                            confirmButtonText: "Close",
                            confirmButtonColor: "#2563eb",
                            width: 480,
                            padding: "1.5rem"
                        });
                    }, 600);
                }
            });

            await this.addListener("invoice_received", null, payload => {
                if (typeof QuickToast !== "undefined") {
                    // QuickToast.show(payload.message, {
                    //     context: "success",
                    //     container: document.querySelector("#_vs_shell"),
                    //     position: "top-center",
                    //     offsetX: 0,
                    //     timeout: 7000
                    // });
                    console.log("payload", payload);
                    switch (payload.type) {
                        case "realtime":
                            InvoicesComponent?.itemView?.showPage(
                                InvoicesComponent?.getFilterData()
                            );
                            break;
                        case "notify":
                            refreshPendingNotificationCounts();
                            break;
                        default:
                            // Optional: handle other payload types or do nothing
                            break;
                    }
                }
            });

            await this.addListener("term_promoting_failed", null, payload => {
                const errorMsg =
                    payload.error ||
                    "An unknown error occurred during promotion.";
                const taskId = payload.task_id || "N/A";

                Swal.fire({
                    icon: "error",
                    iconColor: "#dc2626",
                    title:
                        '<span style="font-size: 20px; font-weight: 700; color: #0f172a;">Promotion Failed</span>',
                    html: `
        <p style="color: #dc2626; font-size: 14px; margin-top: 4px; margin-bottom: 16px; background: #fff5f5; padding: 8px 12px; border-radius: 6px; font-family: monospace;">
            ${errorMsg}
        </p>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px; display: flex; justify-content: space-around; align-items: center;">
            <div style="text-align: center;">
                <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #991b1b; font-weight: 600;">Status</span>
                <span style="font-size: 14px; font-weight: 700; color: #dc2626;">Failed</span>
            </div>
            <div style="height: 24px; width: 1px; background: #fecaca;"></div>
            <div style="text-align: center;">
                <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #991b1b; font-weight: 600;">Task ID</span>
                <span style="font-size: 14px; font-weight: 700; color: #dc2626;">#${taskId}</span>
            </div>
        </div>
    `,
                    confirmButtonText: "Close",
                    confirmButtonColor: "#dc2626",
                    width: 480,
                    padding: "1.5rem"
                });
            });
        },

        async init(userRoomsParam = [], userEventsParam = [], isScan = false) {
            const isOptionsObject =
                typeof userRoomsParam === "object" &&
                !Array.isArray(userRoomsParam);
            const userRooms = isOptionsObject
                ? userRoomsParam?.rooms || []
                : Array.isArray(userRoomsParam)
                ? userRoomsParam
                : [];
            const userEvents = isOptionsObject
                ? userRoomsParam?.events || []
                : Array.isArray(userEventsParam)
                ? userEventsParam
                : [];

            refreshPendingNotificationCounts();
            refreshPendingRequestCounts();

            const DEFAULT_ROOMS = [projectId, appId, userId].filter(Boolean);
            const rooms = [...new Set([...DEFAULT_ROOMS, ...userRooms])];

            logger.log(
                "[Signal Debug] 1. Initializing Signal connection with target rooms:",
                rooms
            );
            logger.log(
                "[Signal Debug] 1. User events to register:",
                userEvents
            );

            rooms.forEach(r => targetRooms.add(r));
            logger.log(
                "[Signal Debug] 2. Persistent targetRooms Set:",
                Array.from(targetRooms)
            );

            try {
                if (window.socket && window.socket.connected) {
                    logger.log(
                        "[Signal Debug] Existing connected socket found. Socket ID:",
                        window.socket.id
                    );
                    this.syncRooms(100);
                    return window.socket;
                }

                logger.log(
                    "[Signal Debug] 3. Fetching signal ticket from API..."
                );
                ticketData = isScan
                    ? await vsapi.get("/api/scan-attendance-signal-ticket", {})
                    : await vsapi.get("/api/signal-ticket", {});
                logger.log("[Signal Debug] 4. Ticket retrieved:", ticketData);

                const SOCKET_URL = window.APP_CONFIG?.socketUrl || "";
                const endpoint = `${SOCKET_URL}/notifications`;
                logger.log(
                    `[Signal Debug] 5. Connecting Socket.io client to endpoint: ${endpoint}`
                );

                window.socket = io(endpoint, {
                    transports: ["websocket"],
                    perMessageDeflate: false,
                    auth: {
                        app_id: ticketData.appId,
                        timestamp: ticketData.timestamp,
                        signature: ticketData.signature,
                        project_id: ticketData.projectId,
                        user_id: ticketData.userId
                    }
                });

                window.socket.on("connect", () => {
                    logger.log(
                        `[Signal Debug] [Event: connect] Connected successfully! Socket ID: ${window.socket.id}`
                    );
                    activeJoinedRooms.clear();
                    this.syncRooms(500);
                });

                window.socket.on("disconnect", reason => {
                    logger.warn(
                        `[Signal Debug] [Event: disconnect] Socket Disconnected: ${reason}`
                    );
                    activeJoinedRooms.clear();
                });

                window.socket.on("connect_error", err => {
                    logger.error(
                        `[Signal Debug] [Event: connect_error] Connection Error: ${err.message}`
                    );
                });

                window.socket.io.on("reconnect_attempt", attempt => {
                    logger.warn(
                        `[Signal Debug] Attempting to reconnect... (Attempt #${attempt})`
                    );
                    isRetrying = true;

                    if (attempt >= 15) {
                        window.socket.disconnect();
                        logger.error(
                            "[Signal Debug] Reconnect failed 15 times. Disconnected and signal removed."
                        );
                    }
                });

                logger.log(
                    "[Signal Debug] 6. Registering default application events..."
                );
                await this.bindDefaultEvents();

                logger.log(
                    "[Signal Debug] 7. Registering custom user events..."
                );
                for (const item of userEvents) {
                    const eventName =
                        typeof item === "string" ? item : item?.name;
                    const callback =
                        typeof item === "object" ? item?.callback : null;
                    const roomId =
                        typeof item === "object" ? item?.roomId : null;

                    if (eventName) {
                        logger.log(
                            `[Signal Debug] Attaching user listener "${eventName}" (Room: ${roomId ||
                                "global"})`
                        );
                        await this.addListener(eventName, roomId, callback);
                    }
                }

                logger.log("[Signal Debug] 8. Init logic finished.");
                return window.socket;
            } catch (err) {
                logger.error(
                    "[Signal Debug] Initialization error:",
                    err.message || err
                );
            }
        },

        async joinRoom(
            roomId = "invoices",
            eventName = null,
            onReceivedCallback = null
        ) {
            if (!roomId) return;
            logger.log(
                `[Signal Debug] Requested joinRoom for room: "${roomId}"`
            );

            targetRooms.add(roomId);

            await ensureConnected(() => {
                this.syncRooms(100);

                if (eventName && typeof onReceivedCallback === "function") {
                    this.addListener(eventName, roomId, onReceivedCallback);
                }
            });
        },

        async addListener(eventName, roomId = null, callback = null) {
            if (!eventName) return;

            await ensureConnected(() => {
                logger.log(
                    `[Signal Debug] Listening for event '${eventName}'${
                        roomId ? ` in room '${roomId}'` : ""
                    }`
                );

                if (roomId) {
                    window.socket.emit("subscribe_room_event", {
                        socketId: window.socket.id,
                        roomId,
                        eventName
                    });
                }

                window.socket.off(eventName);

                window.socket.on(eventName, (...args) => {
                    const payload = args.length === 1 ? args[0] : args;

                    if (
                        roomId &&
                        payload?.roomId &&
                        payload.roomId !== roomId
                    ) {
                        return;
                    }

                    logger.log(
                        `[Signal Debug] Event '${eventName}' received for room '${roomId ||
                            "global"}'`,
                        payload
                    );

                    if (typeof callback === "function") {
                        callback(payload);
                    }
                });
            });
        },

        async joinRoomWithListeners(
            roomId = "invoices",
            events = [],
            isScan = false
        ) {
            logger.log(
                `[Signal Debug] Requested joinRoomWithListeners for room: "${roomId}"`
            );

            await this.joinRoom(roomId);

            if (Array.isArray(events) && events.length > 0) {
                for (const item of events) {
                    const eventName =
                        typeof item === "string" ? item : item.name;
                    const callback =
                        typeof item === "object" ? item.callback : null;

                    if (eventName) {
                        await this.addListener(eventName, roomId, callback);
                    }
                }
            }
        },

        async getSocketId() {
            if (isRetrying) {
                return null;
            }

            return new Promise(resolve => {
                ensureConnected(() => {
                    resolve(window.socket?.id || null);
                });
            });
        },

        isConnecting() {
            return isRetrying;
        }
    };
})();

// Attach to Global Window object
window.Signal = SignalManager;
