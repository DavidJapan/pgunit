<?php

/**
 * Gathers the data together necessary to provide a personalised menu depending on the roles
 * the current user belongs to.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Gathers the data together necessary to provide a personalised menu depending on the roles
 * the current user belongs to.
 */
class Navigation extends Controller {

    /**
     * The current user
     * @var User 
     */
    protected $user;

    /**
     * Returns a hashed array of menu items.
     * @return array
     */
    private function menu() {
        return [
            'administration' => $this->administration(),
            'manage_db_backup_files' => $this->manage_db_backup_files(),
            'db_testing' => $this->db_testing(),
            'users_administration' => $this->users_administration(),
            //'manage_excel_files' => $this->manage_excel_files(),
            'uploader' => $this->uploader(),
            'branch_administration' => $this->branch_administration(),
            'reports_writing' => $this->reports_writing(),
            'reports_administration' => $this->reports_administration(),
            'collective_agreements' => $this->collective_agreements(),
            'qunit_testing'=> $this->qunit_testing()
        ];
    }

    /**
     * Returns a hashed array of menu items for administering branches and sectors
     * @return array
     */
    private function branch_administration() {
        return [
            "text" => "Branches",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR", "ROLE_REPORT_EDITOR"],
            "items" => [
                [
                    "href" => "sectors/view",
                    "text" => "Sectors",
                    "id" => "edit_sectors"
                ],
                [
                    "href" => "branches/view",
                    "text" => "Branches",
                    "id" => "edit_branches"
                ]
            ]
        ];
    }
    private function qunit_testing(){
        return [
            'text' => 'QUnit Testing',
            'href' => '#',
            'roles' => ['ROLE_ADMINISTRATOR'],
            'items' => [
                [
                    'href' => "qunit/index",
                    "text" => "Qunit Test List",
                    "id" => "qunit_test_list"
                ]
                ]
            
        ];
    }
    /**
     * Returns a hashed array of menu items for testing the database.
     * @return array
     */
    private function db_testing() {
        return [
            "text" => "DB Testing",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR"],
            "items" => [
                [
                    "href" => "pgunit/test_all",
                    "text" => "pgunit Test All",
                    "id" => "pgunit_test_all"
                ],
                [
                    "href" => "pgunit/test_schema",
                    "text" => "pgunit Test Schema",
                    "id" => "pgunit_test_schema"
                ],
                [
                    "href" => "pgunit/test_users",
                    "text" => "pgunit Test Users",
                    "id" => "pgunit_test_users"
                ],
                [
                    "href" => "pgunit/test_roles",
                    "text" => "pgunit Test Roles",
                    "id" => "pgunit_test_roles"
                ],
                [
                    "href" => "pgunit/test_sectors",
                    "text" => "pgunit Test Sectors",
                    "id" => "pgunit_test_sectors"
                ],
                [
                    "href" => "pgunit/test_branches",
                    "text" => "pgunit Test Branches",
                    "id" => "pgunit_test_branches"
                ],
                [
                    "href" => "pgunit/test_individual_reports",
                    "text" => "pgunit Test Individual Reports",
                    "id" => "pgunit_test_individual_reports"
                ],
                [
                    "href" => "pgunit/test_reports",
                    "text" => "pgunit Test Reports",
                    "id" => "pgunit_test_reports"
                ],
                [
                    "href" => "pgunit/test_employers",
                    "text" => "pgunit Test Employers",
                    "id" => "pgunit_test_employers"
                ],
                [
                    "href" => "pgunit/test_report_headings",
                    "text" => "pgunit Test Report Headings",
                    "id" => "pgunit_test_report_headings"
                ],
                [
                    "href" => "pgunit/test_collective_agreements",
                    "text" => "pgunit Test Collective Agreements",
                    "id" => "pgunit_test_collective_agreements"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for administering the application
     * @return array
     */
    private function administration() {
        return [
            "text" => "Application",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR"],
            "items" => [
                [
                    "href" => "artisan/cache_config",
                    "text" => "Cache config",
                    "id" => "cache_config"
                ],
                [
                    "href" => "artisan/view_clear",
                    "text" => "Clear views",
                    "id" => "view_clear"
                ],
                [
                    "href" => "artisan/cache_route",
                    "text" => "Cache routes",
                    "id" => "cache_route"
                ],
                [
                    "href" => "phpinfo",
                    "text" => "PHP info",
                    "id" => "php_info"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for administering users and roles
     * @return array
     */
    private function users_administration() {
        return [
            "text" => "Users",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR"],
            "items" => [
                [
                    "href" => "administer_users/view",
                    "text" => "Users/roles",
                    "id" => "edit_users_roles"
                ],
                [
                    "href" => "administer_roles/view",
                    "text" => "Roles",
                    "id" => "edit_roles"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for managing excel files
     * @return array
     */
    private function manage_excel_files() {
        return [
            "text" => "Excel",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR"],
            "items" => [
                [
                    "href" => "excel_files/view",
                    "text" => "Excel files",
                    "id" => "manage_excel_files"
                ],
                [
                    "href" => "excel_files/key",
                    "text" => "Key to data imported from Excel",
                    "id" => "excel_data_key"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for uploading files
     * @return array
     */
    private function uploader() {
        return [
            "text" => "Uploader",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR"],
            "items" => [
                [
                    "href" => "uploaded_files/view",
                    "text" => "Uploaded Files",
                    "id" => "file_list_and_uploader"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for administering reports
     * @return array
     */
    private function reports_administration() {
        return [
            "text" => "Reports Editor",
            "href" => "#",
            "roles" => ["ROLE_ADMINISTRATOR", "ROLE_REPORT_EDITOR"],
            "items" => [
                [
                    "href" => "employers/view",
                    "text" => "Employers/Org/Event",
                    "id" => "edit_employers"
                ],
                [
                    "href" => "report_headings/view",
                    "text" => "Reports Headings",
                    "id" => "edit_reports_headings"
                ],
                [
                    "href" => "reports/view",
                    "text" => "Edit Reports",
                    "id" => "edit_reports"
                ]
            ]
        ];
    }

    private function manage_db_backup_files() {
        return [
            'text' => 'DB backup files',
            'href' => '#',
            'roles' => ['ROLE_ADMINISTRATOR'],
            'items' => [
                [
                    "href" => "db_backup_files/view",
                    "text" => "DB backup files",
                    "id" => "manage_db_backup_files"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for writing only the current user's reports
     * @return array
     */
    private function reports_writing() {
        return [
            "text" => "Reports Writer",
            "href" => "#",
            "roles" => ["ROLE_REPORT_WRITER"],
            "items" => [
                [
                    "href" => "individual_reports/view",
                    "text" => "Write Reports",
                    "id" => "write_reports"
                ]
            ]
        ];
    }

    /**
     * Returns a hashed array of menu items for administering collective agreements
     * @return array
     */
    private function collective_agreements() {
        return [
            "text" => "Collective Agreements",
            "href" => "#",
            "roles" => ["ROLE_COLLECTIVE_AGREEMENT_EDITOR"],
            "items" => [
                [
                    "href" => "collective_agreements/view",
                    "text" => "Collective Agreements",
                    "id" => "edit_collective_agreements"
                ]
            ]
        ];
    }

    /**
     * Returns an array with a menu personalised for the current user.
     * The array contains 3 elements: menu, controller, roles
     * @return array
     */
    public function getMenu() {
        $controller = $this->get_controller_from_menu($this->buildMenu());
        $user = auth()->user();
        $result = [
            "menu" => $this->buildMenu(),
            "controller" => $controller,
            "roles" => $user->roles()->get()
        ];
        return $result;
    }

    /**
     * Returns the items for a menu.
     * @return array
     */
    private function buildMenu() {
        $menu = [
            "items" => $this->buildMenuData()
        ];
        return $menu;
    }

    /**
     * Sets the text to be used to display for each menu item. Adds the user's givenname
     * @param string $menu_item
     * @param string $key
     * @return string
     */
    private function setText($menu_item, $key) {
        $text = $menu_item["text"];
        if (strpos($key, "my_") !== false) {
            $menu_item["text"] = $this->user->givenname .
                    "'s " .
                    $text;
        }
        //$menu_item["text"] = $menu_item["role"] . ' ' . $menu_item["text"];
        return $menu_item;
    }

    /**
     * Checks the current user's roles and determines which menu items to include.
     * @param array $item
     * @param array $menu_item
     * @param string $key
     * @return array
     */
    private function loopThroughRoles($item, $menu_item, $key) {
        $roles = $menu_item["roles"];
        $user = auth()->user();
        $keys = [];
        $app_name = config('app.name');
        foreach ($roles as $role) {

            if ($user->hasRole(config($app_name . "." . $role))) {
                if (!array_key_exists($key, $keys)) {
                    array_push($item, $this->setText($menu_item, $key));
                    $keys[$key] = $key;
                }
            }
        }
        return $item;
    }

    /**
     * Builds the menu items array depending on which roles the current user belongs to.
     * @return array
     */
    private function buildMenuData() {
        $item = [];
        foreach ($this->menu() as $key => $menu_item) {
            if (key_exists("roles", $menu_item)) {
                $item = $this->loopThroughRoles($item, $menu_item, $key);
            }
        }
        return $item;
    }

    /**
     * In this version we build a hash map of all active nodes. For each node with a URL
     * we add an object to controller, assigning the href preperty as the name of the object and an
     * array of URLs to the path property. The path is needed to build the breadcrumbs display, even
     * though in this app I haven't added breadcrumbs.
     * @param array $menu_node
     * @param array $controller
     * @return array
     */
    public function get_controller_from_menu($menu_node, $controller = []) {
        foreach ($menu_node["items"] as $node) {
            $path = [];
            if (array_key_exists("path", $menu_node)) {
                $path = $menu_node["path"];
            }
            $path[] = [
                "href" => $node["href"],
                "text" => $node["text"]
            ];
            $node["path"] = $path;
            if ($node["href"] != "#") {
                $controller[$node["href"]] = $node;
            }
            if (array_key_exists("items", $node)) {
                if (count($node["items"]) > 0) {
                    $controller = $this->get_controller_from_menu($node, $controller);
                }
            }
        }
        return $controller;
    }

}
