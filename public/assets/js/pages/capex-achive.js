$(document).ready(function(){
    $('.chosen-select').chosen();
});

var table = $('table').dataTable({
        "ajax": "{{ $table_ajax }}",
        "paging": true,
        "dom": '<"top"flp<"clear">>rt<"bottom"ip<"clear">>',    {{-- v3.2 by Ferry, 20150911, Pagination on top datatables --}}
        "aoColumnDefs": [
            {
                "aTargets": [8],
                "mData": null,
                "mRender": function (data, type, full) {
                    return '<input type="checkbox" name="budget_number" class="budget_number" value="'+full[0]+'" onClick="recount();">';
                }
            },
            {
                "targets": 8,
                "orderable": false
            }
        ],
        "fnDrawCallback": function (oSettings) {
            budgetStatusStyler();
            budgetClosingStyler();
        },
        // v3.2 by Ferry, 20150914, add custom class for coloring
        "aoColumns": [
            { "sClass": "filter_color" },
            null,
            null,
            null,
            null,
            { "sClass": "filter_color" },
        ]
        // End v3.2
    }).columnFilter({
        "sPlaceHolder": "head:after",
        "aoColumns" : [
            {"type" : "text"},
            null,
            null,
            null,
            null,
            {"type" : "text"},
            null,
            null,
            null,
        ]
    });

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

function moveArchive() {
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

    $.post( "{{ url('capex/archive') }}", data, function( data ) {

    }).done(function( data ) {
        if (data.error) {
            alert(data.error);
        };

        if (data.success) {
            alert (data.success);
        };

        table.api().ajax.reload( null, false );
        $('button').button('reset');
        $('#checkall').prop("checked", false);   {{-- v3.1 by Ferry, 20150908, Fix bug --}}
    });
}

function undoArchive() {
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

    $.post( "{{ url('capex/undoarchive') }}", data, function( data ) {

    }).done(function( data ) {
        if (data.error) {
            alert(data.error);
        };

        if (data.success) {
            alert (data.success);
        };

        table.api().ajax.reload( null, false );
        $('button').button('reset');
        $('#checkall').prop("checked", false);   {{-- v3.1 by Ferry, 20150908, Fix bug --}}
    });
}

</script>
@endsection