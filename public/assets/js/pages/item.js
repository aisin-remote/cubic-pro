var tDepartment;
$(document).ready(function(){

	tRate = $('#table-item').DataTable({
		ajax: SITE_URL + '/item/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'item_category.category_name', name: 'item_category.category_name'},
            { data: 'item_code', name: 'item_code'},
            { data: 'item_description', name: 'item_description'},
            { data: 'item_spesification', name: 'item_spesification'},
            { data: 'item_brand', name: 'item_brand'},
            { data: 'item_price', name: 'item_price'},
            { data: 'uom.uom_fname', name: 'uom.uom_fname'},
            { data: 'supplier.supplier_name', name: 'supplier.supplier_name'},
            { data: 'lead_times', name: 'lead_times'},
            { data: 'remarks', name: 'remarks'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var item_id = $(this).data('value');
        $('#form-delete-' + item_id).submit();
    });

});

function on_delete(item_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', item_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});
