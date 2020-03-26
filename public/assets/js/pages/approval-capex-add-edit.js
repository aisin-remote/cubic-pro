$(document).ready(function(){
  $('#form-add-edit').validate();

  $('[name="budget_no"]').change(function(){
  	var budget_no = $(this).val();
    if (budget_no  !== '' && budget_no !== null && budget_no !== undefined ) {
      var arr_capex = getData(budget_no);
      console.log(arr_capex);
	  if(parseInt(arr_capex.is_closed)){
		  show_notification("Error",'error','Budget ['+arr_capex.budget_no+'] already closed, please contact Accounting/Finance Dept. for further assistance');
		  $('[name="budget_no"]').val('').trigger('change');
	  }else if(parseInt(arr_capex.budget_plan) - parseInt(arr_capex.budget_reserved) <=0){
		  show_notification("Error",'error', 'Budget ['+arr_capex.budget_no+'] already fully reserved, please contact Accounting/Finance Dept. for further assistance');
		   $('[name="budget_no"]').val('').trigger('change');
	  }else{
		  $('[name="budget_description"]').val(arr_capex.equipment_name);
		  $('[name="price_remaining"]').autoNumeric('set', arr_capex.budget_plan);
		  $('[name="budget_remaining_log"]').autoNumeric('set', arr_capex.budget_remaining);
	  }
    }
  });

  $('[name="sap_asset_id"]').change(function(){

  	var sap_asset_id = $(this).val();
    var arr_asset = getAsset(sap_asset_id);
      $('[name="sap_code_id"]').find('option').remove();
      $('[name="sap_code_id"]').select2({
        data: arr_asset
      });
  });

  $('select[name="remarks"]').select2().change(function(){
	 var actual_qty = $(this).find('option:selected').attr('actual_qty');
	 var uom_id 	= $(this).find('option:selected').attr('uom_id');
	 var item_spec  = $(this).find('option:selected').attr('item_spec');
	 var total 		= $(this).find('option:selected').attr('total');
	 $('input[name="actual_qty"]').val(actual_qty);
	 $('select[name="sap_uom_id"]').select2("val", uom_id);
	 $('input[name="pr_specs"]').val(item_spec);
	 $('input[name="price_actual"]').autoNumeric('set', total);
  });
});

function maxAmountQuotation()
{
	var maxBudget = $('input[name="price_remaining"]').val();

}

function setReadOnlyInput ()
{
    $('input[name=asset_kind]').change(function(){

    	var isChecked =this.value;
    	if (isChecked === 'Immediate Use') {
    		$('[name="settlement_date"]').attr('disabled', 'disabled');
    	} else {
    		$('[name="settlement_date"]').removeAttr('disabled');
    	}
    });
}

function getData(id)
{
	var res = $.ajax({
		url: SITE_URL + '/capex/get/' + id,
		type: 'get',
		dataType: 'json',
		async: false
	});

	return res.responseJSON;
}

function getAsset(id)
{
	var res = $.ajax({
		url: SITE_URL + '/capex/getAsset/' + id,
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

var tData;

$(document).ready(function(){
  $('#form-details').validate();
  tData = $('#details-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: SITE_URL + '/approval-capex/get_data',
      columns: [
        { data: 'budget_no', name: 'budget_no' },
        { data: 'project_name', name: 'project_name' },
        { data: 'asset_kind', name: 'asset_kind' },
        { data: 'asset_category', name: 'asset_category' },
        { data: 'sap_uom_id', name: 'sap_uom_id' },
        { data: 'sap_asset_id', name: 'sap_asset_id' },
        { data: 'sap_cost_center_id', name: 'sap_cost_center_id' },
        { data: 'pr_specs', name: 'pr_specs' },
        { data: 'sap_cost_center_id', name: 'sap_cost_center_id' },
        { data: 'budget_remaining_log', name: 'budget_remaining_log' },
        { data: 'price_actual', name: 'price_actual' },
        { data: 'settlement_date', name: 'settlement_date' },
        // { data: 'option', name: 'option' },

      ],
      ordering: false,
      searching: false,
      paging: false,
      info: false,
      drawCallback: function(d) {
        $('[data-toggle="tooltip"]').tooltip({html: true, "show": 500, "hide": 100});
      }

  });

  $('#btn-details-save').click(function(){
    save();
  });

  $('#btn-details-update').click(function(){
    update();
  });

  $('#btn-save').click(function(){

    var form = $('#form-add-edit').validate();

    if (form.form()) {
      $('#form-add-edit').submit();
    }

  });

  $('#btn-reset').click(function(){
    $('#form-add-edit').trigger('reset');
  });

});
