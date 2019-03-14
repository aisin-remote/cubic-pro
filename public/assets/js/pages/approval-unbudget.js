var tUnbudget;
$(document).ready(function(){

    tUnbudget = $('#table-unbudget').DataTable({
		  processing: true,
		  serverSide: true,
		  ajax: SITE_URL + '/approval-unbudget/get_data',
		  columns: [
			{ data: 'budget_no', name: 'budget_no' },
			{ data: 'project_name', name: 'project_name' },
			{ data: 'qty_actual', name: 'qty_actual' },
			{ data: 'price_remaining', name: 'price_remaining' },
			{ data: 'plan_gr', name: 'plan_gr' },
			{ data: 'option', name: 'option' },

		  ],
		  ordering: false,
		  searching: false,
		  paging: false,
		  info: false,
		  drawCallback: function(d) {
			$('[data-toggle="tooltip"]').tooltip({html: true, "show": 500, "hide": 100});
		  }
		});
		
	$('#btn-submit').click(function(){
	  if($('.checklist').length >0){
			$('#formSubmitApproval').submit();
	  }else{
			show_notification('Error','error','You have to add item minimal 1 data');
	  }
	  
	});
  
	$('#btn-save').click(function(){
		save();
	});
});

function onDelete(rowid)
{
  $.ajax({
    url: SITE_URL + '/approvalub/getDelete',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'post',
    dataType: 'json',
	data:{rowid:rowid},
    success: function(res) {
      show_notification(res.title, res.type, res.message);
    },
    error: function(xhr, sts, err) {
      show_notification('Error', 'error', err);
    },
    complete: function()
    {
      tUnbudget.draw();
      $('#modal-details').modal('hide');
      $('#form-details input').val('');
      $('#form-details select').val('').trigger('change');
    }
  });
}