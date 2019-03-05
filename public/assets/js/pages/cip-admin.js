
$(document).ready(function(){

    $('#new_settlement_date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        todayHighlight: true,
        autoclose: true
    });

    $('#convert_settlement_date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        todayHighlight: true,
        autoclose: true
    });
	$('#budget_no').change(function(){
		getBudgetDetail($(this).val());
	});
	$('#budget_no_cip').change(function(){
		getBudgetDetailCIP($(this).val());
	});
});

function getBudgetDetail(budget_no)
{
    $.getJSON(SITE_URL+"/cip/settlement/get_approval_detail/"+budget_no,{}, function( data ) {
		$('#budget_name').val(data.asset_no);
		$('#convert_settlement_date').val(data.settlement_date);
	}).done(function( data ) {
	
	});
}

function getBudgetDetailCIP(budget_no)
{
	$.getJSON(SITE_URL+"/cip/settlement/get_approval_detail/"+budget_no,{}, function( data ) {
		$('#old_settlement_date').val(data.settlement_date);
	}).done(function( data ) {
	
	});
}

function convertCIP (budget_no) {
    if ($('#convert_settlement_date').val() == "") {
		show_notification("Error","error","Settlement date must not empty!");
    }
    else {
        var confirmed = confirm('Are you sure to convert one-time budget '+budget_no+' to CIP ?');

        if (confirmed == true) {

            var data = {
                budget_no: budget_no,
                settlement_date: $('#convert_settlement_date').val(),
            };
			 $.getJSON(SITE_URL+"/cip/admin/convert",{}, function( data ) {
                if (data.error) {
					show_notification("Error","error",data.error);
                    return false;
                }
                else {
					show_notification("Success","success",data.success);
                }

                location.reload();
            });
        };
    }
}

function resettleCIP (budget_no) {
	/* 
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
	*/
    if ($('#new_settlement_date').val() == "") {
		show_notification("Error","error","New settlement date must not empty!");
    }
    else {
        var confirmed = confirm('Are you sure you want to change settlement date '+budget_no+' to new settlement date : '+ $('#new_settlement_date').val() +' ?');

        if (confirmed == true) {

            var data = {
                budget_no: budget_no,
                old_settlement_date: $('#old_settlement_date').val(),
                new_settlement_date: $('#new_settlement_date').val(),
            };
			$.getJSON(SITE_URL+"/cip/admin/resettle",data, function( data ) {
                if (data.error) {
					show_notification("Error","error",data.error);
                    return false;
                }
                else {
					show_notification("Success","success",data.success);
                }

                location.reload();
            });
        };
    }
}
