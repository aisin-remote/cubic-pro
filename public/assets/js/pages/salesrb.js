var tSales;
$(document).ready(function(){
    var is_budget = $('#is_budget').val();
    var csrfToken = $('#csrf-token').val();
    tSales = $('#table-sales').DataTable({
        responsive : true,
        processing : true,
        serverSide : true,
        ajax : SITE_URL + '/sales/get_data',
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
            { data: 'acc_code', name: 'acc_code'},
            { data: 'acc_name', name: 'acc_name'},
            { data: 'group', name: 'group'},
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
            { data: 'fy_first', name: 'fy_first'},
            { data: 'fy_second', name: 'fy_second'},
            { data: 'fy_total', name: 'fy_total'},
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