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
use App\ApprovalMaster;

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

	// Master Period ARK. Ipan Herdiansyah
	Route::get('period/get_data', 'PeriodController@getData');
	Route::resource('period', 'PeriodController');

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

	// Master Item Category ARK. Ipan Herdiansyah
	Route::get('item_category/get_data', 'ItemCategoryController@getData');
	Route::resource('item_category', 'ItemCategoryController');

	// Master Item ARK. Ipan Herdiansyah
	Route::get('item/get_data', 'ItemController@getData');
	Route::resource('item', 'ItemController');
	Route::post('/item/import', 'ItemController@import')->name('item.import');
	Route::get('/item/export', 'ItemController@export')->name('item.export');

	
	// Menu Capex
	// Route::get('approval/{type}', 'ApprovalController@index');

	Route::get('cip/admin/list', 'ApprovalController@getCIPAdminList');
	Route::get('cip/settlement/list', 'ApprovalController@getCip');
	Route::get('approval/create/cx', 'ApprovalController@createApproval')->name('approval-capex.index');
	Route::get('approval/create/cx/add', 'ApprovalController@create')->name('approval-capex.create');
	Route::get('approval/cx/store', 'ApprovalController@store')->name('approval-capex.store');
	Route::get('capex/get_data', 'CapexController@getData');
	Route::post('capex/xedit', 'CapexController@xedit');
	Route::get('capex/upload', 'CapexController@upload');
	Route::post('/capex/import', 'CapexController@import')->name('capex.import');
	Route::post('/capex/template', 'CapexController@template')->name('capex.template');

	Route::get('capex/get/{id}', 'ApprovalCapexController@getOne');
	Route::get('capex/getAsset/{id}', 'ApprovalCapexController@getAsset');
	Route::resource('capex', 'CapexController');
	
	// Menu Unbudget
	Route::resource('unbudget', 'UnbudgetController');
	// Route::get('approval/ub/', 'ApprovalController@approvalUnbudget');
	// Route::get('approval/create/ub', 'ApprovalController@createApprovalUnbudget');
	// Route::get('approval/create/ub/add', 'ApprovalController@createUnbudget')->name('approval-unbudget.create');
	Route::get('approval/ub/store', 'ApprovalController@store')->name('approval-unbudget.store');
	
	// Menu Expense
	// Route::get('approval/ex/', 'ApprovalController@approvalExpense')->name('approval-expense.ListApproval');
	Route::get('approval/create/ex', 'ApprovalController@createApprovalExpense')->name('approval-expense.index');
	Route::get('approval/create/ex/add', 'ApprovalController@createExpense')->name('approval-expense.create');
	Route::get('approval/ex/store', 'ApprovalController@store')->name('approval-expense.store');
	Route::get('expense/get_data', 'ExpenseController@getData');
	Route::post('expense/xedit', 'ExpenseController@xedit');
	Route::get('expense/upload', 'ExpenseController@upload');
	Route::post('/expense/import', 'ExpenseController@import')->name('expense.import');
	Route::post('/expense/template', 'ExpenseController@template')->name('expense.template');
	Route::get('expense/get/{id}', 'ApprovalExpenseController@getOne');
	Route::get('expense/getGlGroup/{id}', 'ApprovalExpenseController@getGlGroup');

	Route::resource('expense', 'ExpenseController');

	// Upload Bom Finish Good
	Route::get('bom/get_data', 'BomController@getData');
	Route::get('bom/details-data/{id}', 'BomController@getDetailsData');
	Route::get('/bom/export', 'BomController@export')->name('bom.export');
	Route::get('/bom/export/template', 'BomController@template_bom')->name('bom.template');

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
	
	// Get Data Bom Semi Detail
	Route::get('bom_semi_datas/get_data', 'BomSemiDatasController@getData');
	Route::post('bom_semi_datas/store', 'BomSemiDatasController@store')->name('bom_semi_datas.store');
	Route::delete('bom_semi_datas/{id}', 'BomSemiDatasController@destroy')->name('bom_semi_datas.destroy');

	
	// Upload Bom Semi Finish Good
	Route::get('bom_semi/get_data', 'BomSemiController@getData');
	Route::get('bom_semi/details-data/{id}', 'BomSemiController@getDetailsData');
	Route::get('bom_semi/get_data_temporary', 'BomSemiController@getData_temporary');
	Route::get('bom_semi/details-datatemp/{id}', 'BomSemiController@getDetails_temporary');
	Route::get('/bom_semi/export', 'BomSemiController@export')->name('bom_semi.export');
	Route::get('bom_semi/temporary', 'BomSemiController@temporary')->name('bom_semi.temporary');
	Route::get('/bom_semi/export/template', 'BomSemiController@templateBomSemi')->name('bom_semi.template');
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
	Route::get('/masterprice/export/template', 'MasterPriceController@templateMasterPrice')->name('masterprice.template');
	Route::get('masterprice/temporary/cancel', 'MasterPriceController@cancel')->name('masterprice.temporary.cancel');
	Route::get('masterprice/temporary/save', 'MasterPriceController@save')->name('masterprice.temporary.save');
	Route::resource('masterprice', 'MasterPriceController');
	
	// Upload Sales Data
	Route::get('salesdata/get_data', 'SalesDataController@getData');
	Route::get('salesdata/get_data_temporary', 'SalesDataController@getData_temporary');
	Route::get('/salesdata/export', 'SalesDataController@export')->name('salesdata.export');
	Route::post('/salesdata/import', 'SalesDataController@import')->name('salesdata.import');
	Route::get('salesdata/temporary', 'SalesDataController@temporary')->name('salesdata.temporary');
	Route::get('/salesdata/export/template', 'SalesDataController@templateSalesData')->name('salesdata.template');

	Route::get('salesdata/temporary/cancel', 'SalesDataController@cancel')->name('salesdata.temporary.cancel');
	Route::get('salesdata/temporary/save', 'SalesDataController@save')->name('salesdata.temporary.save');
	Route::resource('salesdata', 'SalesDataController');

	// Upload Price Catalog
	Route::get('price_catalogue/get_data', 'MasterPriceCatalogController@getData');
	Route::get('price_catalogue/get_data_temporary', 'MasterPriceCatalogController@getData_temporary');
	Route::get('/price_catalogue/export', 'MasterPriceCatalogController@export')->name('price_catalogue.export');
	Route::post('/price_catalogue/import', 'MasterPriceCatalogController@import')->name('price_catalogue.import');
	Route::get('price_catalogue/temporary', 'MasterPriceCatalogController@temporary')->name('price_catalogue.temporary');
	Route::get('/price_catalogue/export/template', 'MasterPriceCatalogController@templatePriceCatalog')->name('price_catalogue.template');
	Route::get('price_catalogue/temporary/cancel', 'MasterPriceCatalogController@cancel')->name('price_catalogue.temporary.cancel');
	Route::get('price_catalogue/temporary/save', 'MasterPriceCatalogController@save')->name('price_catalogue.temporary.save');
	
	Route::resource('price_catalogue', 'MasterPriceCatalogController');
	// Route::get('approval/get_list/{type}/{status}', function($type, $status){
    
 //    return ApprovalMaster::get_list($type, $status);
	// });
	Route::get('statistic/{budget_type}', 'DashboardController@buildJSON_ApprovalStat');
	Route::get('approval/get_list/{type}/{status}', 'ApprovalController@get_list');

	// Upload Budget Planning
	Route::get('budgetplanning/get_data', 'BudgetPlanningController@getData');
	Route::get('budgetplanning/get_data_temporary', 'BudgetPlanningController@getData_temporary');
	Route::get('/budgetplanning/export', 'BudgetPlanningController@export')->name('budgetplanning.export');
	Route::post('/budgetplanning/import', 'BudgetPlanningController@import')->name('budgetplanning.import');
	Route::get('budgetplanning/temporary/cancel', 'BudgetPlanningController@cancel')->name('budgetplanning.temporary.cancel');
	Route::get('/budgetplanning/export/template', 'BudgetPlanningController@templateBudget')->name('budgetplanning.template');
	Route::get('budgetplanning/temporary', 'BudgetPlanningController@temporary')->name('budgetplanning.temporary');
	Route::get('budgetplanning/temporary/save', 'BudgetPlanningController@save')->name('budgetplanning.temporary.save');
	Route::resource('budgetplanning', 'BudgetPlanningController');
	
	Route::get('output_master/get_data', 'OutputMasterController@getData');
	Route::get('output_master/get_sales_data/{fiscal_year}', 'OutputMasterController@getSalesData');
	Route::get('output_master/get_material/{fiscal_year}', 'OutputMasterController@getMaterial');
	Route::get('output_master/get_sales_material/{fiscal_year}', 'OutputMasterController@getSalesMaterial');
	Route::get('output_master/get_group_material/{fiscal_year}', 'OutputMasterController@getGroupMaterial');
	Route::get('output_master/download', 'OutputMasterController@download')->name('output_master.download');
	Route::resource('output_master', 'OutputMasterController');

	Route::prefix('settings')->group(function(){

		Route::get('role/get_data', 'RoleController@getData');
		Route::resource('role', 'RoleController');

		Route::get('permission/get_data', 'PermissionController@getData');
		Route::resource('permission', 'PermissionController');
	});
	
	Route::get('bom/get_data', 'BomController@getData');
	Route::get('/bom/export', 'BomController@export')->name('bom.export');
	Route::post('/bom/import', 'BomController@import')->name('bom.import');
	Route::resource('bom', 'BomController');
	
	
	Route::resource('/settings', 'SettingController');

	//Route Sap Asset 
	Route::get('asset/get_data', 'Sap\AssetController@getData');
	Route::resource('asset', 'Sap\AssetController');

	// Route SAP Cost Center
	Route::get('cost_center/get_data', 'Sap\CostCenterController@getData');
	Route::resource('cost_center', 'Sap\CostCenterController');

	// Route SAP GL Account
	Route::get('gl_account/get_data', 'Sap\GlAccountController@getData');
	Route::resource('gl_account', 'Sap\GlAccountController');

	// Route SAP Number
	Route::get('number/get_data', 'Sap\NumberController@getData');
	Route::resource('number', 'Sap\NumberController');

	// Route SAP Taxe
	Route::get('taxe/get_data', 'Sap\TaxeController@getData');
	Route::resource('taxe', 'Sap\TaxeController');

	// Route SAP Uom
	Route::get('uom/get_data', 'Sap\UomController@getData');
	Route::resource('uom', 'Sap\UomController');

	// Route SAP Uom
	Route::get('uom/get_data', 'Sap\UomController@getData');
	Route::resource('uom', 'Sap\UomController');

	// Route SAP Vendor
	Route::get('vendor/get_data', 'Sap\VendorController@getData');
	Route::resource('vendor', 'Sap\VendorController');

	// Route Manage Approval
	Route::get('manage_approval/get_data', 'ManageApprovalController@getData');
	Route::get('/master/approval/get_user', 'ManageApprovalController@getUser');
	Route::resource('manage_approval', 'ManageApprovalController');

	// Route Cart Approval Capex
	Route::post('approval-capex/store', 'ApprovalCapexController@store')->name('approval_capex.store');
	Route::post('approval-capex/cancel', 'ApprovalCapexController@cancelAjax');
	Route::get('approval/cx/', 'ApprovalCapexController@ListApproval')->name('approval-capex.ListApproval');
	Route::post('approval-capex/approval', 'ApprovalCapexController@SubmitApproval')->name('approval_capex.approval');
	Route::get('approval-capex/get_data', 'ApprovalCapexController@getData');
	Route::get('approval-capex/approval_capex', 'ApprovalCapexController@getApprovalCapex');
	Route::delete('approval-capex/{id}', 'ApprovalCapexController@destroy')->name('approval_capex.destroy');
	// Route::get('approval-capex/{id}', 'ApprovalCapexController@show')->name('approval_capex.show');
	Route::get('approval-capex/{id}', 'ApprovalCapexController@edit')->name('approval_capex.edit');
	Route::get('approval-capex/details-data/{id}', 'ApprovalCapexController@getDetailsData');


	//Route Delete List Approval 
	Route::delete('approval-capex/delete/{id}', 'ApprovalCapexController@delete')->name('approval_capex.delete');
	Route::delete('approval-expense/delete/{id}', 'ApprovalExpenseController@delete')->name('approval_expense.delete');


	// Route Cart Approval Expense
	Route::get('approval-expense/get_data', 'ApprovalExpenseController@getData');
	Route::get('approval-expense/approval_expense', 'ApprovalExpenseController@getApprovalExpense');

	Route::get('approval/ex/', 'ApprovalExpenseController@ListApproval')->name('approval-expense.ListApproval');
	Route::get('approval-expense/{id}', 'ApprovalExpenseController@show')->name('approval_expense.show');
	Route::post('approval-expense/store', 'ApprovalExpenseController@store')->name('approval_expense.store');
	Route::post('approval-expense/approval', 'ApprovalExpenseController@SubmitApproval')->name('approval_expense.approval');
	Route::delete('approval-expense/{id}', 'ApprovalExpenseController@destroy')->name('approval_expense.destroy');

	Route::get('approval/ub/', 'ApprovalController@approvalUnbudget');
	Route::get('approval/create/ub', 'ApprovalController@createApprovalUnbudget');
	Route::get('approval/create/ub/add', 'ApprovalController@createUnbudget')->name('approval-unbudget.create');
	Route::post('approval-unbudget/store', 'ApprovalUnbudgetController@store')->name('approval_unbudget.store');
	// Route::get('approval/cx/', 'ApprovalCapexController@ListApproval')->name('approval-capex.ListApproval');
	Route::post('approval-unbudget/approval', 'ApprovalUnbudgetController@SubmitApproval')->name('approval_unbudget.approval');
	Route::get('approval-unbudget/get_data', 'ApprovalUnbudgetController@getData');
	Route::get('approval-unbudget/approval_unbudget', 'ApprovalUnbudgetController@getApprovalCapex');
	Route::delete('approval-unbudget/{id}', 'ApprovalUnbudgetController@destroy')->name('approval_unbudget.destroy');
	Route::get('approval-unbudget/{id}', 'ApprovalUnbudgetController@show')->name('approval_unbudget.show');
	Route::get('approval-unbudget/details-data/{id}', 'ApprovalUnbudgetController@getDetailsData');
	Route::get('/testing', function(){
		return response()->json(\App\SalesData::sumPercTotalMaterial('apr', '2019'));
	});


});



Auth::routes();
