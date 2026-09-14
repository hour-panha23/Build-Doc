<?php
return [
    'primary-loader' => [
        'attr' => 'async',
        'single_file' => 1,
        'output_file' => '/dist/js/primary-loader.js',
        'files' => [
            '/js/components/tutor/loader.js'
        ]
    ],
    'priority-one' => [
        'attr' => '',
        'single_file' => 1,
        'output_file' => '/dist/js/ksm.priority-one.min.js',
        'files' => [
            'https://cdn.vectoraclouds.com/vsel/utils/vsapi.js',
            'https://cdn.vectoraclouds.com/vsel/utils/LocaleManager.v3.js',
            '/assets/js/priority-load.js'
        ]
    ],
    // 'primary' => [
    //     'attr' => null,
    //     'single_file' => 1,
    //     'output_file' => '/dist/js/ksm.primary.js?v=1',
    //     'files' => [
    //         // '/assets/material-js/jquery.min.js',
    //         '/assets/js/Chart/CChart.js',
    //     ]
    // ],

    // 'vsel-untils' => [
    //     'attr' => 'async',
    //     'single_file' => 1,
    //     'output_file' => '/dist/js/ksm.primary-async.js',
    //     'files' => [
    //         '/js/components/tutor/AuthManager.js'
    //     ]
    // ],

    'formal-base' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/vsel_base.js',
        'files' => [
            'https://cdn.vectoraclouds.com/vsel/utils/sanitizer/sanitizer.js',
            'https://cdn.vectoraclouds.com/vsel/utils/VSUtil.js',

            '/assets/js/sweetalert2.all.min.js',
            '/assets/js/sweetalert2.toast.js',


            'https://cdn.vectoraclouds.com/vsel/components/filterpanel/FilterPanel.js',

            'https://cdn.vectoraclouds.com/vsel/components/expandable_row/ExpandableTableRow.js',

            'https://cdn.vectoraclouds.com/vsel/components/expand_item_view/UMExpandItemView.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_dropdown_menu/VSDropdownMenu.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_dropdown_button/VSDropdownButton.js',

            'https://cdn.vectoraclouds.com/beta-vfc/VSInteractBoundary.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.utils.configSelect.js',
            'https://cdn.vectoraclouds.com/vfc/vfc.utils.inputFormat.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.form.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.material.js',

            'https://cdn.vectoraclouds.com/vsel/components/modal/GeneralDialog.js',

            //'/assets/vendors/general/popper.js/dist/umd/popper.js',

            'https://cdn.vectoraclouds.com/vsel/components/validator/validator.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_interact/cv_interact.js',
            'https://cdn.vectoraclouds.com/vsel/utils/DateHelper.js',

            'https://cdn.vectoraclouds.com/vsel/components/date_time_picker/DateTimePicker.js',
            'https://cdn.vectoraclouds.com/vsel/components/quicktoast/QuickToast.js',

            'https://cdn.jsdelivr.net/npm/fuse.js@7.1.0',
            'https://cdn.vectoraclouds.com/vsel/components/vs_choice/Choices.11.2.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_choice/vs_choices.js',

            'https://cdn.vectoraclouds.com/vsel/components/imagebox/ImageHelper.js',
            'https://cdn.vectoraclouds.com/vsel/components/imagebox/ImageBox.js',
            'https://cdn.vectoraclouds.com/vsel/components/search_input/VSSearchInput.js',
            'https://cdn.vectoraclouds.com/vsel/components/search_input/VSSearchInputHelper.js',  //Needed if developer use cusom rendering of suggestion list. It helps with arrows key navigation, set active item, and commit selected item

            '/assets/js/toastr.min.js',
            '/assets/js/init.toastr.js',
            '/assets/js/browsercontrol.js',
            //'/assets/js/qrcode.min.js',

            'https://cdn.vectoraclouds.com/vsel/layout/shell/layout.shell.js',

            'https://cdn.vectoraclouds.com/vsel/utils/VSMoney.js',
            'https://cdn.vectoraclouds.com/vsel/components/file_chooser/FileChooser.js',
            'https://cdn.vectoraclouds.com/vsel/components/listview/Listview.v3.js',
            //'https://cdn.vectoraclouds.com/vsel/components/option_editor/OptionEditor.js',
            'https://cdn.vectoraclouds.com/vsel/components/inputbox/InputBox.v2.js',

            'https://raw.githack.com/SortableJS/Sortable/master/Sortable.js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js'
        ],
        'no-minify' => [
            '/assets/js/crypto-js.js',
            '/assets/js/ckeditor.js',
        ]
    ],



    'formal-components' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/ksm.components.js?v=8',
        'files' => [

            '/js/components/umt/ChangePasswordDialog.js',
            //'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js',
            'https://cdn.vectoraclouds.com/vsel/router/router.shell.js',
            "https://cdn.socket.io/4.8.1/socket.io.min.js",


            '/js/layout/formal/main.js', /** main.js must be on top here*/

            '/js/components/formal/RenderTableReport.js',

            '/js/components/formal/PDFReport.js',
            '/js/components/formal/InvoiceDialogHelper.js',
            '/js/components/formal/FindPersonDialog.js',
            '/js/components/formal/DashboardComponent.js',
            '/js/components/formal/InvoicesComponent.js?v=2',
            '/js/components/formal/ReceiptComponent.js',
            '/js/components/formal/CashCollectionComponent.js',
            '/js/components/formal/InvoicesNotificationComponent.js',
            '/js/components/formal/StudentAccountsComponent.js',

            '/js/components/formal/ReportCenterComponent.js',
            '/js/components/formal/AccountTransactionsComponent.js',

            '/js/components/formal/EnrolledStudentsComponent.js',
            '/js/components/formal/DropOutSuspendsComponent.js',
            '/js/components/formal/TuitionPaymentsComponent.js',
            '/js/components/formal/TuitionFeeComponent.js',
            '/js/components/formal/PriceListComponent.js',
            '/js/components/formal/PolicyDiscountComponent.js',
            '/js/components/formal/EarlyPromotionComponent.js',
            '/js/components/formal/DisCountByMonthComponent.js',

            '/js/components/formal/NonTuitionFeeComponent.js',
            '/js/components/formal/DepositFeeComponent.js',
            '/js/components/formal/SuspendFeeComponent.js',
            '/js/components/formal/FindStudentComponent.js',
            '/js/components/formal/StudentInformationComponent.js',
            '/js/components/formal/ActivitiesComponent.js',
            '/js/components/formal/DiscountComponent.js',
            '/js/components/formal/AactivitiesComponent.js',
            '/js/components/formal/ParentAccountsComponent.js',
            '/js/components/formal/ReviewComponent.js',
            '/js/components/formal/DatalistComponent.js',
            '/js/components/formal/PrintStudentCardsComponent.js',
            '/js/components/formal/IdCardSettingsComponent.js',
            '/js/components/formal/AttendanceDetailsComponent.js',
            '/js/components/formal/DailyLogsComponent.js',

            '/js/components/formal/ProgramComponent.js',
            '/js/components/formal/StudentGroupComponent.js',

            '/js/components/formal/TermComponent.js',
            '/js/components/formal/AcademicYearComponent.js',
            '/js/components/formal/RequestDiscountComponent.js',
            '/js/components/formal/PromoteStudentComponent.js',
            '/js/components/formal/AssignStudentComponent.js',
            //'/js/components/formal/AccountRequestComponent.js', //not used
            '/js/components/formal/MaterialItemsComponent.js',
            '/js/components/formal/AnnouncementComponent.js',
            '/js/components/formal/PaymentMethodComponent.js',
            '/js/components/formal/SessionComponent.js',
            '/js/components/formal/ClassSessionComponent.js',
            '/js/components/formal/ComeBackComponent.js',
            '/js/components/formal/ApprovalSuspendFeeComponent.js',
            '/js/components/formal/ImportDataComponent.js',
            '/js/components/formal/NonTuitionCategoryComponent.js',
            '/js/components/formal/TestingFeeComponent.js',
            '/js/components/formal/SiblingDiscountComponent.js',
            '/js/components/formal/ReferralFeeComponent.js',
            '/js/components/formal/CoursesComponent.js',
            '/js/components/formal/TeachersComponent.js',
            '/js/components/common/RoomsComponent.js',
            '/js/components/formal/VSAnimate.js',
            '/js/components/formal/ClassesComponent.js',
            '/js/components/formal/GradeSheetComponent.js',
            '/js/components/formal/DailyScheduleComponent.js',
            '/js/components/common/BuildingsComponent.js',
            '/js/components/common/SubjectsComponent.js',
            '/js/components/common/BooksComponent.js',
            '/js/components/common/LibraryComponent.js',
            '/js/components/common/RoomTypeComponent.js',
            '/js/components/common/ScheduleDaysComponent.js',
            '/js/components/formal/HolidayComponent.js',

            '/js/components/formal/PromotionComponent.js',
            '/js/components/common/LocationComponent.js',
            '/js/components/common/SocialMediaComponent.js',
            '/js/components/common/MobileBannerComponent.js',
            '/js/components/common/MobileTCComponent.js',
            '/js/components/common/MobilePrivacyComponent.js',
            '/js/components/common/CompanyComponent.js',
            // 'https://js.pusher.com/8.2.0/pusher.min.js',
            // '/js/components/common/pusher_connect.js',
            '/js/components/formal/CurriculaComponent.js',
            '/js/components/formal/AttendanceTracksComponent.js',
            '/js/components/formal/TrackShiftComponent.js',
            '/js/components/formal/ScholarshipComponent.js',
            '/js/components/formal/StudentPermissionComponent.js',
            '/js/components/formal/OccupationComponent.js',
            '/js/components/formal/TeacherCommentComponent.js',
            '/js/components/formal/ClassAttendanceComponent.js',
            '/js/components/formal/CampusMovementComponent.js',
            '/js/components/formal/GraduatedComponent.js',

            '/js/signal.js',


        ]
    ],

    // 'tutor-components' => [
    //     'attr' => 'defer',
    //     'single_file' => 1,
    //     'output_file' => '/dist/js/ksm.components.js?v=8',
    //     'files' => [
    //         'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js',
    //         'https://cdn.vectoraclouds.com/frontcore/vsroute/vsroute.js',
    //         '/js/layout/tutor/main.js', /** main.js must be on top here*/
    //         '/js/components/tutor/SearchData.js',
    //         '/js/components/tutor/RenderTableReport.js',
    //         '/assets/js/ImageHelper.js',
    //         '/assets/js/ImageBox.js',
    //          'https://cdn.vectoraclouds.com/frontcore/components/SearchInput/VSSearchInput.js',
    //           'https://cdn.vectoraclouds.com/frontcore/components/VSMoney.js',
    //          'https://cdn.vectoraclouds.com/frontcore/components/FileChooser.js',
    //         'https://cdn.vectoraclouds.com/frontcore/components/listview/listview.v2.js',
    //            'https://cdn.vectoraclouds.com/frontcore/components/OptionEditor.js',
    //           'https://cdn.vectoraclouds.com/frontcore/components/InputBox.v2.js',
    //         //'/js/components/tutor/FindPersonDialog.bs4.js',
    //          '/js/components/tutor/FindPersonDialog.js',
    //         '/js/components/tutor/DashboardComponent.js',
    //         '/js/components/tutor/InvoicesComponent.js?v=2',
    //         '/js/components/tutor/ReportCenterComponent.js',
    //         // '/js/components/tutor/LocationComponent.js',
    //         '/js/components/tutor/EnrolledStudentsComponent.js',
    //         '/js/components/tutor/DropOutSuspendsComponent.js',
    //         '/js/components/tutor/TuitionFeeComponent.js',
    //         '/js/components/tutor/PolicyDiscountComponent.js',
    //         '/js/components/tutor/NonTuitionFeeComponent.js',
    //         '/js/components/tutor/DepositFeeComponent.js',
    //         '/js/components/tutor/SuspendFeeComponent.js',
    //         '/js/components/tutor/FindStudentComponent.js',
    //         '/js/components/tutor/StudentInformationComponent.js',
    //         '/js/components/tutor/ActivitiesComponent.js',
    //         '/js/components/tutor/DiscountComponent.js',
    //         '/js/components/tutor/AactivitiesComponent.js',
    //         '/js/components/tutor/ParentAccountsComponent.js',
    //         '/js/components/tutor/PrintStudentCardsComponent.js',
    //         '/js/components/tutor/IdCardSettingsComponent.js',
    //         '/js/components/tutor/AttendanceDetailsComponent.js',
    //         '/js/components/tutor/DailyLogsComponent.js',
    //         //'/js/components/tutor/UserManagementComponent.js',
    //         '/js/components/tutor/ProgramComponent.js',
    //         '/js/components/tutor/StudentGroupComponent.js',
    //         //'/js/components/tutor/CampusComponent.js',
    //         '/js/components/tutor/TermComponent.js',
    //         '/js/components/tutor/TuitionPaymentsComponent.js',
    //         '/js/components/tutor/AcademicYearComponent.js',

    //         '/js/components/tutor/RequestDiscountComponent.js',
    //         '/js/components/tutor/PromoteStudentComponent.js',
    //         '/js/components/tutor/AssignStudentComponent.js',
    //         '/js/components/tutor/AccountRequestComponent.js',
    //         '/js/components/tutor/BannerComponent.js',
    //         '/js/components/tutor/MaterialItemsComponent.js',
    //         '/js/components/tutor/AnnouncementComponent.js',
    //         '/js/components/tutor/PaymentMethodComponent.js',
    //         '/js/components/tutor/SessionComponent.js',
    //         '/js/components/tutor/ComeBackComponent.js',
    //         '/js/components/tutor/ApprovalSuspendFeeComponent.js',
    //         '/js/components/tutor/ImportDataComponent.js',
    //         '/js/components/tutor/NonTuitionCategoryComponent.js',
    //         '/js/components/tutor/TestingFeeComponent.js',
    //         '/js/components/tutor/SiblingDiscountComponent.js',
    //         '/js/components/tutor/ReferralFeeComponent.js',
    //         '/js/components/tutor/CoursesComponent.js',
    //         '/js/components/formal/TeachersComponent.js',
    //         '/js/components/common/RoomsComponent.js',
    //         '/js/components/formal/ClassesComponent.js',
    //         '/js/components/common/BuildingsComponent.js',
    //         '/js/components/common/SubjectsComponent.js',
    //         '/js/components/common/RoomTypeComponent.js',

    //         '/js/components/tutor/PromotionComponent.js',
    //         '/js/components/common/LocationComponent.js',
    //         '/js/components/common/SocialMediaComponent.js',
    //         '/js/components/common/MobileBrandImagesComponent.js',
    //         '/js/components/common/MobileTCComponent.js',
    //         '/js/components/common/MobilePrivacyComponent.js',
    //         '/js/components/common/CompanyComponent.js',
    //         '/assets/js/pusher/pusher.min.js',
    //         '/js/components/common/pusher_connect.js',

    //     ]
    // ],

    'umt-base' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/vsel_base.js',
        'files' => [
            'https://cdn.vectoraclouds.com/vsel/utils/sanitizer/sanitizer.js',
            'https://cdn.vectoraclouds.com/vsel/utils/VSUtil.js',
            '/assets/js/sweetalert2.all.min.js',
            '/assets/js/sweetalert2.toast.js',

            'https://cdn.vectoraclouds.com/vsel/components/filterpanel/FilterPanel.js',

            'https://cdn.vectoraclouds.com/vsel/components/expandable_row/ExpandableTableRow.js',

            'https://cdn.vectoraclouds.com/frontcore/components/UMExpandItemView.js',
            // 'https://cdn.vectoraclouds.com/vsel/components/expand_item_view/UMExpandItemView.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_dropdown_menu/VSDropdownMenu.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_dropdown_button/VSDropdownButton.js',

            'https://cdn.vectoraclouds.com/beta-vfc/VSInteractBoundary.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.utils.configSelect.js',
            'https://cdn.vectoraclouds.com/vfc/vfc.utils.inputFormat.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.form.js',
            'https://cdn.vectoraclouds.com/beta-vfc/vfc.material.js',

            'https://cdn.vectoraclouds.com/vsel/components/modal/GeneralDialog.js',

            //'/assets/vendors/general/popper.js/dist/umd/popper.js',

            'https://cdn.vectoraclouds.com/vsel/components/validator/validator.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_interact/cv_interact.js',
            'https://cdn.vectoraclouds.com/vsel/utils/DateHelper.js',

            'https://cdn.vectoraclouds.com/vsel/components/date_time_picker/DateTimePicker.js',
            'https://cdn.vectoraclouds.com/vsel/components/quicktoast/QuickToast.js',

            'https://cdn.jsdelivr.net/npm/fuse.js@7.1.0',
            'https://cdn.vectoraclouds.com/vsel/components/vs_choice/Choices.11.2.js',
            'https://cdn.vectoraclouds.com/vsel/components/vs_choice/vs_choices.js',

            'https://cdn.vectoraclouds.com/vsel/components/imagebox/ImageHelper.js',
            'https://cdn.vectoraclouds.com/vsel/components/imagebox/ImageBox.js',
            'https://cdn.vectoraclouds.com/vsel/components/search_input/VSSearchInput.js',
            'https://cdn.vectoraclouds.com/vsel/components/search_input/VSSearchInputHelper.js',  //Needed if developer use cusom rendering of suggestion list. It helps with arrows key navigation, set active item, and commit selected item

            '/assets/js/toastr.min.js',
            '/assets/js/init.toastr.js',
            '/assets/js/browsercontrol.js',
            //'/assets/js/qrcode.min.js',
            '/assets/js/SearchWidget.js',


            'https://cdn.vectoraclouds.com/vsel/layout/shell/layout.shell.js',

            'https://cdn.vectoraclouds.com/vsel/utils/VSMoney.js',
            'https://cdn.vectoraclouds.com/vsel/components/file_chooser/FileChooser.js',
            'https://cdn.vectoraclouds.com/vsel/components/listview/Listview.v3.js',
            //'https://cdn.vectoraclouds.com/vsel/components/option_editor/OptionEditor.js',
            'https://cdn.vectoraclouds.com/vsel/components/inputbox/InputBox.v2.js',

            'https://raw.githack.com/SortableJS/Sortable/master/Sortable.js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js'
        ],
        'no-minify' => [
            '/assets/js/crypto-js.js',
            '/assets/js/ckeditor.js',
        ]
    ],

    'umt-components' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/umt.components.js',
        'files' => [
            'https://cdn.vectoraclouds.com/vsel/router/router.shell.js',

            // 'https://cdn.vectoraclouds.com/vfc/vfc.utils.configSelect.js',
            // 'https://cdn.vectoraclouds.com/vfc/vfc.utils.inputFormat.js',
            // 'https://cdn.vectoraclouds.com/vfc/vfc.form.js',
            //'/assets/material-js/jquery.min.js',
            //'https://cdn.jsdelivr.net/gh/tomik23/circular-progress-bar@latest/docs/circularProgressBar.min.js',
            '/js/layout/umt/main.js',
            // '/js/components/abm/PDFReport.js',
            // '/js/components/formal/RenderTableReport.js',
            '/assets/js/xlsx/xlsx.full.min.js',
            // '/js/components/umt/FindUserDialog.js',
            '/js/components/umt/ChangeRoleDialog.js',
            '/js/components/umt/CreateLoginDialog.js',
            '/js/components/umt/BranchDialog.js',
            '/js/components/umt/ChangeLoginNameDialog.js',
            '/js/components/umt/SetPasswordDialog.js',
            '/js/components/umt/UserManagementComponent.js',
            '/js/components/umt/CampusManagementComponent.js',
            // '/js/components/umt/RoleManagementTool.js',
             '/js/components/umt/RoleManagementComponent.js',
            'https://js.pusher.com/8.2.0/pusher.min.js',
            //'/js/components/abm/pusher_client_houxpress.js',
            //'/js/components/common/pusher_connect.js',
            // '/js/components/abm/CustomersComponent.js',
        ]
    ],
    'report-scripts' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/report-scripts.js',
        'files' => [
            //'/assets/material-js/jquery.min.js',
            //'/assets/material-js/bootstrap.min.js'
        ]
    ],
    'attendance-script' => [
        'attr' => 'defer',
        'single_file' => 1,
        'output_file' => '/dist/js/attendance.js',
        'files' => [
            "https://cdn.socket.io/4.8.1/socket.io.min.js",

            '/js/signal.js',
            '/assets/js/sweetalert2.all.min.js',
            'https://cdn.vectoraclouds.com/vsel/components/quicktoast/QuickToast.js',
            'https://cdn.vectoraclouds.com/vsel/utils/DateHelper.js',
            'https://cdn.vectoraclouds.com/vsel/components/date_time_picker/DateTimePicker.js',
            'https://cdn.vectoraclouds.com/vsel/utils/vsapi.js',
            '/js/components/formal/ScanAttendanceComponent.js'

        ]
    ]
];
