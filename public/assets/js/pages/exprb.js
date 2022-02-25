var tSales;
$(document).ready(function(){
    var is_budget = $('#is_budget').val();
    var csrfToken = $('#csrf-token').val();
    tSales = $('#table-exp').DataTable({
        responsive : true,
        processing : true,
        serverSide : true,
        ajax : SITE_URL + '/exp/get_data',
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
            { data: 'group', name: 'group'},
            { data: 'line', name: 'line'},
            { data: 'profit_center', name: 'profit_center'},
            { data: 'profit_center_code', name: 'profit_center_code'},
            { data: 'cost_center', name: 'cost_center'},
            { data: 'acc_code', name: 'acc_code'},
            { data: 'project_name', name: 'project_name'},
            { data: 'equipment_name', name: 'equipment_name'},
            { data: 'import_domestic', name: 'import_domestic'},
            { data: 'qty', name: 'qty'},
            { data: 'cur', name: 'cur'},
            { data: 'price_per_qty', name: 'price_per_qty'},
            { data: 'exchange_rate', name: 'exchange_rate'},
            { data: 'budget_before', name: 'budget_before'},           
            { data: 'po', name: 'po'},
            { data: 'gr', name: 'gr'},
            { data: 'sop', name: 'sop'},
            { data: 'first_dopayment_term', name: 'first_dopayment_term'},
            { data: 'first_dopayment_amount', name: 'first_dopayment_amount'},
            { data: 'final_payment_term', name: 'final_payment_term'},
            { data: 'final_payment_amount', name: 'final_payment_amount'},            
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
            // { data: 'checking', name: 'checking'},
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