<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'WelcomeController@index');
Route::get('/trigger', 'WelcomeController@trigger');
Route::get('/send_ajax', 'WelcomeController@ajax');
Auth::routes();
Route::middleware(['auth'])->group(function() {
    //Can't use closures if you want to cache routes, so I've added a method 
    Route::get('authorised', 'WelcomeController@authorised');
    Route::get('navigation/menu', 'Navigation@getMenu');
    Route::get('artisan/cache_config', 'RunArtisan@cacheConfig');
    Route::get('artisan/view_clear', 'RunArtisan@clearView');
    Route::get('artisan/cache_route', 'RunArtisan@cacheRoute');
    Route::get('phpinfo', 'System@phpinfo');

    Route::get('pgunit/test_all', 'DBMetaDataController@pgunitTestAll');
    /*
    Route::get('pgunit/test_schema', 'DBMetaDataController@pgunitTestSchema');
    Route::get('pgunit/test_users', 'DBMetaDataController@pgunitTestUsers');
    Route::get('pgunit/test_roles', 'DBMetaDataController@pgunitTestRoles');
    Route::get('pgunit/test_sectors', 'DBMetaDataController@pgunitTestSectors');
    Route::get('pgunit/test_branches', 'DBMetaDataController@pgunitTestBranches');
    Route::get('pgunit/test_individual_reports', 'DBMetaDataController@pgunitTestIndividualReports');
    Route::get('pgunit/test_reports', 'DBMetaDataController@pgunitTestReports');
    Route::get('pgunit/test_employers', 'DBMetaDataController@pgunitTestEmployers');
    Route::get('pgunit/test_report_headings', 'DBMetaDataController@pgunitTestReportHeadings');
    Route::get('pgunit/test_collective_agreements', 'DBMetaDataController@pgunitTestCollectiveAgreements');
     * 
     */
    Route::get('administer_users/view', 'AdministerUsers@view');
    Route::delete('administer_users', 'AdministerUsers@delete');
    Route::post('administer_users', 'AdministerUsers@store');
    Route::put('administer_users', 'AdministerUsers@update');
    Route::put('administer_users/pwd/{id}', 'AdministerUsers@updatePassword');
    Route::post('administer_users/role', 'AdministerUsers@attachRole');
    Route::delete('administer_users/role', 'AdministerUsers@detachRole');
    Route::put('administer_users/email/{id}', 'AdministerUsers@sendEmail');

    Route::get('administer_roles/model', 'DataTableController@getModelFromPath');
    Route::get('administer_roles/view', 'EditableDataTableController@view');
    Route::delete('administer_roles', 'EditableDataTableController@delete');
    Route::post('administer_roles', 'EditableDataTableController@store');
    Route::put('administer_roles', 'EditableDataTableController@update');

    Route::get('employers/model', 'DataTableController@getModelFromPath');
    Route::get('employers/view', 'EditableDataTableController@view');
    Route::post('employers', 'EditableDataTableController@store');
    Route::put('employers', 'EditableDataTableController@update');
    Route::delete('employers', 'EditableDataTableController@delete');
    Route::get('employers/{report_id}', 'ReportsController@getAvailableEmployers');

    Route::get('report_headings/model', 'DataTableController@getModelFromPath');
    Route::get('report_headings/view', 'EditableDataTableController@view');
    Route::post('report_headings', 'EditableDataTableController@store');
    Route::put('report_headings', 'EditableDataTableController@update');
    Route::delete('report_headings', 'EditableDataTableController@delete');


    Route::get('individual_reports/model', 'DataTableController@getModelFromPath');
    Route::get('individual_reports/view', 'EditableDataTableController@view');
    Route::post('individual_reports', 'IndividualReportsController@store');
    Route::put('individual_reports', 'IndividualReportsController@update');
    Route::delete('individual_reports', 'IndividualReportsController@delete');

    Route::get('reports/model', 'DataTableController@getModelFromPath');
    Route::get('reports/view', 'EditableDataTableController@view');
    Route::post('reports', 'ReportsController@store');
    Route::post('reports/ssp', 'ReportsController@ssp');
    Route::put('reports', 'ReportsController@update');
    Route::delete('reports', 'ReportsController@delete');
    Route::put('reports/toggle_include/{report_id}', 'ReportsController@updateInclude');
    //Route::get('reports/get_excel/{from}/{to}', 'ReportsController@generateFilteredExcel');
    Route::get('reports/get_excel/{from}/{to}/{single_column}', 'ReportsController@generateFilteredExcel');
    Route::get('reports/get_excel_by_updated/{from}/{to}', 'ReportsController@generateFilteredExcelByUpdatedDate');
    Route::get('reports/get_all_excel', 'ReportsController@generateAllExcel');
    Route::get('reports/get_available_officers/{report_id}', 'ReportsController@getAvailableOfficers');
    
});