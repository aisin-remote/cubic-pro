var budget_no = "{{ key($budget_no) }}";
var tCapex;
$(document).ready(function(){

    tCapex = $('#table-cip').DataTable({
        ajax: SITE_URL + 'cip/settlement/list',
        "fnDrawCallback": function (oSettings) {
            budgetStatusStyler();
            budgetClosingStyler();
            budgetView();
            xeditClasser();
            initEditable();
            initSelectable();
            
        },
        columns: [
            { data: 'budget_no', name: 'budget_no'},
            { data: 'equipment_name', name: 'equipment_name'},
            { data: 'budget_plan', name: 'budget_plan'},
            { data: 'budget_used', name: 'budget_used'},
            { data: 'budget_remaining', name: 'budget_remaining'},
            { data: 'plan_gr', name: 'plan_gr'},
            { data: 'status', name: 'status'},
            { data: 'is_closed', name: 'is_closed'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(d) {
            $('[data-toggle="popover"]').popover();
        }
    });

    $('#btn-confirm').click(function(){
        var capex_id = $(this).data('value');
        $('#form-delete-' + capex_id).submit();
    });

});
var table = $('table').dataTable({
        "ajax": "{{ $table_ajax }}",
        "paging": true,
        "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>',    {{-- v3.2 by Ferry, 20150911, Pagination on top datatables --}}
        "fnDrawCallback": function (oSettings) {
            budgetClosingStyler();
        },
    }).columnFilter({
        "sPlaceHolder": "head:after",
        "aoColumns" : [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ]
    });

$(document).ready(function(){

    var data = {
            _token: '{{ csrf_token() }}',
        };
    
    $(".chosen-select").chosen();

    // If no open CIP
    if (budget_no == "") {
        $("#btn_finish").hide();
    }

    $("#cip_close").click(function(){
        $("#div_budget_no").hide();
        $("#div_asset_no").hide();
        $("#div_settlement_name").hide();
        $("#btn_finish").hide();
        $(".chosen-container").hide();
        $("#opt_budget_no").hide();

        table.api().ajax.url( "{{ url('cip/settlement/ajaxlist/tablelist/close/none') }}" ).load();
    });
    $("#cip_open").click(function(){
        $("#div_budget_no").show();
        $("#div_asset_no").show();
        $("#div_settlement_name").show();
        $("#btn_finish").show();
        $(".chosen-container").show();
        // $("#opt_budget_no").show();
        
        if (budget_no != "") {
            table.api().ajax.url( "{{ url('cip/settlement/ajaxlist/tablelist/open').'/' }}" + budget_no).load();
        }
        else {
            $("#btn_finish").hide();
            table.api().ajax.url( "{{ url('cip/settlement/ajaxlist/tablelist/open/none') }}" + budget_no).load();
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
    {{-- // if (($('#asset_no').val() == "") || ($('#settlement_name').val() == "")) {  // v3.5, Ferry,20151204, Commented --}}
    if ($('#settlement_name').val() == "") {
        alert ('Settlement name must not empty!');
    }
    else {
        var confirmed = confirm('Are you sure to finish CIP associated with budget '+budget_no+' ?');

        if (confirmed == true) {

            var data = {
                _token: "{{ csrf_token() }}",
                budget_no: budget_no,
                settlement_name: $('#settlement_name').val(),
                asset_no: $('#asset_no').val(),
            };

            $.post( "{{ url('cip/settlement/finish') }}", data, function( data ) {
                if (data.error) {
                    alert(data.error);
                    return false;
                };

                location.reload();
            });
        };
    }
}

function getBudgetDetail(value)
{
    var data = {
        _token: '{{ csrf_token() }}',
    };
    budget_no = value;

    table.api().ajax.url( "{{ url('cip/settlement/ajaxlist/tablelist/open').'/' }}" + value).load();

    // v3.5 by Ferry, 20151105, Get data CIP
    $.get( "{{ url('cip/settlement/ajaxlist/tablelist/open').'/' }}" + value, data, function( data ) {
        var cip = data.data[0];

        if (cip[0] != "") {
            $('#asset_no').val(cip[1]);
        }
    });
}

</script>