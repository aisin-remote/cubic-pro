
var budget_no = "";

$(document).ready(function(){

    var data = {
            _token: '{{ csrf_token() }}',
        };
    
    $(".chosen-select").chosen();

    // v3.5 by Ferry, 20151109, CIP Management
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
});

function getBudgetDetail(value)
{
    var data = {
        _token: '{{ csrf_token() }}',
        budget_no: value
    };

    $.post( "{{ url('capex/get') }}", data, function( data ) {
        var capex = data.data[0];

        $('#budget_name').val(capex[1]);
    });
}

function getSettlementDate(value)
{
    var data = {
        _token: '{{ csrf_token() }}',
        budget_no: value
    };

    $.post( "{{ url('cip/get') }}", data, function( data ) {
        var approvals = data.data[0];

        $('#old_settlement_date').val(approvals[4]);
    });
}

function convertCIP (budget_no) {
    if ($('#convert_settlement_date').val() == "") {
        alert ('Settlement date must not empty!');
    }
    else {
        var confirmed = confirm('Are you sure to convert one-time budget '+budget_no+' to CIP ?');

        if (confirmed == true) {

            var data = {
                _token: "{{ csrf_token() }}",
                budget_no: budget_no,
                settlement_date: $('#convert_settlement_date').val(),
            };

            $.post( "{{ url('cip/admin/convert') }}", data, function( data ) {
                if (data.error) {
                    alert(data.error);
                    return false;
                }
                else {
                    alert (data.success);
                }

                location.reload();
            });
        };
    }
}

function resettleCIP (budget_no) {
    if ($('#new_settlement_date').val() == "") {
        alert ('New settlement date must not empty!');
    }
    else {
        var confirmed = confirm('Are you sure you want to change settlement date '+budget_no+' to new settlement date : '+ $('#new_settlement_date').val() +' ?');

        if (confirmed == true) {

            var data = {
                _token: "{{ csrf_token() }}",
                budget_no: budget_no,
                old_settlement_date: $('#old_settlement_date').val(),
                new_settlement_date: $('#new_settlement_date').val(),
            };

            $.post( "{{ url('cip/admin/resettle') }}", data, function( data ) {
                if (data.error) {
                    alert(data.error);
                    return false;
                }
                else {
                    alert (data.success);
                }

                location.reload();
            });
        };
    }
}
