var tUnbudget;
$(document).ready(function(){

    tUnbudget = $('#table-approval-unbudget').DataTable({
        ajax: SITE_URL + '/approval/get_list/ub/all',
        "fnDrawCallback": function (oSettings) {
            budgetStatusStyler();
        },
        columns: [
           {data: 'name', name: 'name'},
           {data: 'approval_number', name: 'approval_number'},
           {data: 'total', name: 'total'},
           {data: 'status', name: 'status'},
           {data: 'overbudget_info', name: 'overbudget_info', orderable: false, searchable: false },
           {data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

});


function cancelApproval(approval_number)
{
    var confirmed = confirm('Are you sure to cancel Approval: '+approval_number+'?');

    if (confirmed == true) {
        var data = {
            _token: "{{ csrf_token() }}",
            approval_number: approval_number
        };

        $.post( "{{ url('approval/cancel') }}", data, function( data ) {
            if (data.error) {
                alert(data.error);
                return false;
            };

            console.log('update data table');
            table.api().ajax.reload( null, false );
        });
    };

    return false;
}