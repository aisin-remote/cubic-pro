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
Route::get('/', 'CatalogController@index');
Route::get('/catalog', 'CatalogController@show');
Route::post('/catalog', 'CatalogController@store');
Route::get('/catalog/compare', 'CatalogController@compare');
Route::get('/catalog/compare/print', 'CatalogController@print');
Route::get('/catalog/compare/remove/{id}', 'CatalogController@compareRemove');
Route::get('/catalog/{item_code}', 'CatalogController@details');
Route::get('/testing2', function(){
	session()->forget('compare');
});

Route::middleware('auth')->group(function(){

	Route::get('/cart', 'CartController@index');
	Route::put('/cart/{id}', 'CartController@update');
	Route::delete('/cart/{id}', 'CartController@delete');
	Route::get('cart/delete/{id}', 'CartController@deleteCart');
	Route::get('/cart/checkout', 'CartController@checkout');
	Route::get('/dashboard/getMyNotification','DashboardController@getMyNotification');
	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
	// Route::get('/dashboard/view/{group_type}','DashboardController@view');
	Route::get('/dashboard/getJSONData','DashboardController@getJSONData');
	Route::get('/dashboard/view', 'Dashboard2Controller@view');
	Route::get('/dashboard/get', 'Dashboard2Controller@get');
	// Route::get('/dashboard/get_chart', 'DashboardController@getChart')->name('dashboard.chart');
	// Route::get('/dashboard/get_data_park', 'DashboardController@getDataPark')->name('dashboard.get_data_park');
	// Route::get('/dashboard/get_data_user', 'DashboardController@getDataUser')->name('dashboard.get_data_user');
	// Route::get('/dashboard/revoke/{user_id}', 'DashboardController@revoke')->name('dashboard.revoke');
	// User 
	Route::group(['middleware' => ['permission:manage-user','auth']], function() {
		Route::post('/user/validate', 'UserController@validatePost');
		Route::get('/user/export', 'UserController@export')->name('user.export');
		Route::post('/user/import', 'UserController@import')->name('user.import');
		Route::get('/user/tes', 'UserController@tes');
		Route::resource('/user', 'UserController');
	});
	
	// Menu 
	Route::group(['middleware' => ['permission:menu','auth']], function() {
		Route::resource('menu', 'MenuController');
		Route::post('/menu/bulk_edit', 'MenuController@bulkEdit');
	});
	
	// Master Division ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:division','auth']], function() {	
		Route::get('division/get_data', 'DivisionController@getData');
		Route::get('division/get_department_by_division/{division_id}', 'DivisionController@getDepartmentByDivision');
		Route::resource('division', 'DivisionController');
	});
	
	// Master Department ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:department','auth']], function() {
		Route::get('department/get_data', 'DepartmentController@getData');
		Route::resource('department', 'DepartmentController');
	});

	// Master Section ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:section','auth']], function() {
		Route::get('section/get_data', 'SectionController@getData');
		Route::resource('section', 'SectionController');
	});

	// Master Customer ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:customer','auth']], function() {
		Route::post('/customer/import', 'CustomerController@import')->name('customer.import');
		Route::get('/customer/export/template', 'CustomerController@template_customer')->name('customer.template');
		Route::get('customer/get_data', 'CustomerController@getData');
		Route::resource('customer', 'CustomerController');
	});
	
	// Master Supplier ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:supplier','auth']], function() {
		Route::post('/supplier/import', 'SupplierController@import')->name('supplier.import');
		Route::get('/supplier/export/template', 'SupplierController@template_supplier')->name('supplier.template');
		Route::get('supplier/get_data', 'SupplierController@getData');
		Route::resource('supplier', 'SupplierController');
	});

	// Master Part Category ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:part','auth']], function() {
		Route::post('/part/import', 'PartController@import')->name('part.import');
		Route::get('/part/export', 'PartController@export')->name('part.export');
		Route::get('part/get_data', 'PartController@getData');
		Route::resource('part', 'PartController');
	});

	// Master Period ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:periode','auth']], function() {
		Route::get('period/get_data', 'PeriodController@getData');
		Route::resource('period', 'PeriodController');
	});

	//MANAGE APPROVAL
	Route::group(['middleware' => ['permission:approval','auth']], function() {
		Route::get('manage_approval/get_data', 'ManageApprovalController@getData');
		Route::get('/master/approval/get_user', 'ManageApprovalController@getUser');
		Route::get('/master/approval/get_level', 'ManageApprovalController@getLevel');
		Route::resource('manage_approval', 'ManageApprovalController');
	});
	
	
	// Master SYSTEM ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:system','auth']], function() {
		Route::get('system/get_data', 'SystemController@getData');
		Route::resource('system', 'SystemController');
	});
	
	// Master Item Category ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:item-category','auth']], function() {
		Route::get('item_category/get_data', 'ItemCategoryController@getData');
		Route::resource('item_category', 'ItemCategoryController');
	});

	// Master Item ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:item','auth']], function() {
		Route::post('/item/import', 'ItemController@import')->name('item.import');
		Route::get('/item/export', 'ItemController@export')->name('item.export');
		Route::get('item/get_data', 'ItemController@getData');
		Route::get('/item/export/template', 'ItemController@template_item')->name('item.template');
		Route::resource('item', 'ItemController');
	});
	

	// Upload PO ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:upload-po','auth']], function() {
		Route::post('/upload_po/import', 'UploadPoController@import')->name('upload_po.import');
		Route::post('upload_po/xedit', 'UploadPoController@xedit');
		Route::get('/upload_po/export', 'UploadPoController@export')->name('upload_po.export');
		Route::get('upload_po/get_data', 'UploadPoController@getData');
		Route::get('/upload_po/export/template', 'UploadPoController@template_upload_po')->name('upload_po.template');
		Route::resource('upload_po', 'UploadPoController');
	});
	
	// GR Confirm ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:gr-confirm','auth']], function() {
		Route::get('gr_confirm/get_data', 'GrConfirmController@getData');
		Route::get('gr_confirm/details-data/{id}', 'GrConfirmController@getDetailsData');		
		Route::get('gr_confirm/details-data/{id}', 'GrConfirmController@getDetailsData');
		Route::get('gr_confirm/details-data-session', 'GrConfirmController@getDataGrConfirm');
		Route::get('gr_confirm/get_user/{po_number}', 'GrConfirmController@getUser');
		Route::get('gr_confirm/email','GrConfirmController@sendEmail');
		Route::resource('gr_confirm', 'GrConfirmController');
		
		// GR Confirm Detail ARK. Ipan Herdiansyah
		Route::get('gr_confirm_detail/get_data', 'GrConfirmDetailController@getData');
		Route::post('gr_confirm_detail/xedit', 'GrConfirmDetailController@xedit');
		Route::post('gr_confirm_detail/store', 'GrConfirmDetailController@store')->name('gr_confirm_detail.store');
		Route::delete('gr_confirm_detail/{id}', 'GrConfirmDetailController@destroy')->name('gr_confirm_detail.destroy');
	});
	
	// EPS Tracking ARK. Ipan Herdiansyah
	Route::group(['middleware' => ['permission:eps-tracking','auth']], function() {
		Route::get('eps_tracking/get_data', 'EpsTrackingController@getData');
		Route::resource('eps_tracking', 'EpsTrackingController');
	});
	// media
	Route::prefix('media')->group(function(){
		Route::post('uploads', 'MediaController@uploads')->name('media.uploads');
		Route::get('get_data', 'MediaController@getData')->name('media.get_data');
		Route::get('select_data/{id}', 'MediaController@selectData')->name('media.get_data');
	});
	
	Route::resource('/media', 'MediaController');
	
	Route::group(['middleware' => ['permission:budget-upload','auth']], function() {		

		// Get Data Bom Detail
		Route::get('bom_datas/get_data', 'BomDatasController@getData');
		Route::post('bom_datas/store', 'BomDatasController@store')->name('bom_datas.store');
		Route::delete('bom_datas/{id}', 'BomDatasController@destroy')->name('bom_datas.destroy');
		
		// Get Data Bom Semi Detail
		Route::get('bom_semi_datas/get_data', 'BomSemiDatasController@getData');
		Route::post('bom_semi_datas/store', 'BomSemiDatasController@store')->name('bom_semi_datas.store');
		Route::delete('bom_semi_datas/{id}', 'BomSemiDatasController@destroy')->name('bom_semi_datas.destroy');

		
	});

	Route::group(['middleware' => ['permission:upload-sales-data','auth']], function() {
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
	});

	Route::group(['middleware' => ['permission:upload-bom-finish-goods','auth']], function() {
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
		
		Route::get('bom/get_data', 'BomController@getData');
		Route::get('/bom/export', 'BomController@export')->name('bom.export');
		Route::post('/bom/import', 'BomController@import')->name('bom.import');
		Route::resource('bom', 'BomController');
	});


	Route::group(['middleware' => ['permission:upload-bom-semi-finish-goods','auth']], function() {
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
	});

	Route::group(['middleware' => ['permission:upload-master-price-parts','auth']], function() {
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
	});
	
	Route::group(['middleware' => ['permission:output-master','auth']], function() {
		// Output Master
		Route::get('output_master/get_data', 'OutputMasterController@getData');
		Route::get('output_master/get_sales_data/{fiscal_year}', 'OutputMasterController@getSalesData');
		Route::get('output_master/get_material/{fiscal_year}', 'OutputMasterController@getMaterial');
		Route::get('output_master/get_sales_material/{fiscal_year}', 'OutputMasterController@getSalesMaterial');
		Route::get('output_master/get_group_material/{fiscal_year}', 'OutputMasterController@getGroupMaterial');
		Route::get('output_master/download', 'OutputMasterController@download')->name('output_master.download');
		Route::resource('output_master', 'OutputMasterController');
	});
	
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
	
	//STATISTIC
	Route::get('statistic/{budget_type}','ApprovalController@buildJSONApprovalStatus'); // Getting Capex Approval Data 
	// Route::get('statistic/{budget_type}', 'DashboardController@buildJSON_ApprovalStat');
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

	Route::prefix('settings')->group(function(){

		Route::get('role/get_data', 'RoleController@getData');
		Route::resource('role', 'RoleController')->middleware('permission:role','auth');

		Route::get('permission/get_data', 'PermissionController@getData');
		Route::resource('permission', 'PermissionController')->middleware('permission:user-role','auth');
		
	});	
	
	Route::resource('/settings', 'SettingController');

	//Route Sap Asset
	Route::group(['middleware' => ['permission:sap-asset','auth']], function() {
		Route::get('asset/get_data', 'Sap\AssetController@getData');
		Route::resource('asset', 'Sap\AssetController');

		Route::get('getCmbAsset','Sap\AssetController@getCmbAsset');
	});
	
	// Route SAP Cost Center
	Route::group(['middleware' => ['permission:sap-cost-center','auth']], function() {
		Route::get('cost_center/get_data', 'Sap\CostCenterController@getData');
		Route::resource('cost_center', 'Sap\CostCenterController');
		Route::get('getCmbCostCenter','Sap\CostCenterController@getCmbCostCenter');
	});
		
	// Route SAP GL Account
	Route::group(['middleware' => ['permission:sap-gl-account','auth']], function() {
		Route::get('gl_account/get_data', 'Sap\GlAccountController@getData');
		Route::resource('gl_account', 'Sap\GlAccountController');

		Route::get('getCmbGlAccount','Sap\GlAccountController@getCmbGlAccount');
	});
	
	// Route SAP Number
	Route::group(['middleware' => ['permission:sap-number','auth']], function() {
		Route::get('number/get_data', 'Sap\NumberController@getData');
		Route::resource('number', 'Sap\NumberController');
	});
	
	// Route SAP Taxe
	Route::group(['middleware' => ['permission:sap-taxes','auth']], function() {
		Route::get('taxe/get_data', 'Sap\TaxeController@getData');
		Route::resource('taxe', 'Sap\TaxeController');
		Route::get('getCmbTax','Sap\TaxeController@getCmbTax');
	});
		
	// Route SAP Uom
	Route::group(['middleware' => ['permission:sap-uom','auth']], function() {
		Route::get('uom/get_data', 'Sap\UomController@getData');
		Route::resource('uom', 'Sap\UomController');
	});

	// Route SAP Vendor
	Route::group(['middleware' => ['permission:sap-vendor','auth']], function() {
		Route::get('vendor/get_data', 'Sap\VendorController@getData');
		Route::resource('vendor', 'Sap\VendorController');
		Route::get('getCmbVendor','Sap\VendorController@getCmbVendor');
	});	
	
	// Route Link To Sap
	Route::group(['middleware' => ['permission:link-to-sap','auth']], function() {
		Route::get('link_to_sap','Sap\PrController@index');
		Route::get('pr_convert_excel/{approval_number}','Sap\PrController@pr_convert_excel');
		Route::get('approvalku/get_print/{status}','ApprovalController@get_print');
	});
	
	Route::get('/testing', function(){
		return response()->json(\App\SalesData::sumPercTotalMaterial('apr', '2019'));
	});
	
	/******* ROUTE CAPEX *******/
	
			//GET LIST CAPEX
			Route::group(['middleware' => ['permission:list-capex','auth']], function() {				
				Route::get('capex', 'CapexController@index')->name('capex.index');
				
				//AJAX GET LIST CAPEX
				Route::get('/capex/get_data', 'CapexController@getData'); 
			});
			
			//LIST APPROVAL CAPEX
			Route::group(['middleware' => ['permission:list-approval-capex','auth']], function() {
				Route::get('approval/cx/', 'ApprovalCapexController@ListApproval')->name('approval-capex.ListApproval'); 
			});
			
			//PENDING APPROVAL CAPEX
			Route::group(['middleware' => ['permission:pending-approval-capex','auth']], function() {
				
				Route::get('approval/cx/unvalidated','ApprovalCapexController@ListApprovalUnvalidated'); 
			});		
			
			// PENDING APPROVAL ACC
			Route::group(['middleware' => ['permission:pending-asset-assign-no-capex','auth']], function() {
				Route::get('approval/cx/acc', 'ApprovalCapexController@ListApprovalAcc');
			});

			//CREATE APPROVAL CAPEX	
			Route::group(['middleware' => ['permission:create-approval-capex','auth']], function() {
				Route::get('/approval/create/cx', 'ApprovalController@createApproval')->name('approval-capex.index');
				
				//CREATE ITEM UNTUK APPROVAL CAPEX
				Route::get('/approval/create/cx/add', 'ApprovalController@create')->name('approval-capex.create');
				
				//STORE DATA ITEM UNTUK APPROVAL CAPEX
				Route::get('/approval/cx/store', 'ApprovalController@store')->name('approval-capex.store');
				
				//SUBMIT APPROVAL
				Route::post('approval-capex/approval', 'ApprovalCapexController@SubmitApproval')->name('approval_capex.approval');
			});
			
			//UPLOAD CAPEX	
			Route::group(['middleware' => ['permission:upload-capex','auth']], function() {
				Route::get('capex/upload', 'CapexController@upload');
				Route::post('/capex/import', 'CapexController@import')->name('capex.import');
			});
			
			Route::post('/capex', 'CapexController@store')->name('capex.store');
			Route::get('/capex/create','CapexController@create')->name('capex.create');	
			Route::put('/capex/{capex}','CapexController@update')->name('capex.update');
			Route::delete('/capex/{capex}','CapexController@destroy')->name('capex.destroy');
			
			Route::get('/capex/{capex}/edit', 'CapexController@edit')->name('capex.edit');
			Route::post('capex/xedit', 'CapexController@xedit');
			Route::post('approval/xedit', 'ApprovalCapexController@xedit');
			
			
			Route::post('/capex/template', 'CapexController@template')->name('capex.template');

			Route::get('capex/get/{id}', 'ApprovalCapexController@getOne');
			Route::get('capex/getAsset/{id}', 'ApprovalCapexController@getAsset');
						
			//APPROVE APPROVAL
			Route::get('approval/approve','ApprovalController@approveAjax');
			
			//CANCEL APPROVAL
			Route::get('approval/cancel_approval','ApprovalController@cancelApproval');
			
			//PRINT APPROVAL 
			Route::get('approval/print_approval/{approval_number}','ApprovalController@printApproval');
			
			//PRINT APPROVAL EXCEL
			Route::get('approval/print_approval_excel/{approval_number}','ApprovalController@printApprovalExcel');
			
			//DETAIL APPROVAL CAPEX
			Route::get('approval/cx/{id}','ApprovalCapexController@DetailApproval');
			Route::get('approval/cx/unvalidate/{id}','ApprovalCapexController@DetailUnvalidateApproval');
			Route::get('approval/detail/{id}','ApprovalCapexController@AjaxDetailApproval');
			
			Route::post('approval-capex/store', 'ApprovalCapexController@store')->name('approval_capex.store');
			Route::post('approval/cancel', 'ApprovalController@cancelAjax');	
			
			Route::get('approval-capex/get_data', 'ApprovalCapexController@getData');
			Route::get('approval-capex/approval_capex/{status}', 'ApprovalCapexController@getApprovalCapex');	
			Route::get('approval-capex/{id}', 'ApprovalCapexController@edit')->name('approval_capex.edit');
			Route::get('approval-capex/details-data/{id}', 'ApprovalCapexController@getDetailsData');
			
			//DELETE APPROVAL CAPEX
			Route::delete('approval-capex/delete/{id}', 'ApprovalCapexController@delete')->name('approval_capex.delete');
			Route::delete('approval-capex/{id}', 'ApprovalCapexController@destroy')->name('approval_capex.destroy');
			
			//DETAIL CAPEX
			Route::get('/capex/select/{budget_no}','CapexController@show')->name('capex.show');
			
			//ARCHIVE
			Route::group(['middleware' => ['permission:archive-capex','auth']], function() {
				
				Route::get('capex/archive','CapexController@archive');
				Route::get('capex/get_archive','CapexController@execArchive');
				Route::get('capex/undo_archive','CapexController@execUndoArchive');
				Route::get('capex/archive/ajaxsource','CapexController@getArchiveAjaxSource');
				Route::get('capex/archive/ajaxdest','CapexController@getArchiveAjaxDestination');
				Route::get('capex/archive/list','CapexController@viewArchive');
			});
			
			//CLOSING
			Route::group(['middleware' => ['permission:closing-capex','auth']], function() {
				
				Route::get('capex/closing','CapexController@listClosing');
				Route::get('capex/get_closing/{page_name}','CapexController@getListClosing');
				Route::get('capex/closingUpdate','CapexController@closingUpdate');
			});
			
			//FISCAL YEAR
			Route::group(['middleware' => ['permission:fyear_closing','auth']], function() {
				Route::get('fyear/closing', 'CapexController@fiscalYearClosing'); 
				Route::get('fyear/doClosing', 'CapexController@doFiscalYearClosing');
			});
			
			// CIP Administrator
			Route::group(['middleware' => ['permission:cip-admin-capex','auth']], function() {
				Route::get('/cip/admin/list', 'ApprovalController@getCIPAdminList');
				Route::get('/cip/admin/convert','ApprovalController@convertToCIP');
				Route::get('/cip/admin/resettle','ApprovalController@extendResettle');
			});
			
			// CIP Settlement
			Route::group(['middleware' => ['permission:cip-settlement-capex','auth']], function() {
				Route::get('/cip/settlement/list', 'ApprovalController@getCipSettlementList');
				Route::get('/cip/settlement/ajaxlist/{control}/{status}/{filter}', 'ApprovalController@getCIPSettlementAjaxList');
				Route::get('/cip/settlement/get_approval_detail/{budget_no}','ApprovalController@getApprovalDetail');
				Route::get('/cip/settlement/finish','ApprovalController@finishCIP');
			});
	
	/******* END OF ROUTE CAPEX *******/
	
	
	/******* ROUTE EXPENSE *******/
	
			//GET LIST EXPENSE
			Route::group(['middleware' => ['permission:list-expense','auth']], function() {
				Route::get('expense', 'ExpenseController@index')->name('expense.index');
			});
			
			//LIST APPROVAL EXPENSE
			Route::group(['middleware' => ['permission:list-approval-expense','auth']], function() {
				Route::get('approval/ex/', 'ApprovalExpenseController@ListApproval')->name('approval-expense.ListApproval');
			});
			
			//PENDING APPROVAL EXPENSE
			Route::group(['middleware' => ['permission:pending-approval-expense','auth']], function() {
				Route::get('approval/ex/unvalidated','ApprovalExpenseController@ListApprovalUnvalidated'); 
			});
			
			//CREATE APPROVAL EXPENSE	
			Route::group(['middleware' => ['permission:create-approval-expense','auth']], function() {
				Route::get('approval/create/ex', 'ApprovalController@createApprovalExpense')->name('approval-expense.index');
				
				//CREATE ITEM UNTUK APPROVAL EXPENSE
				Route::get('approval/create/ex/add', 'ApprovalController@createExpense')->name('approval-expense.create');
				
				//STORE DATA ITEM UNTUK APPROVAL CAPEX
				Route::get('approval/ex/store', 'ApprovalController@store')->name('approval-expense.store');
				
				//SUBMIT APPROVAL
				Route::post('approval-expense/approval', 'ApprovalExpenseController@SubmitApproval')->name('approval_expense.approval');
			});
			
			//UPLOAD EXPENSE	
			Route::group(['middleware' => ['permission:upload-expense','auth']], function() {
				Route::get('expense/upload', 'ExpenseController@upload');
				Route::post('/expense/import', 'ExpenseController@import')->name('expense.import');
			});
			
			Route::post('expense', 'ExpenseController@store')->name('expense.store');
			Route::get('expense/create','ExpenseController@create')->name('expense.create');
			Route::get('expense/select/{budget_no}','ExpenseController@show')->name('expense.show'); // buat ambigu
			// Route::get('expense/{expense}','ExpenseController@show')->name('expense.show');
			Route::get('expense/{expense}/edit','ExpenseController@edit')->name('expense.edit');
			Route::put('expense/{expense}','ExpenseController@update')->name('expense.update');
			Route::delete('expense/{expense}','ExpenseController@destroy')->name('expense.destroy');	
			
			Route::post('expense/xedit', 'ExpenseController@xedit');
			
			
			Route::get('expense/get_data', 'ExpenseController@getData');
			Route::post('expense/xedit', 'ExpenseController@xedit');
			
			
			Route::post('/expense/template', 'ExpenseController@template')->name('expense.template');
			Route::get('expense/get/{id}', 'ApprovalExpenseController@getOne');
			Route::get('expense/getGlGroup/{id}', 'ApprovalExpenseController@getGlGroup');
			
			Route::group(['middleware' => ['permission:closing-expense','auth']], function() {
				Route::get('expense/closing','ExpenseController@listClosing');
				Route::get('expense/get_closing/{page_name}','ExpenseController@getListClosing');
				Route::get('expense/closingUpdate','ExpenseController@closingUpdate');	
			});
			
			Route::post('approvalex/getDelete/', 'ApprovalExpenseController@getDelete');
			Route::get('approval/ex/{id}','ApprovalExpenseController@DetailApproval');
			Route::get('approval/ex/unvalidate/{id}','ApprovalExpenseController@DetailUnvalidateApproval');
			Route::get('approval-expense/get_data', 'ApprovalExpenseController@getData');
			Route::get('approval-expense/approval_expense/{status}', 'ApprovalExpenseController@getApprovalExpense');
			
			Route::get('approval-expense/{id}', 'ApprovalExpenseController@show')->name('approval_expense.show');
			Route::post('approval-expense/store', 'ApprovalExpenseController@store')->name('approval_expense.store');
				
			Route::get('approval-expense/detail/{id}','ApprovalExpenseController@AjaxDetailApproval');
			
			//DELETE APPROVAL EXPENSE
			Route::delete('approval-expense/{id}', 'ApprovalExpenseController@destroy')->name('approval_expense.destroy');
			Route::delete('approval-expense/delete/{id}','ApprovalExpenseController@delete')->name('approval_expense.delete');
			
			
			//ARCHIVE EXPENSE
			Route::group(['middleware' => ['permission:archive-expense','auth']], function() {
				
				Route::get('expense/archive','ExpenseController@archive');
				Route::get('expense/get_archive','ExpenseController@execArchive');
				Route::get('expense/undo_archive','ExpenseController@execUndoArchive');
				Route::get('expense/archive/ajaxsource','ExpenseController@getArchiveAjaxSource');
				Route::get('expense/archive/ajaxdest','ExpenseController@getArchiveAjaxDestination');
				Route::get('expense/archive/list','ExpenseController@viewArchive');
			});
	
	/******* END OF ROUTE EXPENSE *******/
	
	
	/******* ROUTE UNBUDGET *******/
	
			//GET LIST EXPENSE
			Route::get('unbudget', 'UnbudgetController@index')->name('unbudget.index');
			
			//LIST APPROVAL
			Route::group(['middleware' => ['permission:list-approval-unbudget','auth']], function() {
				Route::get('approval/ub/', 'ApprovalUnbudgetController@ListApproval')->name('approval-unbudget.ListApproval');
			});
			
			//CREATE APPROVAL UNBUDGET
			Route::group(['middleware' => ['permission:create-approval-unbudget','auth']], function() {
				
				Route::get('approval/create/ub', 'ApprovalController@createApprovalUnbudget')->name('approval-unbudget.index'); 
				
				//CREATE ITEM UNTUK APPROVAL UNBUDGET
				Route::get('approval/create/ub/add', 'ApprovalController@createUnbudget')->name('approval-unbudget.create');
			});
			
			//PENDING APPROVAL UNBUDGET
			Route::group(['middleware' => ['permission:pending-approval-unbudget','auth']], function() {
				Route::get('approval/ub/unvalidated','ApprovalUnbudgetController@ListApprovalUnvalidated'); 
				
				//STORE ITEM UNTUK APPROVAL UNBUDGET
				Route::post('approval-unbudget/store', 'ApprovalUnbudgetController@store')->name('approval_unbudget.store');
				
				//SUBMIT APPROVAL
				Route::post('approval-unbudget/approval', 'ApprovalUnbudgetController@SubmitApproval')->name('approval_unbudget.approval');
			});
			
			Route::post('unbudget', 'UnbudgetController@store')->name('unbudget.store');
			Route::get('unbudget/create','UnbudgetController@create')->name('unbudget.create');
			Route::get('unbudget/select/{budget_no}','UnbudgetController@show')->name('unbudget.show');
			// Route::get('unbudget/{unbudget}','UnbudgetController@show')->name('unbudget.show');
			Route::get('unbudget/{unbudget}/edit','UnbudgetController@edit')->name('unbudget.edit');
			Route::put('unbudget/{unbudget}','UnbudgetController@update')->name('unbudget.update');
			Route::delete('unbudget/{unbudget}','UnbudgetController@destroy')->name('unbudget.destroy');
			
			// Route::get('approval-unbudget/approval_unbudget/{status}', 'ApprovalUnbudgetController@getApprovalUnbudget');
			Route::post('approvalub/getDelete/', 'ApprovalUnbudgetController@getDelete');
			
			//DETAIL APPROVAL UNBUDGET
			Route::get('approval/ub/{id}','ApprovalUnbudgetController@DetailApproval');
			Route::get('approval/ub/unvalidate/{id}','ApprovalUnbudgetController@DetailUnvalidateApproval');
			Route::get('approval-unbudget/get_data', 'ApprovalUnbudgetController@getData');			
			Route::get('approval-unbudget/approval_unbudget/{status}', 'ApprovalUnbudgetController@getApprovalUnbudget');	
			Route::get('approval-unbudget/{id}', 'ApprovalUnbudgetController@show')->name('approval_unbudget.show');
			Route::get('approval-unbudget/details-data/{id}', 'ApprovalUnbudgetController@getDetailsData');
			Route::get('approval-unbudget/detail/{id}','ApprovalUnbudgetController@AjaxDetailApproval');
			Route::get('approval/ub/store', 'ApprovalController@store')->name('approval-unbudget.store');
			
			//DELETE APPROVAL UNBUDGET
			Route::delete('approval-unbudget/{id}', 'ApprovalUnbudgetController@destroy')->name('approval_unbudget.destroy');
			Route::delete('approval-unbudget/delete/{id}', 'ApprovalUnbudgetController@delete')->name('approval_unbudget.delete');
	
	/******* END OF ROUTE UNBUDGET *******/

});

Auth::routes();
