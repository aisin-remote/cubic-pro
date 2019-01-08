var tData;

$(document).ready(function(){
  $('#form-details').validate();
  tData = $('#details-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: SITE_URL + '/bom_semi_datas/get_data',
      columns: [
        { data: 'part_number', name: 'part_number' },
        { data: 'supplier_name', name: 'supplier_name' },
        { data: 'qty', name: 'qty' },
        { data: 'source', name: 'source' },
        { data: 'option', name: 'option' }

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

function save()
{
  var formdata = $('#form-details').serializeArray();
  $.ajax({
    url: SITE_URL + '/bom_semi_datas/store',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'post',
    dataType: 'json',
    data: formdata,
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

function update()
{
  var formdata = $('#form-details').serializeArray();
  $.ajax({
    url: SITE_URL + '/bom_semi_datas/update',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'post',
    dataType: 'json',
    data: formdata,
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


function on_details()
{
    $('#modal-details').modal('show');
}
$('#btn-confirm').click(function(){
    var bom_id= $(this).data('value');
    $('#form-delete-' + bom_id).submit();
});
$('#btn-details').click(function(){
    $('#form-details').submit();
});


function onDelete(rowid)
{
  $.ajax({
    url: SITE_URL + '/bom_semi_datas/'+rowid,
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