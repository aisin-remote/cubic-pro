var tApprovalExpense;
$(document).ready(function(){

	tApprovalExpense = $('#table-approval-expense').DataTable({
		ajax: SITE_URL + '/approval-expense/approval_expense/no_need_approval',
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

    $('#btn-confirm').click(function(){
        var approval_capex_id = $(this).data('value');
        $('#form-delete-' + approval_capex_id).submit();
    });

    

});

function on_delete(approval_expense_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', approval_expense_id);
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
      tApprovalExpense.draw();
      $('#modal-details').modal('hide');
      $('#form-details input').val('');
      $('#form-details select').val('').trigger('change');
    }
  });
}
