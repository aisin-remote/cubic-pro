var budget_no = $('#budgetno').val();
var tCapex;
$(document).ready(function(){
	budget_no = budget_no ==""?"none":budget_no;
	
	$('select[name="budget_no"]').val(budget_no).change(function(){	
			getBudgetDetail(budget_no);
	}).trigger('change');
	
    tCapex = $('#table-CIP-capex').DataTable({
        ajax: SITE_URL+"/cip/settlement/ajaxlist/tablelist/open/"+budget_no,
        "fnDrawCallback": function (oSettings) {
            budgetClosingStyler();
            
        },
        columns: [
            { data: 'budget_no', name: 'budget_no'},
            { data: 'asset_no', name: 'asset_no'},
            { data: 'cip_no', name: 'cip_no'},
            { data: 'settlement_date', name: 'settlement_date'},
            { data: 'settlement_name', name: 'settlement_name'},
            { data: 'actual_gr', name: 'actual_gr'},
            { data: 'status', name: 'status'},
        ],
        drawCallback: function(d) {
            $('[data-toggle="popover"]').popover();
        },
		
    });

});

$(document).ready(function(){
    // If no open CIP
    if (budget_no == "") {
        $("#btn_finish").hide();
    }

    $("#cip_close").click(function(){
        $("#div_budget_no").hide();
        $("#div_asset_no").hide();
        $("#div_settlement_name").hide();
        $("#btn_finish").hide();
        $("#opt_budget_no").hide();

        tCapex.ajax.url( SITE_URL+"/cip/settlement/ajaxlist/tablelist/close/none").load();
    });
    $("#cip_open").click(function(){
        $("#div_budget_no").show();
        $("#div_asset_no").show();
        $("#div_settlement_name").show();
        $("#btn_finish").show();
        
        if (budget_no != "") {
            tCapex.ajax.url( SITE_URL+"/cip/settlement/ajaxlist/tablelist/open/"+budget_no).load();
        }
        else {
            $("#btn_finish").hide();
            tCapex.ajax.url( SITE_URL+"/cip/settlement/ajaxlist/tablelist/open/none").load();
        }
    });
});

function budgetClosingStyler()
{
    $('tr > td:nth-child(7)').each(function(index, element) {
        var value = $(this).text();
        if (value == 'Open') {
            $(this).addClass('danger');
        };

        if (value == 'Close') {
            $(this).addClass('success');
        };
    })
}

function finishCIP (budget_no) {
    if ($('#opt_budget_no').val() == "" || $('#settlement_name').val() == ""||$('#asset_no').val() == "") {
		show_notification('Error','error','Budget No, Settlement name & Asset Number must not empty!');
    }
    else {
        var confirmed = confirm('Are you sure to finish CIP associated with budget '+budget_no+' ?');

        if (confirmed == true) {

            var data = {
                budget_no: budget_no,
                settlement_name: $('#settlement_name').val(),
                asset_no: $('#asset_no').val(),
            };
			$.getJSON(SITE_URL+"/cip/settlement/finish",data, function( data ) {
                if (data.error) {
					show_notification('Error','error',data.error);
                    return false;
                };
				show_notification('Success','success',data.success);
                location.reload();
            });
        };
    }
}

function getBudgetDetail(value)
{
    budget_no = value;
	if(typeof tCapex !== 'undefined'){
		tCapex.ajax.url( SITE_URL+"/cip/settlement/ajaxlist/tablelist/open/"+budget_no).load();
	}
    $.getJSON(SITE_URL+"/cip/settlement/get_approval_detail/"+budget_no,{}, function( data ) {
		$('#asset_no').val(data.asset_no);
		$('#settlement_name').val(data.settlement_name);
	}).done(function( data ) {
	
	});
}