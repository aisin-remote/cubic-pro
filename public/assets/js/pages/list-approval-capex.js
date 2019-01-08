var tApprovalCapex;
$(document).ready(function(){

	tApprovalCapex = $('#table-approval-capex').DataTable({
		ajax: SITE_URL + '/approval-capex/approval_capex',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'departments.department_name', name: 'departments.department_name'},
            { data: 'approval_number', name: 'approval_number'},
            { data: 'total', name: 'total'},
            { data: 'status', name: 'status'},
            {data: 'overbudget_info', name: 'overbudget_info', orderable: false, searchable: false },
            // { data: 'model', name: 'model'},
            //  { data: 'reject_ratio', name: 'reject_ratio'},
            { data: 'action', name: 'action', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-approval-capex tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tApprovalCapex.row(tr);
        var tableId = 'posts-' + row.data().id;

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(template(row.data())).show();
            initTable(tableId, row.data());
            tr.addClass('shown');
            tr.next().find('td').addClass('no-padding bg-gray');
        }
    });

    function initTable(tableId, data) {
        $('#' + tableId).DataTable({
            /*processing: true,
            serverSide: true,*/
            ajax: data.details_url,
            columns: [
               { data: 'budget_no', name: 'budget_no'},
               { data: 'project_name', name: 'project_name'},
               { data: 'asset_kind', name: 'asset_kind'},
               { data: 'asset_category', name: 'asset_category'},
               { data: 'sap_costs.cc_fname', name: 'sap_costs.cc_fname'},
            ],
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });
    }

     function template(d) {

        // console.log(d);

        return `

                <table class="table details-table" id="posts-${d.id}">
                    <thead>
                    <tr>
                        <th>Budget No</th>
                        <th>Project Name</th>
                        <th>Asset Kind</th>
                        <th>Asset Category</th>
                        <th>Cost Center</th>
                    </tr>


            </table>

        `;
    }

    $('#btn-confirm').click(function(){
        var approval_capex_id = $(this).data('value');
        $('#form-delete-' + approval_capex_id).submit();
    });

    

});

function on_delete(approval_capex_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', approval_capex_id);
}
function printApproval(approval_number)
{
    var data = {
        _token: "{{ csrf_token() }}",
        approval_number: approval_number
    };

    $.post( "{{ url('approval/print') }}", data, function( data ) {
        if (data.error) {
            alert(data.error);
            return false;
        };
         //dev-4.0 by yudo, 20161214, mengubah link ke print_excel
        window.location.replace("{{ url('approval/print_excel') }}/"+approval_number);
    });

    return false;
}

function cancelApproval(approval_number)
{
  // var formdata = $('#table-approval-capex').serializeArray();
  var confirmed = confirm('Are you sure to cancel Approval: '+approval_number+'?');
  $.ajax({
    url: SITE_URL + '/approval-capex/cancel',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'post',
    dataType: 'json',
    data: approval_number,
    success: function(res) {
      show_notification(res.title, res.type, res.message);
    },
    error: function(xhr, sts, err) {
      show_notification('Error', 'error', err);
    },
    complete: function()
    {
      tData.draw();
      $('#modal-details').modal('hide');
      $('#form-details input').val('');
      $('#form-details select').val('').trigger('change');
    }
  });
}
