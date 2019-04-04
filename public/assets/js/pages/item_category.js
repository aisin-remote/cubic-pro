var tType;
$(document).ready(function(){

	tType = $('#table-item_category').DataTable({
		aaSorting: [],
		ajax: SITE_URL + '/item_category/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'category_code', name: 'category_code'},
            { data: 'category_name', name: 'category_name'},
            { data: 'feature_image', name: 'feature_image'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var item_category_id = $(this).data('value');
        $('#form-delete-' + item_category_id).submit();
    });

});

function on_delete(item_category_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', item_category_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});