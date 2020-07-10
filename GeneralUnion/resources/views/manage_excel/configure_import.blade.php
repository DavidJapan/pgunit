<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="csrf-token" content="<?php echo csrf_token() ?>" />
        <meta name="description" content="description">
        <meta name="author" content="David Mann">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> General Union Administration Database </title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" href="/css/vendor.css">
        <link rel="stylesheet" href="/css/app.css"> </head>
    <body>
        <div class="main-wrapper">
            <div class="app" id="app">
                <header class="header">
                    <div class="header-block header-block-collapse d-lg-none d-xl-none">
                        <button class="collapse-btn" id="sidebar-collapse-btn">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <div class="header-block header-block-buttons">
                        <a href="https://generalunion.org" class="btn btn-sm header-btn">
                            <i class="fa fa-building"></i>
                            <span>General Union</span>
                        </a>
                    </div>
                    <div class="header-block header-block-nav">
                        <ul class="nav-profile">
                            <li class="profile dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    <span class="name">
                                        <?php 
        			        echo (Auth::user()->givenname . " " . Auth::user()->familyname);
        			      ?> </span>
                                </a>
                                <div class="dropdown-menu profile-dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <div class="dropdown-item">
                                        <form method="post" action="/logout" class="inline"> @csrf
                                            <button type="submit" name="logout button" value="submit_value" class="link-button"> Logout
                                                <i class="fa fa-power-off icon"></i>
                                            </button>
                                        </form>
                                    </div>
                            </li>
                        </ul>
                        </div>
                </header>
                <aside class="sidebar">
                    <div class="sidebar-container">
                        <div class="sidebar-header">
                            <a href="/authorised">
                                <div class="logo">.</div>
                            </a>
                        </div>
                        <div class="import-task-menu" data-bind="with: fieldsTableFactory">
                            <h4>Import Tasks</h4>
                            <select id="select-import-task" data-bind="
                                options: tasks,
                                optionsText: 'name',
                                optionsValue: 'modelName',
                                optionsCaption: 'Choose task...',
                                value: selectedModelName,
                                event:{ change: taskChanged}"> </select>
                            <table style="width: 95%" class="table table-advance table-hover" id="fields-table">
                                <thead>
                                    <tr>
                                        <th> DB field </th>
                                        <th> Excel Col </th>
                                    </tr>
                                </thead>
                                <tbody> </tbody>
                            </table>
                        </div>
                        <script id="db_field" type="text/html">
                            <span data-bind="text: rowData.db()"> </span>
                        </script>
                        <script id="excel_field" type="text/html">
                            <input size="5" data-bind="value: rowData.excel_col()"> </script>
                    </div>
                </aside>
                <div class="mobile-menu-handle"></div>
                <article class="content configure_import-page">
                    <section class="section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <div id="excel-files-display">
                                            <a data-bind="click: importExcelFileAsBatch" class="btn btn-primary" href="#">
                                                <i class="fa fa-plus-circle fa-lg"></i> Batch Import </a>
                                            <a data-bind="click: viewOriginalFile" class="btn btn-primary" href="#">
                                                <i class="fa fa-plus-circle fa-lg"></i> View full file </a>
                                            <span id="error_message" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="excel_file_table_container">
                                            <table id="read-excel-file-table" class="table table-bordered table-advance table-hover" width="100%" cellspacing="0"> </table>
                                        </div>
                                        <div data-bind="with: importedDataTableFactory"> @include('manage_excel.child_views.editor') @include('manage_excel.child_views.add')
                                            <div id="imported-data-table_container">
                                                <table id="imported-data-table" class="table table-bordered table-advance table-hover" width="100%" cellspacing="0"> </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </article>
                <script id="customMessageTemplate" type="text/html">
                    <em class="alert-error alert-danger" data-bind='validationMessage: field'></em>
                </script>
                <footer class="footer">
                    <div class="footer-block author">
                        <ul>
                            <li> Template created by
                                <a href="https://github.com/modularcode">ModularCode</a>
                            </li>
                            <li>
                                <a href="https://github.com/modularcode/modular-admin-html#get-in-touch">get in touch</a>
                            </li>
                        </ul>
                    </div>
                </footer>
                </div>
            </div>
            <script id="imported_data_display" type="text/html">
                <span data-bind="text: cellData"></span>
            </script>
            <script id="cropped_description" type="text/html"> <pre>
    
            <span data-bind="text: rowData.croppedDescription"></span>
    
        </pre>
                <!-- ko if: rowData.showMoreLink() -->
                <a href="#" data-bind="attr:{title: rowData.description}, tooltipster:{side: 'right', 
                theme: 'tooltipster-shadow', maxWidth: 500}"> Show more... </a>
                <!-- /ko -->
            </script> @include('elements.selector') @include('manage_excel.child_views.templates.textbox') @include('manage_excel.child_views.templates.textarea') @include('manage_excel.child_views.templates.selectbox') @include('manage_excel.child_views.templates.datepicker')
            @include('manage_excel.child_views.templates.readonly')
            <script src="/js/vendor/vendor.js"></script>
            <script src="/js/crud/namespace.js"></script>
            <script src="/js/vendor/app.js"></script>
            <script src="/js/crud/validation_rules.js"></script>
            <script src="/js/crud/error_handler.js"></script>
            <script src="/js/crud/primary_key.js"></script>
            <script src="/js/crud/data_table_factory.js"></script>
            <script src="/js/crud/data_table_factory_extend.js"></script>
            <script src="/js/crud/template_binding_context.js"></script>
            <script src="/js/crud/data_table_factory_templates.js"></script>
            <script src="/js/crud/edit.js"></script>
            <script src="/js/crud/selected_item.js"></script>
            <script src="/js/crud/selected_items.js"></script>
            <script src="/js/crud/add.js"></script>
            <script src="/js/crud/new_item.js"></script>
            <script src="/js/import_excel/import_excel_post_data.js"></script>
            <script src="/js/import_excel/import_excel_response_data.js"></script>
            <script src="/js/import_excel/reports_task.js"></script>
            <script src="/js/import_excel/import_tasks.js"></script>
            <script src="/js/import_excel/fields_table_factory.js"></script>
            <script src="/js/import_excel/imported_data_table_factory.js"></script>
            <script src="/js/import_excel/excel_file_table_factory.js"></script>
            <script src="/js/import_excel/import_factory.js"></script>
            <script src="/js/crud/knockout-datetimepicker.js"></script>
            <script>
                $(function()
                {
                    var viewModel, viewData;
                    viewData = @json($view_data);
                    viewModel = new ko.dt.ImportFactory(viewData);
                    viewModel.init();
                    ko.applyBindings(viewModel, document.getElementById('app'));
                });
            </script>
    </body>
</html>