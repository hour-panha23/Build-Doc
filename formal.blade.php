<!DOCTYPE html>
<html lang="<?php echo XAuthService::user()->lang ?? 'en'; ?>">

<head>

    <base href="../">

    <meta charset="utf-8" />

    <title>Vectorasoft Edvance</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/edv-logo.png') }}" />

    <meta name="description" content="Vectorasoft Edvance for General Education" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <meta name="app_short_name" content="{{ getAppShortName() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="sess_branch_id" content="{{ sess_company_id() }}" />
    <meta name="sess_user_id" content="{{ sess_user_id() }}" />
    <meta name="base_url" content="{{ url('/') }}" />
    <meta name="main_route" content="formal" />
    <meta name="default_component" content="<?php echo $defaultComponent; ?>" />
    <meta name="asset_url" content="{{ asset('assets/') }}" />
    <meta name="app_id" content="{{ sess_app_id('formal') }}" />
    <meta name="subs_id" content="{{ sess_subs_id() }}" />

    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Moul&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Battambang&family=Noto+Sans+Khmer:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- <link href="https://fonts.cdnfonts.com/css/battambang" rel="stylesheet"> -->

    <?php
StyleManager::render('vsel-formal', 1, 3);
StyleManager::render('customs-formal', 1, 4);
    ?>
    <script>
        window.APP_CONFIG = {
            socketUrl: "{{ config('signal.signal_url') }}",
            projectId: "{{ config('signal.signal_project_id') }}",
            // socketUrl:'https://signal.vectoraclouds.com';
        };
    </script>

    <?php

ScriptManager::render(
    'priority-one',
    1,
    21
);

ScriptManager::render(
    'primary',
    1,
    19
);

        ScriptManager::render( 'formal-base', 1,47);

ScriptManager::render(
    'formal-components',
    1,
    425
);

    ?>

</head>

<body class="vs-app font-en">

    <!-- =====================================================
         GLOBAL LOADER
    ====================================================== -->

    <div id="vs_loading" class="vs-loader-bar">
    </div>

    <?php
ScriptManager::render(
    'primary-loader',
    1
);
    ?>

    <!-- =====================================================
         HIDDEN FIELDS
    ====================================================== -->

    <div id="_main_hidden_fields" hidden>

        <input id="__base_url" type="hidden" value="{{ url('/') }}">

        <input id="__xsp_name" type="hidden" value="_csrf_115578">

        <input id="__xsp_value" type="hidden" value="<?php echo Str::random(30); ?>">

    </div>

    <!-- =====================================================
         MOBILE HEADER
    ====================================================== -->

    @include(
        'layouts.formal.mobile_header'
    )

    <!-- =====================================================
         MOBILE OVERLAY
    ====================================================== -->

    <div id="vs_sidebar_overlay" class="vs-sidebar-overlay"> </div>

    <!-- =====================================================
         SHELL
    ====================================================== -->

    <div id="vs_shell" class="vs-shell">

        <!-- =========================================
             SIDEBAR
        ========================================== -->

        <div id="vs_sidebar" class="vs-sidebar">

            @include(
                'menus.formal_menus'
            )

            <button id="vs_sidebar_toggle" class="vs-sidebar-toggle" type="button">

                <i id="vs_sidebar_toggle_icon" class="bi bi-chevron-double-left">
                </i>

            </button>

        </div>

        <!-- =========================================
             MAIN
        ========================================== -->

        <div id="vs_main" class="vs-main">

            <!-- =====================================
                 TOP NAV
            ====================================== -->

            <div id="vs_header" class="vs-header">

                <div class="vs-header-left">
                    <div id="vs_back_page" class="vs-back-page"> </div>

                    <div class="vs-page-title-block">

                        <h1 id="screen_title" class="vs-page-title">
                        </h1>

                        <div class="vs-page-subtitle d-flex">
                            <span class="vs-brand-ed">Ed</span>
                            <span class="vs-brand-vance">vance</span>
                            <span class="vs-subtitle-separator">
                                .
                            </span>

                            <span class="vs-subtitle-text">
                                General Education Edition
                            </span>

                        </div>

                    </div>

                </div>
                @include('layouts.formal.top_nav_right')

            </div>

            <!-- =====================================
                 BODY
            ====================================== -->

            <div id="vs_body" class="vs-body">
                <div id="vs_workspace" class="vs-workspace">
                    @include('layouts.formal._components')
                </div>
            </div>

        </div>

    </div>

</body>

</html>
