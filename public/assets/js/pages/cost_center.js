var tCostCenter;
$(document).ready(function(){

	tCostCenter = $('#table-cost-center').DataTable({
		aaSorting: [],
		ajax: SITE_URL + '/cost_center/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'cc_code', name: 'cc_code'},
            { data: 'cc_sname', name: 'cc_sname'},
            { data: 'cc_fname', name: 'cc_fname'},
            { data: 'cc_gcode', name: 'cc_gcode'},
            { data: 'cc_gtext', name: 'cc_gtext'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var cost_id = $(this).data('value');
        $('#form-delete-' + cost_id).submit();
    });

});

function on_delete(cost_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', cost_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});