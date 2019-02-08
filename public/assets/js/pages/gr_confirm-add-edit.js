var tData;

$(document).ready(function(){
	$('#form-details').validate();
  tData = $('#details-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: SITE_URL + '/gr_confirm_detail/get_data',
          data: function(d) {
              d.po_number = $('[name="po_number"]').val();
          }
        },
      columns: [
        { data: 'approval_detail.remarks', name: 'approval_detail.remarks'},
        { data: 'approval_detail.pr_specs', name: 'approval_detail.pr_specs'},
        { data: 'approval_detail.pr_uom', name: 'approval_detail.pr_uom'},
        { data: 'qty_order', name: 'qty_order'},
        { data: 'qty_receive', name: 'qty_receive'},
        { data: 'qty_outstanding', name: 'qty_outstanding'},
        { data: 'gr_no', name: 'gr_no'},
        { data: 'notes', name: 'notes'}

      ],
      ordering: false,
      searching: false,
      paging: false,
      info: false,
      drawCallback: function(d) {
        $('[data-toggle="tooltip"]').tooltip({html: true, "show": 500, "hide": 100});
        xeditClasser();
        initEditable();
      }

  });

  $('[name="po_number"]').change(function(){
    var user =  getUser($(this).val());
    setForm(user);
    tData.draw();
  });

  $('#btn-details-save').click(function(){
    save();
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

function getUser(po_number)
{
    var response = $.ajax({
        url: `${SITE_URL}/gr_confirm/get_user/${po_number}`,
        type: 'get',
        dataType: 'json',
        async: false
    });

    return response.responseJSON;
}

function setForm(data) {
    $('[name="approval_number"]').val(data.approval_number);
    $('[name="user_name"]').val(data.user_name);
    $('[name="department_name"]').val(data.department_name);
}

function save()
{
  var formdata = $('#form-details').serializeArray();
  $.ajax({
    url: SITE_URL + '/bom_datas/store',
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
    url: SITE_URL + '/bom_datas/'+rowid,
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

function xeditClasser()
{
    $('#details-table tbody tr').each(function(i, e) {

        var id = $(this).attr('id');
        var qty_order = $(this).find('td:nth-child(4)');
        var qty_receive = $(this).find('td:nth-child(5)');
        var qty_outstanding = qty_order - qty_receive;
        var gr_no = $(this).find('td:nth-child(7)');
        var notes = $(this).find('td:nth-child(8)');
        
        // set qty_receive anchor
        qty_receive.html('<a href="#" class="editable" data-pk="'+id+'" data-name="qty_receive" data-title="Enter qty receive">'+qty_receive.text()+'</a>');
        gr_no.html('<a href="#" class="editable" data-pk="'+id+'" data-name="gr_no" data-title="Enter gr number">'+gr_no.text()+'</a>');
        notes.html('<a href="#" class="editable" data-pk="'+id+'" data-name="notes" data-title="Enter notes">'+notes.text()+'</a>');
        
       
    });
}
function initEditable()
{
    $('.editable').editable({
        type: 'text',
        url: SITE_URL + '/gr_confirm_detail/xedit',
        params: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        validate: function(value) {
            if($.trim(value) == '') return 'This field is required';
        },
        display: function(value, response) {
            return false;   //disable this method
        },
        success: function(data, config) {
            console.log(data.error);
            if (data.error) {
                return data.error;
            };

            tData.draw();
        },
        error: function(sts) {
          console.log(sts);
          return sts.responseJSON.message;
        }
    });
}