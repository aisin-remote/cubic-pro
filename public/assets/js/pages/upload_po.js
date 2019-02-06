var tType;
$(document).ready(function(){

	tType = $('#table-upload_po').DataTable({
		ajax: SITE_URL + '/upload_po/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'approval_number', name: 'approval_number'},
            { data: 'po_number', name: 'po_number'},
            { data: 'po_date', name: 'po_date'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var upload_po_id = $(this).data('value');
        $('#form-delete-' + upload_po_id).submit();
    });

});

function on_delete(upload_po_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', upload_po_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});