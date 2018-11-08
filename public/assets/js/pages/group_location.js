var tGroupLocation;
$(document).ready(function(){

	tGroupLocation = $('#table-group_location').DataTable({
		ajax: SITE_URL + '/group_location/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'name', name: 'name'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var group_location_id = $(this).data('value');
        $('#form-delete-' + group_location_id).submit();
    });

});

function on_delete(group_location_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', group_location_id);
}