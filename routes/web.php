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

Route::get('/', function () {
    return redirect('dashboard');
});


Route::middleware('auth')->group(function(){

	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
	Route::get('/dashboard/get_chart', 'DashboardController@getChart')->name('dashboard.chart');
	Route::get('/dashboard/get_data_park', 'DashboardController@getDataPark')->name('dashboard.get_data_park');
	Route::get('/dashboard/get_data_user', 'DashboardController@getDataUser')->name('dashboard.get_data_user');
	Route::get('/dashboard/revoke/{user_id}', 'DashboardController@revoke')->name('dashboard.revoke');

	Route::post('/user/validate', 'UserController@validatePost');
	Route::get('/user/export', 'UserController@export')->name('user.export');
	Route::post('/user/import', 'UserController@import')->name('user.import');
	Route::get('/user/tes', 'UserController@tes');
	Route::resource('/user', 'UserController');
	Route::post('/menu/bulk_edit', 'MenuController@bulkEdit');
	Route::resource('menu', 'MenuController');

	// Master Division ARK. Ipan Herdiansyah
	Route::get('division/get_data', 'DivisionController@getData');
	Route::get('division/get_department_by_division/{division_id}', 'DivisionController@getDepartmentByDivision');
	Route::resource('division', 'DivisionController');

	// Master Department ARK. Ipan Herdiansyah
	Route::get('department/get_data', 'DepartmentController@getData');
	Route::resource('department', 'DepartmentController');

	// Master Department ARK. Ipan Herdiansyah
	Route::get('section/get_data', 'SectionController@getData');
	Route::resource('section', 'SectionController');

	// Master Department ARK. Ipan Herdiansyah
	Route::get('customer/get_data', 'CustomerController@getData');
	Route::resource('customer', 'CustomerController');

	// Master Supplier ARK. Ipan Herdiansyah
	Route::get('supplier/get_data', 'SupplierController@getData');
	Route::resource('supplier', 'SupplierController');


	// Master Part Category ARK. Ipan Herdiansyah
	Route::get('part/get_data', 'PartController@getData');
	Route::resource('part', 'PartController');
	Route::post('/part/import', 'PartController@import')->name('part.import');
	Route::get('/part/export', 'PartController@export')->name('part.export');
	
	// Master SYSTEM ARK. Ipan Herdiansyah
	Route::get('system/get_data', 'SystemController@getData');
	Route::resource('system', 'SystemController');

	// Upload Bom Finish Good
	Route::get('bom/get_data', 'BomController@getData');
	Route::get('bom/details-data/{id}', 'BomController@getDetailsData');
	Route::get('/bom/export', 'BomController@export')->name('bom.export');
	Route::post('/bom/import', 'BomController@import')->name('bom.import');
	Route::get('bom/temporary', 'BomController@temporary')->name('bom.temporary');
	Route::get('bom/details-data/{id}', 'BomController@getDetailsData');
	Route::get('bom/get_data_temporary', 'BomController@getData_temporary');
	Route::get('bom/details-datatemp/{id}', 'BomController@getDetails_temporary');
	Route::get('bom/temporary/cancel', 'BomController@cancel')->name('bom.temporary.cancel');
	Route::get('bom/temporary/save', 'BomController@save')->name('bom.temporary.save');
	Route::get('bom/details-data-session', 'BomController@getDataBom');
	Route::resource('bom', 'BomController');

	// Get Data Bom Detail
	Route::get('bom_datas/get_data', 'BomDatasController@getData');
	Route::post('bom_datas/store', 'BomDatasController@store')->name('bom_datas.store');
	Route::delete('bom_datas/{id}', 'BomDatasController@destroy')->name('bom_datas.destroy');
	Route::get('bom_datas/{id}', 'BomDatasController@show')->name('bom_datas.show');
	// Get Data Bom Semi Detail
	Route::get('bom_semi_datas/get_data', 'BomSemiDatasController@getData');
	Route::post('bom_semi_datas/store', 'BomSemiDatasController@store')->name('bom_semi_datas.store');
	// Route::post('bom_semi_datas/update{id}', 'BomSemiDatasController@update')->name('bom_semi_datas.update');
	Route::post('bom_semi_datas/destroy', 'BomSemiDatasController@store')->name('bom_semi_datas.destroy');
	Route::get('bom_semi_datas/{id}', 'BomSemiDatasController@show')->name('bom_semi_datas.show');
	
	// Upload Bom Semi Finish Good
	Route::get('bom_semi/get_data', 'BomSemiController@getData');
	Route::get('bom_semi/details-data/{id}', 'BomSemiController@getDetailsData');
	Route::get('bom_semi/get_data_temporary', 'BomSemiController@getData_temporary');
	Route::get('bom_semi/details-datatemp/{id}', 'BomSemiController@getDetails_temporary');
	Route::get('/bom_semi/export', 'BomSemiController@export')->name('bom_semi.export');
	Route::get('bom_semi/temporary', 'BomSemiController@temporary')->name('bom_semi.temporary');
	Route::get('bom_semi/temporary/cancel', 'BomSemiController@cancel')->name('bom_semi.temporary.cancel');
	Route::get('bom_semi/temporary/save', 'BomSemiController@save')->name('bom_semi.temporary.save');
	Route::post('/bom_semi/import', 'BomSemiController@import')->name('bom_semi.import');
	Route::post('/bom_semi/update{}', 'BomSemiController@update')->name('bom_semi.update');
	Route::resource('bom_semi', 'BomSemiController');
	
	// Upload  Master Price
	Route::get('masterprice/get_data', 'MasterPriceController@getData');
	Route::get('masterprice/get_data_temporary', 'MasterPriceController@getData_temporary');
	Route::get('/masterprice/export', 'MasterPriceController@export')->name('masterprice.export');
	Route::post('/masterprice/import', 'MasterPriceController@import')->name('masterprice.import');
	Route::get('masterprice/temporary', 'MasterPriceController@temporary')->name('masterprice.temporary');
	Route::get('masterprice/temporary/cancel', 'MasterPriceController@cancel')->name('masterprice.temporary.cancel');
	Route::get('masterprice/temporary/save', 'MasterPriceController@save')->name('masterprice.temporary.save');
	Route::resource('masterprice', 'MasterPriceController');
	
	// Upload Sales Data
	Route::get('salesdata/get_data', 'SalesDataController@getData');
	Route::get('salesdata/get_data_temporary', 'SalesDataController@getData_temporary');
	Route::get('/salesdata/export', 'SalesDataController@export')->name('salesdata.export');
	Route::post('/salesdata/import', 'SalesDataController@import')->name('salesdata.import');
	Route::get('salesdata/temporary', 'SalesDataController@temporary')->name('salesdata.temporary');
	Route::get('salesdata/temporary/cancel', 'SalesDataController@cancel')->name('salesdata.temporary.cancel');
	Route::get('salesdata/temporary/save', 'SalesDataController@save')->name('salesdata.temporary.save');
	Route::resource('salesdata', 'SalesDataController');

	// Upload Price Catalog
	Route::get('price_catalogue/get_data', 'MasterPriceCatalogController@getData');
	Route::get('price_catalogue/get_data_temporary', 'MasterPriceCatalogController@getData_temporary');
	Route::get('/price_catalogue/export', 'MasterPriceCatalogController@export')->name('price_catalogue.export');
	Route::post('/price_catalogue/import', 'MasterPriceCatalogController@import')->name('price_catalogue.import');
	Route::get('price_catalogue/temporary', 'MasterPriceCatalogController@temporary')->name('price_catalogue.temporary');
	Route::get('price_catalogue/temporary/cancel', 'MasterPriceCatalogController@cancel')->name('price_catalogue.temporary.cancel');
	Route::get('price_catalogue/temporary/save', 'MasterPriceCatalogController@save')->name('price_catalogue.temporary.save');
	
	Route::resource('price_catalogue', 'MasterPriceCatalogController');
	
	// Upload Budget Planning
	Route::get('budgetplanning/get_data', 'BudgetPlanningController@getData');
	Route::get('budgetplanning/get_data_temporary', 'BudgetPlanningController@getData_temporary');
	Route::get('/budgetplanning/export', 'BudgetPlanningController@export')->name('budgetplanning.export');
	Route::post('/budgetplanning/import', 'BudgetPlanningController@import')->name('budgetplanning.import');
	Route::get('budgetplanning/temporary/cancel', 'BudgetPlanningController@cancel')->name('budgetplanning.temporary.cancel');
	Route::get('budgetplanning/temporary', 'BudgetPlanningController@temporary')->name('budgetplanning.temporary');
	Route::get('budgetplanning/temporary/save', 'BudgetPlanningController@save')->name('budgetplanning.temporary.save');
	Route::resource('budgetplanning', 'BudgetPlanningController');
	
	Route::get('output_master/get_data', 'OutputMasterController@getData');
	Route::get('output_master/download', 'OutputMasterController@download')->name('output_master.download');
	Route::resource('output_master', 'OutputMasterController');

	Route::prefix('settings')->group(function(){

		Route::get('role/get_data', 'RoleController@getData');
		Route::resource('role', 'RoleController');

		Route::get('permission/get_data', 'PermissionController@getData');
		Route::resource('permission', 'PermissionController');
	});

	
	Route::get('faq/get_data', 'FaqController@getData');
	Route::resource('faq', 'FaqController');

	Route::get('help/get_data', 'HelpController@getData');
	Route::resource('help', 'HelpController');
	
	Route::get('bom/get_data', 'BomController@getData');
	Route::get('/bom/export', 'BomController@export')->name('bom.export');
	Route::post('/bom/import', 'BomController@import')->name('bom.import');
	Route::resource('bom', 'BomController');
	
	
	Route::resource('/settings', 'SettingController');

});


Route::get('/tes', 'MasterPriceController@tes');

Auth::routes();
