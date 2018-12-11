var tManageApproval;
$(document).ready(function(){
	tManageApproval = $('#tabel-manage_approval').DataTable({
		ajax: SITE_URL + '/manage_approval/get_data',
        columns: [
            { data: 'name', name: 'name'},
            { data: 'total_approval', name: 'total_approval' },
            { data: 'options', name: 'options', class: 'text-center' },
        ],
	});

	$('#btn-confirm').click(function(){
		var id = $(this).data('id');
		$('#form-delete-'+id).submit();
	});


});

function on_delete(id)
{
	$('#modal-delete-confirm').modal('show');
	$('#btn-confirm').data('id', id);
}