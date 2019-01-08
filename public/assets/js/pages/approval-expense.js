var tData;
$(document).ready(function(){

  tData = $('#table-approval-expense').DataTable({
      processing: true,
      serverSide: true,
      ajax: SITE_URL + '/approval-expense/get_data',
      columns: [
        { data: 'budget_no', name: 'budget_no' },
        { data: 'project_name', name: 'project_name' },
        { data: 'pr_specs', name: 'pr_specs' },
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
  $('#btn-approval').click(function(){
    approval();
  });

});

function approval()
{
  var formdata = $('#table-approval-expense').serializeArray();
  $.ajax({
    url: SITE_URL + '/approval-expense/approval',
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

function onDelete(rowid)
{
  $.ajax({
    url: SITE_URL + 'approval-expense/delete/'+rowid,
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
    