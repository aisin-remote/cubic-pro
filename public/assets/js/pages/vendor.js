var tVendor;
$(document).ready(function(){

	tVendor = $('#table-vendor').DataTable({
		aaSorting: [],
		ajax: SITE_URL + '/vendor/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'vendor_code', name: 'vendor_code'},
            { data: 'vendor_sname', name: 'vendor_sname'},
            { data: 'vendor_fname', name: 'vendor_fname'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var vendor_id = $(this).data('value');
        $('#form-delete-' + vendor_id).submit();
    });

});

function on_delete(vendor_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', vendor_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});