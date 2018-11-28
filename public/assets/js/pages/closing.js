
$(document).ready(function(){
    $('.chosen-select').chosen();
});

var table = $('table').dataTable({
        "ajax": "{{ url('capex/get_list/closing') }}",
        "paging": true,
        processing: true,
        serverSide: true,
        "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>',    {{-- v3.2 by Ferry, 20150911, Pagination on top datatables --}}
        "fnDrawCallback": function (oSettings) {
            budgetStatusStyler();
            budgetClosingStyler();
        },
        columns: [
            {data: 'budget_no', name: 'budget_no'},
            {data: 'equipment_name', name: 'equipment_name', width:"10%"},
            {data: 'budget_plan', name: 'budget_plan'},
            {data: 'budget_used', name: 'budget_used'},
            {data: 'budget_remaining', name: 'budget_remaining'},
            {data: 'plan_gr', name: 'plan_gr'},
            {data: 'status', name: 'status',orderable: false, searchable: false},
            {data: 'is_closed', name: 'is_closed',orderable: false, searchable: false},
            {data: 'action', name: 'action',orderable: false, searchable: false },
        ],
    })
    
function budgetStatusStyler()
{
    $('tr > td:nth-child(7)').each(function(index, element) {
        var value = $(this).text();
        if (value == 'Underbudget') {
            $(this).addClass('success');
        };

        if (value == 'Overbudget') {
            $(this).addClass('danger');
        };
    })
}

function budgetClosingStyler()
{
    $('tr > td:nth-child(8)').each(function(index, element) {
        var value = $(this).text();
        if (value == 'Open') {
            $(this).addClass('info');
        };

        if (value == 'Closed') {
            $(this).addClass('active');
        };
    })
}

function checkAll (bx) {
    // var checkBoxes = $('.budget_number');
    // checkBoxes.prop("checked", !checkBoxes.prop("checked"));

    // v3.2 by Ferry, 20150911, Bug fix select all checkboxes
    $(':checkbox').each(function() {
        this.checked = bx.checked;                        
    });
}

function recount () {
    if ($('.budget_number:checked').size() < $('.budget_number').size()) {
        $('#checkall').prop("checked", false);
    }else{
        $('#checkall').prop("checked", true);
    }
}

function closing(status) {
    var data = {
        _token: "{{ csrf_token() }}",
        status: status,
        budget_number: $( "form" ).serializeArray()
    };

    $('button').button('loading');

    if ($('.budget_number:checked').size() <= 0) {
        alert('Check at least one.');
        $('button').button('reset');
        return false;
    };

    $.post( "{{ url('capex/closing') }}", data, function( data ) {

    }).done(function( data ) {
        if (data.error) {
            alert(data.error);
        };

        table.api().ajax.reload( null, false );
        $('button').button('reset');
        $('#checkall').prop("checked", false);   {{-- v3.1 by Ferry, 20150908, Fix bug --}}
    });
}

