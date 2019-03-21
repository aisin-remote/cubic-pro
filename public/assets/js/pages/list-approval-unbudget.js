var tApprovalCapex;
$(document).ready(function(){

	tApprovalCapex = $('#table-approval-unbudget').DataTable({
		ajax: SITE_URL + '/approval-unbudget/approval_unbudget/no_need_approval',
        columns: [
            { data: 'departments.department_name', name: 'departments.department_name'},
            { data: 'approval_number', name: 'approval_number'},
            { data: 'total', name: 'total'},
            { data: 'status', name: 'status'},
            { data: 'overbudget_info', name: 'overbudget_info', orderable: false, searchable: false },
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
	window.location.href=SITE_URL+"/approval/print_approval_excel/"+approval_number
}

function cancelApproval(approval_number)
{
  // var formdata = $('#table-approval-capex').serializeArray();
  var confirmed = confirm('Are you sure to cancel Approval: '+approval_number+'?');
  $.ajax({
    url: SITE_URL + '/approval/cancel_approval',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'get',
    dataType: 'json',
   data: {approval_number:approval_number},
    success: function(res) {
      if(res.error){
		show_notification('Error', 'error',res.error); 
	  }else{
		show_notification('Success', 'success',res.success);
	  }
	  return false;
    },
    error: function(xhr, sts, err) {
      show_notification('Error', 'error', err);
    },
    complete: function()
    {
      tApprovalCapex.draw();
      $('#modal-details').modal('hide');
      $('#form-details input').val('');
      $('#form-details select').val('').trigger('change');
    }
  });
}
