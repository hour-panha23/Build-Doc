"use strict";

var main_view = (() => {
    const mThis = {};

    /* =====================================================
       STATE
    ===================================================== */

    mThis.apiCluster = "menus";
    mThis.onLayoutLoad = null;

    mThis.current_view_name = "";
    mThis.pusher_channel = {};

    mThis.prevView = null;
    mThis.didInitialViewCleanup = false;
    mThis.prev_shown_dropdown_menus = null;
    mThis.initialized = false;

    mThis.MULTI_WAREHOUSE_OP = 1;
    mThis.DEF_TO_WAREHOUSE_ID = 1;
    mThis.DEF_WAREHOUSE_ID = 1;

    mThis.notifPanel = document.querySelector(".main-notif-panel");
    mThis.taskPanel = document.querySelector(".main-task-panel");

    /* =====================================================
       HELPERS
    ===================================================== */

    const qs = (root, selector) => (root ? root.querySelector(selector) : null);

    const qsa = (root, selector) =>
        root ? Array.from(root.querySelectorAll(selector)) : [];

    const meta = name =>
        document
            .querySelector(`meta[name="${name}"]`)
            ?.getAttribute("content") || "";

    const toNumber = v => Number(v || 0);

    /* =====================================================
       PHASE 1 — EARLY CACHE
       Important:
       This is called immediately after main_view is created,
       before deferred page component scripts execute.
    ===================================================== */

    mThis.cacheMeta = () => {
        mThis.base_url = meta("base_url");
        mThis.asset_url = meta("asset_url");
        mThis.branch_id = meta("sess_branch_id");
        mThis.user_id = meta("sess_user_id");
        mThis.subs_id = meta("subs_id");
        mThis.app_id = meta("app_id");
        mThis.app_short_name = meta("app_short_name");

        mThis.auth_script_url =
            "https://cdn.vectoraclouds.com/frontcore/utils/AuthManager.v2.js";

        mThis.secure_endpoint = [
            mThis.base_url,
            "/api/1a2b3c4d5e6f7g8h9i0j1k2l3m/en"
        ].join("");

        mThis.backend_channel_name = [
            "wiscam_backend_",
            String(mThis.subs_id || "").toLowerCase(),
            mThis.branch_id > 0 ? `_${mThis.branch_id}` : ""
        ].join("");
    };

    mThis.cacheDom = () => {
        mThis.VSAppContent = document.querySelector("#vs_workspace");
        mThis.side_menus = document.querySelector("#vs_aside_menu_wrapper");
        mThis.top_right_menus = document.querySelector(
            "#_main_top_right_menus"
        );

        mThis.elScreenTitle = document.querySelector("#screen_title");
        mThis.elScreenTitle_mobile = document.querySelector(
            "#mobile_screen_title"
        );
        mThis.lnkFilterButton = document.querySelector("#_db_filter_data");
    };

    mThis.cacheTopMenus = () => {
        const root = mThis.top_right_menus;
        if (!root) return;

        mThis.btnTasks = qs(root, "#_main_btn_tasks");
        mThis.btnLang = qs(root, "#_main_btn_lang");
        mThis.btnUser = qs(root, "#_main_btn_user");
        mThis.btnNotif = qs(root, "#_main_btn_notif");
        mThis.btnApps = qs(root, "#_main_btn_apps");

        mThis.mnuChangePassword = qs(root, "#_main_mnu_changepwd");
        mThis.mnuLogout = qs(root, "#_main_mnu_logout");
        mThis.mnuAbout1 = qs(root, "#_main_mnu_about");

        mThis.notifPanel = qs(root, ".main-notif-panel");
        mThis.taskPanel = qs(root, ".main-task-panel");
    };

    mThis.refreshDomCache = () => {
        mThis.cacheMeta();
        mThis.cacheDom();
        mThis.cacheTopMenus();
    };

    mThis.validateRequiredDom = () => {
        const missing = [];

        if (!mThis.VSAppContent) missing.push("#_app_content");
        if (!mThis.side_menus) missing.push("#vs_aside_menu_wrapper");
        if (!mThis.top_right_menus) missing.push("#_main_top_right_menus");
        if (!mThis.elScreenTitle) missing.push("#screen_title");
        if (!mThis.elScreenTitle_mobile) missing.push("#mobile_screen_title");

        if (missing.length) {
            console.error("[main_view] Missing DOM:", missing.join(", "));
        }
    };

    /* =====================================================
       VSAPI
    ===================================================== */

    mThis.init_vsapi = async () => {
        await vsapi.init({
            authType: vsapi.authTypes.BEARER,

            tokenResolver: async () => {
                console.log("tokenResolver is called");
                const res = await fetch("/api/vsx-sec/token", {
                    credentials: "include",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                const json = await res.json();

                if (json.status_code === 200) {
                    return json.data.token;
                }

                alert(
                    "[vsapi] tokenResolver() got error: " + json.error_message
                );
                return null;
            },

            defaultLoaderSelector: "#vs_loader",
            useStreamingProgress: true
        });
    };

    /* =====================================================
       PHASE 2 — INIT
    ===================================================== */

    mThis.init = async () => {
        if (mThis.initialized) return;

        mThis.refreshDomCache();
        mThis.validateRequiredDom();

        if (!mThis.branch_id || !mThis.user_id) {
            console.error(
                "branch_id and user_id are missing; notifications may not work."
            );
        }

        if (window.VSUtil) {
            VSUtil.selectClass = "form-select";
            VSUtil.dropdownPosition = "below";
        }

        if (mThis.side_menus && window.VSRoute) {
            VSRoute.init(
                mThis.side_menus.querySelectorAll("a.menu-item"),
                "DashboardComponent",
                mThis.side_menus,
                true
            );
        }

        mThis.bindTopMenus();
        mThis.bindDocumentDropdownClose();
        mThis.bindLogout();
        mThis.bindUserMenus();
        mThis.displayNotifications(false);
        mThis.bindNotificationScroll();

        mThis.displayUserMenus();
        if (mThis.nextCursor) {
            mThis.displayNotifications(true); // Pass true to append next page
        }
        mThis.displayTasks();

        mThis.initialized = true;

        if (typeof mThis.onLayoutLoad === "function") {
            mThis.onLayoutLoad();
        }
    };

    /* =====================================================
       TOP RIGHT MENUS
    ===================================================== */

    // mThis.bindTopMenus = () => {
    //     if (!mThis.top_right_menus) return;

    //     mThis.top_right_menus.onclick = e => {
    //         const langLink = e.target.closest(".lnk-lang");

    //         if (langLink) {
    //             e.preventDefault();

    //             const lang = langLink.dataset.lang;

    //             LocaleManager.translateAll(lang);
    //             mThis.setLangMenu(lang);

    //             if (window.SideMenuHelper && mThis.side_menus) {
    //                 SideMenuHelper.populateItems(mThis.side_menus);
    //             }

    //             langLink.closest(".dropdown-menu")?.classList.remove("show");
    //             LocaleManager.saveLang(lang);
    //             return;
    //         }

    //         const btn = e.target.closest(".btn-dropdown");

    //         if (!btn) return;

    //         /*
    //           If VSEL layout.js is present, it may already handle dropdown
    //           toggling. For old layouts, main.js still handles it.
    //         */
    //         if (window.VSLayout && document.body.classList.contains("vs-app")) {
    //             return;
    //         }

    //         e.preventDefault();

    //         const dropdown = btn.closest(".dropdown");
    //         const menu = qs(dropdown, ".dropdown-menu");

    //         if (!menu) return;

    //         if (
    //             mThis.prev_shown_dropdown_menus &&
    //             mThis.prev_shown_dropdown_menus !== menu
    //         ) {
    //             mThis.prev_shown_dropdown_menus.classList.remove("show");
    //         }

    //         menu.classList.toggle("show");

    //         mThis.prev_shown_dropdown_menus = menu.classList.contains("show")
    //             ? menu
    //             : null;
    //     };
    // };

    // mThis.bindDocumentDropdownClose = () => {
    //     document.addEventListener("click", e => {
    //         const menu = mThis.prev_shown_dropdown_menus;
    //         const container = menu ? menu.closest(".dropdown") : null;

    //         if (container && !container.contains(e.target)) {
    //             menu.classList.remove("show");
    //             mThis.prev_shown_dropdown_menus = null;
    //         }

    //         if (e.target.matches(".dropdown-item")) {
    //             e.target.closest(".dropdown-menu")?.classList.remove("show");
    //         }
    //     });
    // };

    mThis.bindTopMenus = () => {
        if (!mThis.top_right_menus) return;

        mThis.top_right_menus.onclick = e => {
            const langLink = e.target.closest(".lnk-lang");

            if (langLink) {
                e.preventDefault();
                const lang = langLink.dataset.lang;

                LocaleManager.translateAll(lang);
                mThis.setLangMenu(lang);

                if (window.SideMenuHelper && mThis.side_menus) {
                    SideMenuHelper.populateItems(mThis.side_menus);
                }

                langLink
                    .closest(
                        ".notif-dropdown-menu, .vs-dropdown-menu, .dropdown-menu"
                    )
                    ?.classList.remove("show", "is-open");
                LocaleManager.saveLang(lang);
                return;
            }

            const btn = e.target.closest(".btn-dropdown");
            if (!btn) return;

            if (window.VSLayout && document.body.classList.contains("vs-app")) {
                return;
            }

            e.preventDefault();

            // 1. Support .vs-dropdown alongside .dropdown
            const dropdown =
                btn.closest(".vs-dropdown") || btn.closest(".dropdown");
            if (!dropdown) return;

            // 2. Support .notif-dropdown-menu alongside standard menus
            const menu =
                qs(dropdown, ".notif-dropdown-menu") ||
                qs(dropdown, ".vs-dropdown-menu") ||
                qs(dropdown, ".dropdown-menu");

            if (!menu) return;

            // Close previously opened dropdown if a different one was clicked
            if (
                mThis.prev_shown_dropdown_menus &&
                mThis.prev_shown_dropdown_menus !== menu
            ) {
                mThis.prev_shown_dropdown_menus.classList.remove(
                    "show",
                    "is-open"
                );
            }

            // 3. Toggle both visibility classes
            menu.classList.toggle("is-open");
            menu.classList.toggle("show");

            mThis.prev_shown_dropdown_menus =
                menu.classList.contains("show") ||
                menu.classList.contains("is-open")
                    ? menu
                    : null;
        };
    };

    mThis.bindDocumentDropdownClose = () => {
        document.addEventListener("click", e => {
            const menu = mThis.prev_shown_dropdown_menus;
            const container = menu
                ? menu.closest(".vs-dropdown") || menu.closest(".dropdown")
                : null;

            if (container && !container.contains(e.target)) {
                menu.classList.remove("show", "is-open");
                mThis.prev_shown_dropdown_menus = null;
            }

            if (
                e.target.matches(".dropdown-item") ||
                e.target.matches(".vs-dropdown-item")
            ) {
                e.target
                    .closest(
                        ".notif-dropdown-menu, .vs-dropdown-menu, .dropdown-menu"
                    )
                    ?.classList.remove("show", "is-open");
            }
        });
    };
    /* =====================================================
       AUTH / USER
    ===================================================== */

    mThis.bindLogout = () => {
        const lnkLogout = qs(mThis.side_menus, "#_main_lnkLogout");

        lnkLogout?.addEventListener("click", e => {
            e.preventDefault();
            mThis.confirmLogout();
        });
    };

    mThis.bindUserMenus = () => {
        mThis.mnuChangePassword?.addEventListener("click", e => {
            e.preventDefault();

            const user = AuthManager?.user || null;

            if (!user) {
                cv_interact.error("Authentication failed!");
                return;
            }

            ChangePasswordDialog.show({
                login_name: user.login_name,
                user_id: user.id
            });
        });

        mThis.mnuLogout?.addEventListener("click", e => {
            e.preventDefault();
            mThis.confirmLogout();
        });
    };

    mThis.confirmLogout = () => {
        cv_interact.confirm(
            "Do you want to log out?",
            {
                title: "Sign Out",
                confirmButtonText: "Log Out",
                cancelButtonText: "No, I stay in",
                context: "delete",
                translate: true
            },
            ok => {
                if (ok) mThis.logOut();
            }
        );
    };

    mThis.logOut = () => {
        mThis.deleteAllCookies();
        window.location.replace([mThis.base_url, "/logout"].join(""));
    };

    mThis.deleteAllCookies = () => {
        document.cookie.split(";").forEach(cookie => {
            const [name] = cookie.trim().split("=");
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/`;
        });
    };

    /* =====================================================
       LANGUAGE
    ===================================================== */

    mThis.setLangMenu = lang => {
        if (!window.LocaleManager?.langs?.[lang]) return;

        const langInfo = LocaleManager.langs[lang];
        const langName = langInfo.name;
        const iconImage = langInfo.icon_image;

        const label = qs(mThis.top_right_menus, "#_main_lang_name");

        if (label) {
            label.textContent = langName;
        }

        const iconUrl = [mThis.asset_url, "/images/icons/", iconImage].join("");

        const icon =
            qs(mThis.btnLang, '[data-role="lang-icon"]') ||
            qs(mThis.btnLang, ".lang-icon") ||
            qs(mThis.btnLang, "img");

        if (icon) {
            icon.setAttribute("src", iconUrl);
        }

        if (mThis.btnLang) {
            mThis.btnLang.dataset.lang = lang;
        }

        LocaleManager.lang = lang;

        document.body.classList.remove("font-kh", "font-en");
        document.body.classList.add(lang === "km" ? "font-kh" : "font-en");
    };

    /* =====================================================
       NOTIFICATIONS
    ===================================================== */

    mThis.nextCursor = null;
    mThis.isNotifLoading = false;

    mThis.displayNotifications = (isLoadMore = false) => {
        if (!mThis.notifPanel) {
            console.warn(
                "[Notif Debug] displayNotifications cancelled: notifPanel DOM element not found."
            );
            return Promise.resolve();
        }

        if (mThis.isNotifLoading) {
            console.log(
                "[Notif Debug] displayNotifications skipped: Already fetching notifications."
            );
            return Promise.resolve();
        }

        mThis.isNotifLoading = true;

        // 1. SHOW LOADING INDICATOR
        if (!isLoadMore) {
            console.log(
                "[Notif Debug] Fetching Page 1 (Resetting panel and showing initial loader)..."
            );
            mThis.nextCursor = null;
            mThis.notifPanel.innerHTML = `
            <div class="main-notif-item notif-loader text-center p-3 text-muted">
                <i class="fa fa-spinner fa-spin me-1"></i> Loading notifications...
            </div>
        `;
        } else {
            console.log(
                `[Notif Debug] Fetching next page using cursor: "${mThis.nextCursor}"`
            );
            const loaderEl = document.createElement("div");
            loaderEl.className =
                "main-notif-item notif-loader text-center p-2 text-muted";
            loaderEl.innerHTML = `<i class="fa fa-spinner fa-spin me-1"></i> Loading more...`;
            mThis.notifPanel.appendChild(loaderEl);

            // Auto-scroll to show the loader at the bottom
            mThis.notifPanel.scrollTop = mThis.notifPanel.scrollHeight;
        }

        // Build URL with cursor query parameter for page 2+
        let url = `${mThis.base_url}/api/notifications`;
        if (isLoadMore && mThis.nextCursor) {
            url += `?cursor=${encodeURIComponent(mThis.nextCursor)}`;
        }

        console.log(`[Notif Debug] Calling API: ${url}`);

        return vsapi
            .call(url, null, {
                useCache: false,
                cacheTTL: 3000
            })
            .then(res => {
                // Remove loader element before rendering new items
                qsa(mThis.notifPanel, ".notif-loader").forEach(el =>
                    el.remove()
                );

                console.log("[Notif Debug] Raw API Response:", res);
                let count = 0;

                if (res) {
                    // Handle both unwrapped and wrapped payloads
                    const paginator = res.data?.data ? res.data : res;
                    const items = Array.isArray(paginator.data)
                        ? paginator.data
                        : [];

                    // Extract next cursor token from next_page_url or next_cursor
                    let nextCursor = paginator.next_cursor || null;
                    if (!nextCursor && paginator.next_page_url) {
                        try {
                            const urlObj = new URL(paginator.next_page_url);
                            nextCursor = urlObj.searchParams.get("cursor");
                        } catch (e) {
                            const match = paginator.next_page_url.match(
                                /[?&]cursor=([^&]+)/
                            );
                            nextCursor = match
                                ? decodeURIComponent(match[1])
                                : null;
                        }
                    }
                    mThis.nextCursor = nextCursor;
                    console.log(
                        `[Notif Debug] Updated mThis.nextCursor = "${mThis.nextCursor}"`
                    );

                    // Update unread count
                    if (
                        res.unread_count !== undefined ||
                        paginator.unread_count !== undefined
                    ) {
                        mThis.setNotificationCount(
                            res.unread_count ?? paginator.unread_count
                        );
                    }

                    // Render notification items
                    for (const item of items) {
                        if (!item) continue;
                        mThis.addNotificationItem(item, false, isLoadMore);
                        count++;
                    }

                    console.log(
                        `[Notif Debug] Successfully rendered ${count} item(s).`
                    );
                }

                // Handle empty state only on initial load
                if (!isLoadMore && count === 0) {
                    console.log(
                        "[Notif Debug] No notifications found for initial load."
                    );
                    mThis.notifPanel.innerHTML = `
                    <div class="main-notif-item empty-item text-center">
                        <span class="p-1 text-muted">No Notifications</span>
                    </div>
                `;
                }
            })
            .catch(err => {
                console.error(
                    "[Notif Debug] Error fetching notifications:",
                    err
                );
            })
            .finally(() => {
                // Guarantee cleanup of loading state and loader elements
                qsa(mThis.notifPanel, ".notif-loader").forEach(el =>
                    el.remove()
                );
                mThis.isNotifLoading = false;
            });
    };
    // Scroll Listener for Infinite Pagination
    mThis.bindNotificationScroll = () => {
        if (!mThis.notifPanel) {
            console.warn(
                "[Notif Debug] bindNotificationScroll failed: notifPanel DOM element not found."
            );
            return;
        }

        // Determine the actual container that scrolls (either panel or outer menu)
        const scrollTarget =
            mThis.notifPanel.scrollHeight > mThis.notifPanel.clientHeight
                ? mThis.notifPanel
                : mThis.notifPanel.closest(
                      ".notif-dropdown-menu, .dropdown-menu"
                  ) || mThis.notifPanel;

        console.log("[Notif Debug] Scroll listener attached to:", scrollTarget);

        const handleScroll = e => {
            const el = e.target;
            const { scrollTop, scrollHeight, clientHeight } = el;
            const distanceToBottom = scrollHeight - (scrollTop + clientHeight);

            console.log(
                `[Notif Debug Scroll] scrollTop: ${Math.round(
                    scrollTop
                )}, clientHeight: ${clientHeight}, scrollHeight: ${scrollHeight}, distanceToBottom: ${Math.round(
                    distanceToBottom
                )}px`
            );

            // Trigger fetch when user is within 30px of the bottom
            if (distanceToBottom <= 30) {
                console.log(
                    `[Notif Debug Scroll] Bottom reached! nextCursor: "${mThis.nextCursor}", isLoading: ${mThis.isNotifLoading}`
                );
                if (mThis.nextCursor && !mThis.isNotifLoading) {
                    console.log(
                        "[Notif Debug Scroll] Triggering displayNotifications(true)..."
                    );
                    mThis.displayNotifications(true);
                } else if (!mThis.nextCursor) {
                    console.log(
                        "[Notif Debug Scroll] Reached end of notifications (nextCursor is null)."
                    );
                }
            }
        };

        // Attach to panel and scroll target
        mThis.notifPanel.addEventListener("scroll", handleScroll);
        if (scrollTarget !== mThis.notifPanel) {
            scrollTarget.addEventListener("scroll", handleScroll);
        }
    };

    mThis.addNotificationItem = (
        notif,
        updateCount = true,
        isAppend = false
    ) => {
        if (!notif?.message || !mThis.notifPanel) return;

        // Clear empty state placeholder
        qsa(mThis.notifPanel, ".empty-item").forEach(el => el.remove());

        let title = notif.title || "General";
        if (["na", "n/a"].includes(String(title).toLowerCase())) {
            title = "General";
        }

        const type = notif.type || "info";
        const timestamp = notif.time || notif.create_date || "Just now";

        const div = document.createElement("div");
        div.className = `main-notif-item notif-type-${type} is-unread animate-slide-in`;
        div.setAttribute("role", "article");

        div.innerHTML = `
    <div class="notif-body">
        <div class="notif-header">
            <span class="notif-title"></span>
            <span class="notif-time"></span>
        </div>
        <p class="notif-text"></p>
    </div>
    <button type="button" class="notif-dismiss-btn" aria-label="Dismiss notification">&times;</button>
`;

        div.querySelector(".notif-title").textContent = title;
        div.querySelector(".notif-time").textContent = timestamp;
        div.querySelector(".notif-text").textContent = notif.message;

        if (notif.link) {
            div.classList.add("is-clickable");
            div.addEventListener("click", e => {
                if (!e.target.closest(".notif-dismiss-btn")) {
                    window.location.href = notif.link;
                }
            });
        }

        const dismissBtn = div.querySelector(".notif-dismiss-btn");
        dismissBtn.addEventListener("click", e => {
            e.stopPropagation();
            div.classList.add("animate-fade-out");
            div.addEventListener("animationend", () => div.remove());
        });

        if (isAppend) {
            mThis.notifPanel.appendChild(div);
        } else {
            mThis.notifPanel.appendChild(div);
        }

        if (updateCount) {
            mThis.incrementNotificationCount();
        }
    };

    mThis.setNotificationCount = count => {
        if (!mThis.btnNotif) return;

        const value = toNumber(count);

        // FIX: Match the badge ID (#_main_notif_count) and class (.vs-badge-number)
        const span =
            qs(mThis.btnNotif, "#_main_notif_count") ||
            qs(mThis.btnNotif, ".vs-badge-number") ||
            qs(mThis.btnNotif, ".number--notification");

        mThis.btnNotif.dataset.count = value;

        if (span) {
            span.textContent = value;
        }
    };
    mThis.incrementNotificationCount = () => {
        const current = toNumber(mThis.btnNotif?.dataset.count);
        mThis.setNotificationCount(current + 1);
    };

    mThis.updateNotificationCount = () => {
        if (!mThis.btnNotif) return;

        vsapi
            .call(`${mThis.base_url}/api/user/unread-count`, null, {
                useCache: false,
                cacheTTL: 3000
            })
            .then(res => {
                if (res.status_code === 200) {
                    mThis.setNotificationCount(res.data || 0);
                }
            });
    };

    /* =====================================================
       TASKS
    ===================================================== */

    mThis.ensureTaskPanel = () => {
        if (mThis.taskPanel) return mThis.taskPanel;
        if (!mThis.btnTasks) return null;

        const parent =
            mThis.btnTasks.closest(".dropdown") || mThis.btnTasks.parentElement;

        if (!parent) return null;

        parent.insertAdjacentHTML(
            "beforeend",
            `
            <div class="dropdown-menu dropdown-menu-right">
                <span class="task-header">Requests</span>
                <div class="main-task-panel"></div>
            </div>
            `
        );

        mThis.taskPanel = qs(parent, ".main-task-panel");

        return mThis.taskPanel;
    };

    mThis.displayTasks = () => {
        if (!mThis.btnTasks) return;

        vsapi
            .call(`${mThis.base_url}/api/user/pending-requests`, null, {
                useCache: false,
                cacheTTL: 3000
            })
            .then(res => {
                if (res.status_code !== 200) return;

                const items = res.data || [];
                let count = 0;

                for (const item of items) {
                    if (!item) continue;

                    mThis.addTaskItem(item, false);
                    count++;
                }

                if (count === 0) {
                    const panel = mThis.ensureTaskPanel();

                    if (panel) {
                        panel.innerHTML = `
                        <div class="main-task-item">
                            <span class="task-text">No pending requests</span>
                        </div>
                    `;
                    }
                }

                mThis.setTaskCount(count);
            });
    };

    mThis.addTaskItem = (c = {}, updateCount = true) => {
        const panel = mThis.ensureTaskPanel();

        if (!panel) return;

        const requestId = c.request_id ?? c.id;
        const requestStatus = c.request_status ?? c.status;
        const requestCompleted = c.request_completed ?? c.completed;

        const isCompleted = Number(requestCompleted) === 1;
        let buttons = "";

        if (isCompleted) {
            buttons =
                String(requestStatus).toLowerCase() === "approved"
                    ? `<div class="task-buttons"><span class="task-btn-approved"><i class="fa fa-check" style="color:green"></i> Approved</span></div>`
                    : `<div class="task-buttons"><span class="task-btn-rejected"><i class="fa fa-times" style="color:red"></i> Rejected</span></div>`;
        } else {
            buttons = `
                <div class="task-buttons form-inline">
                    <button data-id="${requestId}" data-status="${requestStatus}" class="btn btn-sm btn-danger btn-reject-request">Reject</button>&nbsp;
                    <button data-id="${requestId}" class="btn btn-sm btn-success btn-approve-request">Approve</button>
                </div>
            `;
        }

        const div = document.createElement("div");

        div.className = "main-task-item";
        div.dataset.id = requestId;
        div.dataset.completed = requestCompleted;
        div.dataset.status = requestStatus;

        div.innerHTML = `
            <span class="task-title">${c.title || ""}</span>
            <span class="task-text">${c.description || ""}</span>
            ${buttons}
        `;

        panel.prepend(div);

        if (updateCount) {
            mThis.incrementTaskCount();
        }
    };

    mThis.changeRequestStatus = d => {
        const panel = mThis.taskPanel;

        if (!panel) return;

        const requestId = d.request_id ?? d.id;
        const requestCompleted = d.request_completed ?? d.completed;
        const requestStatus = d.request_status ?? d.status;

        const item = qs(panel, `.main-task-item[data-id="${requestId}"]`);
        const buttons = qs(item, ".task-buttons");

        if (!buttons) return;

        if (Number(requestCompleted) === 1) {
            buttons.innerHTML =
                String(requestStatus).toLowerCase() === "approved"
                    ? `<span class="task-btn-approved"><i class="fa fa-check" style="color:green"></i> Approved</span>`
                    : `<span class="task-btn-rejected"><i class="fa fa-times" style="color:red"></i> Rejected</span>`;
        }
    };

    mThis.setTaskCount = count => {
        if (!mThis.btnTasks) return;

        const value = toNumber(count);

        const span =
            qs(mThis.btnTasks, '[data-role="task-count"]') ||
            qs(mThis.btnTasks, ".number--task") ||
            qs(mThis.btnTasks, ".number--notification");

        mThis.btnTasks.dataset.count = value;

        if (span) {
            span.textContent = value;
        }
    };

    mThis.incrementTaskCount = () => {
        const current = toNumber(mThis.btnTasks?.dataset.count);
        mThis.setTaskCount(current + 1);
    };

    /* =====================================================
       TITLE / WORKSPACE
       Page components call:
       main_view.setContentView(mThis.self, mThis.title_prop)
    ===================================================== */

    mThis.setTitle = (title_prop = null) => {
        const title = LocaleManager.trans(title_prop, "titles");

        if (mThis.elScreenTitle) {
            mThis.elScreenTitle.textContent = title;
            mThis.elScreenTitle.setAttribute("vslang", `titles.${title_prop}`);
        }

        if (mThis.elScreenTitle_mobile) {
            mThis.elScreenTitle_mobile.textContent = title;
            mThis.elScreenTitle_mobile.setAttribute(
                "vslang",
                `titles.${title_prop}`
            );
        }

        document.title = [mThis.app_short_name, ": ", title].join("");
    };

    mThis.setContentView = (viewInstance, title_prop = null) => {
        if (!viewInstance) {
            console.error(
                "[main_view.setContentView] viewInstance is missing."
            );
            return;
        }

        /*
          First switch only:
          clean up any visible sibling that may be visible from Blade/default HTML.
          After that, switch is O(1): hide previous, show current.
        */
        if (!mThis.didInitialViewCleanup && viewInstance.parentElement) {
            Array.from(viewInstance.parentElement.children).forEach(div => {
                if (div !== viewInstance && div.style.display !== "none") {
                    div.style.display = "none";
                }
            });

            mThis.didInitialViewCleanup = true;
        }

        if (mThis.prevView && mThis.prevView !== viewInstance) {
            mThis.prevView.style.display = "none";
        }

        viewInstance.style.display = "block";
        mThis.prevView = viewInstance;

        if (mThis.lnkFilterButton) {
            mThis.lnkFilterButton.style.visibility =
                title_prop === "dashboard" ? "visible" : "hidden";
        }

        if (title_prop) {
            mThis.setTitle(title_prop);
        }
    };

    /* =====================================================
       MISC
    ===================================================== */

    mThis.displayUserMenus = () => {
        return;
    };

    mThis.getEncryptData = (qstring, onFinish) => {
        vsapi
            .call([mThis.base_url, "/api/vs-encrypt031181"].join(""), {
                data: qstring
            })
            .then(res => {
                onFinish(res.data || res);
            });
    };

    return mThis;
})();

/* =========================================================
   CRITICAL EARLY CACHE
   Must run before deferred page component scripts execute.
========================================================= */

main_view.cacheMeta();
main_view.cacheDom();
main_view.cacheTopMenus();

/* =========================================================
   DOM READY
========================================================= */

window.addEventListener("DOMContentLoaded", async () => {
    await main_view.init_vsapi();

    if (window.VSMoney) {
        await VSMoney.init();
    }

    main_view.init();

    if (window.VSUtil) {
        VSUtil.dropdownPosition = "auto";
    }

    if (window.LocaleManager && main_view.VSAppContent) {
        LocaleManager.translateZone(main_view.VSAppContent);
        main_view.setLangMenu(LocaleManager.currentLanguage.code);
    }

    const inputs = main_view.VSAppContent
        ? main_view.VSAppContent.querySelectorAll("input")
        : [];

    inputs.forEach(el => {
        const type = el.getAttribute("type") ?? el.dataset.select ?? "";

        if (["date", "daterange", "datepicker"].includes(type.toLowerCase())) {
            new DateTimePicker(el, {
                range: type === "daterange" || el.dataset.range === "true"
            });
        }

        el.onselect = e => e.preventDefault();
        el.onfocus = e => e.preventDefault();
    });
});

document.addEventListener("DOMContentLoaded", async () => {
    if (window.Signal) {
        setTimeout(() => {
            window.Signal.init(null, null, false);
        }, 2000);
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const notifBtn = document.querySelector("#_main_btn_notif");
    const notifDropdown = document.querySelector("#notifDropdown");
    const notifWrapper = document.querySelector(".vs-notification-dropdown");

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener("click", e => {
            e.stopPropagation();
            notifDropdown.classList.toggle("is-open");
            notifDropdown.classList.toggle("show");
        });

        document.addEventListener("click", e => {
            if (notifWrapper && !notifWrapper.contains(e.target)) {
                notifDropdown.classList.remove("is-open", "show");
            }
        });
    }
});
