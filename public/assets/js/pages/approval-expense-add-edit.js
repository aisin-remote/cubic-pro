$(document).ready(function(){
  $('#form-add-edit').validate();

  $('[name="budget_no"]').change(function(){
    var budget_no = $(this).val();

    if (budget_no  !== '' && budget_no !== null && budget_no !== undefined ) {
      var arr_expense = getData(budget_no);

      $('[name="budget_description"]').val(arr_expense.description);
      $('[name="qty_remaining"]').val(arr_expense.qty_plan);
      $('[name="price_remaining"]').val(arr_expense.budget_plan);
      $('[name="budget_remaining_log"]').val(arr_expense.budget_remaining);
      
    }
    
  });

  $('[name="sap_gl_account_id"]').change(function(){

    var sap_gl_account_id = $(this).val();

    if (sap_gl_account_id !== '' && sap_gl_account_id !== null && sap_gl_account_id !== undefined) {
      var arr_asset = getGlGroup(sap_gl_account_id);
      
      $('[name="gl_fname"]').val(arr_asset.gl_acode);
    }
    
  });
  

});

function onDelete(rowid)
{
  $.ajax({
    url: SITE_URL + '/approval-Expense/'+rowid,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'delete',
    dataType: 'json',
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

function getData(id)
{
	var res = $.ajax({
		url: SITE_URL + '/expense/get/' + id,
		type: 'get',
		dataType: 'json',
		async: false
	});

	return res.responseJSON;
}

function getGlGroup(id)
{
	var res = $.ajax({
		url: SITE_URL + '/expense/getGlGroup/' + id,
		type: 'get',
		dataType: 'json',
		async: false
	});

	return res.responseJSON;
}

function foreignCurrency(elem) {
  if(elem.checked == true){
      $('#hide12').show();
  }
  else{
      $('#hide12').hide();
      $('#currency').val('').trigger('chosen:updated'); 
      $('#price_to_download').val(''); 
  }
}