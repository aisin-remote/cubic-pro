var tGlAccount;
$(document).ready(function(){

	tGlAccount = $('#table-gl_account').DataTable({
		aaSorting: [],
		ajax: SITE_URL + '/gl_account/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'gl_gcode', name: 'gl_gcode'},
            { data: 'gl_gname', name: 'gl_gname'},
            { data: 'gl_acode', name: 'gl_acode'},
            { data: 'gl_aname', name: 'gl_aname'},
            { data: 'dep_key', name: 'dep_key'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var gl_account_id = $(this).data('value');
        $('#form-delete-' + gl_account_id).submit();
    });

});

function on_delete(gl_account_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', gl_account_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});