var tAsset;
$(document).ready(function(){

	tAsset = $('#table-asset').DataTable({
		ajax: SITE_URL + '/asset/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'asset_code', name: 'asset_code'},
            { data: 'asset_type', name: 'asset_type'},
            { data: 'asset_class', name: 'asset_class'},
            { data: 'asset_name', name: 'asset_name'},
            { data: 'asset_content', name: 'asset_content'},
            { data: 'asset_account', name: 'asset_account'},
            { data: 'asset_acctext', name: 'asset_acctext'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var asset_id = $(this).data('value');
        $('#form-delete-' + asset_id).submit();
    });

});

function on_delete(asset_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', asset_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});