var tType;
$(document).ready(function(){

	tType = $('#table-type').DataTable({
		ajax: SITE_URL + '/type/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'name', name: 'name'},
            { data: 'description', name: 'description'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var type_id = $(this).data('value');
        $('#form-delete-' + type_id).submit();
    });

});

function on_delete(type_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', type_id);
}