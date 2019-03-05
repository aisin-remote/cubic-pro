var tType;
$(document).ready(function(){

	tType = $('#table-eps_tracking').DataTable({
		ajax: SITE_URL + '/eps_tracking/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'fyear', name: 'fyear'},
            { data: 'approval_number', name: 'approval_number'},
            { data: 'budget_type', name: 'budget_type'},
            { data: 'budget_no', name: 'budget_no'},
            { data: 'remarks', name: 'remarks'},
            { data: 'pr_specs', name: 'pr_specs'},
            { data: 'actual_qty', name: 'actual_qty', class:'autonumeric text-right'},
            { data: 'pr_uom', name: 'pr_uom'},
            { data: 'actual_price_purchasing', name: 'actual_price_purchasing', class:'autonumeric text-right'},
            { data: 'status', name: 'status'},
            
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});

});