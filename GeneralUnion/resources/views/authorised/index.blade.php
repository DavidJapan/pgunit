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
                        <nav class="menu">
                            <ul class="sidebar-menu metismenu" id="sidebar-menu">
                                <!-- ko template:{name:'menuitem',foreach: menuItems} -->
                                <!-- /ko -->
                            </ul>
                        </nav>
                    </div>
                </aside>
                <script id="menuitem" type="text/html">
                    <!-- ko if:$data.items -->
                    <li>
                        <a href="#">
                            <i class="fa fa-table"></i>
                            <span data-bind="text: $data.text"></span>
                            <i class="fa arrow"></i>
                        </a>
                        <ul class="sidebar-nav">
                            <!-- ko template:{name:'menuitem', foreach: $data.items} -->
                            <!-- /ko -->
                        </ul>
                    </li>
                    <!-- /ko -->
                    <!-- ko if: !$data.items -->
                    <!-- ko template:'leafitem' -->
                    <!-- /ko -->
                    <!-- /ko -->
                </script>
                <script id="leafitem" type="text/html">
                    <li>
                        <a class="ajax-link" data-bind="attr: { href: $data.href}, text: $data.text"></a>
                    </li>
                </script>
                <div class="sidebar-overlay" id="sidebar-overlay"></div>
                <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
                <div class="mobile-menu-handle"></div>
                <article class="content authorised-page">
                    <section class="section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-block">
                                        <div id="html-content"></div>
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
            <script src="/js/pusher.min.js"></script>
            <script src="/js/vendor/vendor.js"></script>
            <script src="/js/crud/namespace.js"></script>
            <script src="/js/crud/extra.js"></script>
            <script src="/js/crud/get_scripts.js"></script>
            <script src="/js/crud/validation_rules.js"></script>
            <script src="/js/vendor/app.js"></script>
            <script src="/js/crud/validation_rules.js"></script>
            <script src="/js/crud/leftmenu_ajaxhtml.js"></script>
            <script src="/js/crud/error_handler.js"></script>
            <script src="/js/crud/primary_key.js"></script>
            <script src="/js/crud/view_model_standard.js"></script>
            <script src="/js/crud/view_model_edit_users.js"></script>
            <script src="/js/crud/data_table_factory.js"></script>
            <script src="/js/crud/data_table_factory_extend.js"></script>
            <script src="/js/crud/template_binding_context.js"></script>
            <script src="/js/crud/data_table_factory_templates.js"></script>
            <script src="/js/crud/startup.js"></script>
            <script>
                $(function()
                {
                    ko.dt.menuModel.getMenuData();
                    ko.applyBindings(ko.dt.menuModel, document.getElementById('sidebar-left'));
                });
            </script>
    </body>
</html>