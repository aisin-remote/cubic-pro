var tPeriod;
$(document).ready(function(){

	tPeriod = $('#table-period').DataTable({
		ajax: SITE_URL + '/period/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'name', name: 'name'},
            { data: 'value', name: 'value'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var period_id = $(this).data('value');
        $('#form-delete-' + period_id).submit();
    });

});

function on_delete(period_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', period_id);
}