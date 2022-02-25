var tSales;
$(document).ready(function(){
    var is_budget = $('#is_budget').val();
    var csrfToken = $('#csrf-token').val();
    tSales = $('#table-cpx').DataTable({
        responsive : true,
        processing : true,
        serverSide : true,
        ajax : SITE_URL + '/cpx/get_data',
   //      fnDrawCallback : function (oSettings) {
   //          budgetStatusStyler();
   //          budgetClosingStyler();
   //          budgetView();
			// if(is_budget==1){
			// 	xeditClasser();
			// 	initEditable();
			// 	initSelectable();
			// }
   //      },
        columns : [
            { data: 'budget_no', name: 'budget_no'},
            { data: 'line', name: 'line'},
            { data: 'profit_center', name: 'profit_center'},
            { data: 'profit_center_code', name: 'profit_center_code'},
            { data: 'cost_center', name: 'cost_center'},
            { data: 'type', name: 'type'},
            { data: 'project_name', name: 'project_name'},
            { data: 'import_domestic', name: 'import_domestic'},
            { data: 'items_name', name: 'items_name'},
            { data: 'equipment', name: 'equipment'},
            { data: 'qty', name: 'qty'},
            { data: 'curency', name: 'curency'},
            { data: 'original_price', name: 'original_price'},
            { data: 'exchange_rate', name: 'exchange_rate'},
            { data: 'price', name: 'price'},
            { data: 'sop', name: 'sop'},
            { data: 'first_dopayment_term', name: 'first_dopayment_term'},
            { data: 'first_dopayment_amount', name: 'first_dopayment_amount'},
            { data: 'final_payment_term', name: 'final_payment_term'},
            { data: 'final_payment_amount', name: 'final_payment_amount'},
            { data: 'owner_asset', name: 'owner_asset'},
            { data: 'april', name: 'april'},
            { data: 'mei', name: 'mei'},
            { data: 'juni', name: 'juni'},
            { data: 'juli', name: 'juli'},
            { data: 'agustus', name: 'agustus'},
            { data: 'september', name: 'september'},
            { data: 'oktober', name: 'oktober'},
            { data: 'november', name: 'november'},
            { data: 'december', name: 'december'},
            { data: 'januari', name: 'januari'},
            { data: 'februari', name: 'februari'},
            { data: 'maret', name: 'maret'},
            {
                data: null,
                className: "center",
                orderable: false,
                searchable: false,
                render: function(data){
                    if (is_budget == 1) {
                        return '<button class="btn btn-danger btn-xs" data-toggle="tooltip"  title="Hapus" onclick="on_delete('+data.id+')"><i class="mdi mdi-close"></i></button> <form action="/capex/'+data.id+'" method="POST" id="form-delete-'+data.id+'" style="display:none"><input type="hidden" name="_token" value="'+csrfToken+'"><input type="hidden" name="_method" value="DELETE"></form>'
                    } else {
                        return '';
                    }
                }
            }
        ],
        drawCallback: function(d) {
        	$('[data-toggle="popover"]').popover();
        }
    });

    $('#btn-confirm').click(function(){
        var sales_id = $(this).data('value');
        $('#form-delete-' + sales_id).submit();
    });

});