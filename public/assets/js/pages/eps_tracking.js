var tType;
$(document).ready(function(){

	tType = $('#table-eps_tracking').DataTable({
		ajax: SITE_URL + '/eps_tracking/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'approval_number', name: 'approval_number'},
            { data: 'user_create', name: 'user_create'},
            { data: 'approval_budget', name: 'approval_budget'},
            { data: 'approval_dep_head', name: 'approval_dep_head'},
            { data: 'approval_div_head', name: 'approval_div_head'},
            { data: 'approval_dir', name: 'approval_dir'},
            { data: 'pr_receive', name: 'pr_receive'},
            { data: 'item_code', name: 'item_code'},
            { data: 'item_description', name: 'item_description'},
            { data: 'actual_qty', name: 'actual_qty', class:'autonumeric text-right'},
            { data: 'pr_uom', name: 'pr_uom'},
            { data: 'actual_price_user', name: 'actual_price_user', class:'autonumeric text-right'},
            { data: 'supplier_name', name: 'supplier_name'},
            { data: 'po_date', name: 'po_date'},
            { data: 'po_number', name: 'po_number'},
            
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});

});