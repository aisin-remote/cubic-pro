$(document).ready(function(){
  $('#form-add-edit').validate();

  $('[name="budget_no"]').change(function(){
    var budget_no = $(this).val();

    if (budget_no  !== '' && budget_no !== null && budget_no !== undefined ) {
      var arr_expense = getData(budget_no);
	  if(parseInt(arr_expense.is_closed)){
		  show_notification("Error",'error','Budget ['+arr_expense.budget_no+'] already fully reserved, please contact Accounting/Finance Dept. for further assistance');
		  $('[name="budget_no"]').val('').trigger('change');
	  }else if(parseInt(arr_expense.budget_plan) - parseInt(arr_expense.budget_reserved) <=0){
		  show_notification("Error",'error', 'Budget ['+arr_expense.budget_no+'] already fully reserved, please contact Accounting/Finance Dept. for further assistance');
		   $('[name="budget_no"]').val('').trigger('change');
	  }else{
		  $('[name="budget_description"]').val(arr_expense.description);
		  $('[name="qty_remaining"]').val(arr_expense.qty_plan);
		  $('[name="price_remaining"]').autoNumeric('set', arr_expense.budget_remaining);
		  $('[name="budget_remaining_log"]').autoNumeric('set', arr_expense.budget_remaining);
      }
    }

  });

  $('[name="sap_gl_account_id"]').change(function(){

    var sap_gl_account_id = $(this).val();

    // if (sap_gl_account_id !== '' && sap_gl_account_id !== null && sap_gl_account_id !== undefined) {
      var arr_asset = getGlGroup(sap_gl_account_id);

      $('[name="gl_fname"]').find('option').remove();
      $('[name="gl_fname"]').select2({
        data: arr_asset
      });
    // }

  });

  $('[name="sap_asset_id"]').change(function(){

  	var sap_asset_id = $(this).val();

    // if (sap_asset_id !== '' && sap_asset_id !== null && sap_asset_id !== undefined) {
      var arr_asset = getAsset(sap_asset_id);
      $('[name="sap_code_id"]').find('option').remove();
      $('[name="sap_code_id"]').select2({
        data: arr_asset
      });
    // }

  });


});

$('select[name="remarks"]').select2().change(function(){
	 var qty_item = $(this).find('option:selected').attr('qty_item');
	 var uom_id 	= $(this).find('option:selected').attr('uom_id');
	 var item_spec  = $(this).find('option:selected').attr('item_spec');
   var total 		= $(this).find('option:selected').attr('total');
  //  $('input[name="qty_remaining"]').val(1);
	 $('input[name="qty_item"]').val(qty_item);
	 $('input[name="qty_actual"]').val(qty_item);
	 $('select[name="sap_uom_id"]').select2("val", uom_id);
	 $('input[name="pr_specs"]').val(item_spec);
	 $('input[name="price_actual"]').autoNumeric('set', total);
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