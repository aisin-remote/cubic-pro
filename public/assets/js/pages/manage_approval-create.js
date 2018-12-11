$(document).ready(function(){
  $('#form-add').validate();
  arr_pos = get_pos();
});

function get_pos() {
  var res = $.ajax({
    url: SITE_URL + '/master/approval/get_user',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType:'JSON',
        async: false,
  });

  return res.responseJSON;
}

var row_length = 0;

function on_add(){

  $('#empty-row').remove();

  row_length = row_length + 1; /*$('#table-details-appr > tbody > tr').length;*/

  //console.log(row_length);

  var table = '<tr id="'+ row_length +'">'  +
        '<td style="width:50px" class="text-center"><button type="button" class="btn btn-danger btn-xs removeRow"><i class="fa fa-times"></i></button></td>' +
        '<td>'+
        '<div class="form-group">' +
        '<input name="level['+ row_length +']" type="text" class="form-control number" placeholder="Level" required="required">'+
        '<span class="help-block"></span>' +
        '</div>'+
        '</td>' +
        '<td>'+
        '<div class="form-group">' +
        '<select name="user_id['+ row_length +'][]" class="input-sm select-pos" required="required" multiple="multiple"></select>'+
        '<span class="help-block"></span>' +
        '</div>'+
        '</td>' +
        '</tr>';
    
  $('#table-details-appr').append(table);

  $('.select-pos').select2({
    data: arr_pos,
    
  });

  $('.number').autoNumeric();

}

$('#table-details-appr').on('click', '.removeRow', function(){

  var init_length = $('#table-details-appr > tbody > tr').length;

  if (init_length <= 1) {
    $('#table-details-appr > tbody').append('<tr class="text-center" id="empty-row"><td colspan="4"></td></tr>');
  }

  $(this).parent().parent().remove();

});