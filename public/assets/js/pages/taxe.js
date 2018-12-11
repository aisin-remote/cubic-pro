var tTaxe;
$(document).ready(function(){

	tTaxe = $('#table-taxe').DataTable({
		ajax: SITE_URL + '/taxe/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'tax_code', name: 'tax_code'},
            { data: 'tax_name', name: 'tax_name'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var taxe_id = $(this).data('value');
        $('#form-delete-' + taxe_id).submit();
    });

});

function on_delete(taxe_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', taxe_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});