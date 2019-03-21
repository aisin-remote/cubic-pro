$(document).ready(function(){
  $('#form-add-edit').validate();
  
  $('input[name="type"]').change(function(){
	 if($(this).val() == 1){
		 $('#capex').css('display','block');
		 $('#expense').css('display','none');
		 $('select[name="sap_gl_account_id"]').removeAttr('required');
		 $('input[name="gl_fname"]').removeAttr('required');
		 $('select[name="sap_asset_id"]').attr('required','required');
		 $('input[name="asset_code"]').attr('required','required');
	 }else{
		 $('#capex').css('display','none');
		 $('#expense').css('display','block');
		 $('select[name="sap_gl_account_id"]').attr('required','required');
		 $('input[name="gl_fname"]').attr('required','required');
		 $('select[name="sap_asset_id"]').removeAttr('required');
		 $('input[name="asset_code"]').removeAttr('required');
	 } 
  });
  
  $('[name="budget_no"]').change(function(){
  	var budget_no = $(this).val();
    if (budget_no  !== '' && budget_no !== null && budget_no !== undefined ) {
      var arr_capex = getData(budget_no);
	  if(arr_capex.is_closed){
		  show_notification("Error",'error','Budget['+budget_no+'] already fully reserved, please contact Accounting/Finance Dept. for further assistance');
		  $([name="budget_no"]).val('').trigger('change');
	  }else{
		  $('[name="budget_description"]').val(arr_capex.equipment_name);
		  $('[name="price_remaining"]').val(arr_capex.budget_plan);
		  $('[name="budget_remaining_log"]').val(arr_capex.budget_remaining);
	  }
    }
  	
  });

  $('[name="sap_asset_id"]').change(function(){

  	var sap_asset_id = $(this).val();

    if (sap_asset_id !== '' && sap_asset_id !== null && sap_asset_id !== undefined) {
      var arr_asset = getAsset(sap_asset_id);
      $('[name="asset_code"]').val(arr_asset.asset_code);
    }
  	
  });
  
  $('[name="sap_gl_account_id"]').change(function(){
	
    var sap_gl_account_id = $(this).val();
    if (sap_gl_account_id !== '' && sap_gl_account_id !== null && sap_gl_account_id !== undefined) {
      var arr_asset = getGlGroup(sap_gl_account_id);
      
      $('[name="gl_fname"]').val(arr_asset.gl_acode);
    }
    
  });
  
  $('select[name="remarks"]').select2().change(function(){
	 var qty_item = $(this).find('option:selected').attr('qty_item');
	 var uom_id 	= $(this).find('option:selected').attr('uom_id');
	 var item_spec  = $(this).find('option:selected').attr('item_spec');
	 var total 		= $(this).find('option:selected').attr('total');
	 $('input[name="qty_actual"]').val(qty_item);
	 $('input[name="qty_item"]').val(qty_item);
	 $('select[name="sap_uom_id"]').select2("val", uom_id);
	 $('input[name="pr_specs"]').val(item_spec);
	 $('input[name="price_actual"]').val(total);
  });
});

function setReadOnlyInput ()
{
    $('input[name=asset_kind]').change(function(){

    	// if ()

    	var isChecked =this.value;
    	if (isChecked === 'Immediate Use') {
    		$('[name="settlement_date"]').attr('disabled', 'disabled');
    	} else {
    		$('[name="settlement_date"]').removeAttr('disabled');
    	}
    	// console.log(isChecked);
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
