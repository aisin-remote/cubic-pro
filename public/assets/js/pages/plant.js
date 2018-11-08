var tType;
$(document).ready(function(){

	tType = $('#table-plant').DataTable({
		ajax: SITE_URL + '/plant/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'plant_code', name: 'plant_code'},
            { data: 'plant_name', name: 'plant_name'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var plant_id = $(this).data('value');
        $('#form-delete-' + plant_id).submit();
    });

});

function on_delete(plant_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', plant_id);
}