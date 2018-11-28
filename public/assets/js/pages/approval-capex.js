var tCapex;
$(document).ready(function(){

    tCapex = $('#table-approval-capex').DataTable({
        ajax: SITE_URL + '/approval/get_list/cx/need_approval',
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

   

    function budgetStatusStyler()
    {
        $('tr > td:nth-child(5)').each(function(index, element) {    
            var value = $(this).text();

            if (value == 'All underbudget') {
                $(this).addClass('success');
            };

            if (value == 'Overbudget exist') {
                $(this).addClass('danger');
            };
        })
    }

    function cancelApproval(approval_number)
    {
        var confirmed = confirm('Are you sure to cancel Approval: '+approval_number+'?');

        if (confirmed == true) {
            $('#'+approval_number).hide();
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

        // return false;
    }

    function validateApproval(approval_number)
    {
        var confirmed = confirm('Are you sure to validate Approval: '+approval_number+'?');

        if (confirmed == true) {
            $('#'+approval_number).hide();
            var data = {
                _token: "{{ csrf_token() }}",
                approval_number: approval_number
            };

            $.post( "{{ url('approval/approve') }}", data, function( data ) {
                if (data.error) {
                    alert(data.error);
                    return false;
                };

                console.log('update data table');
                table.api().ajax.reload( null, false );
            });
        };

        // return false;
    }
    