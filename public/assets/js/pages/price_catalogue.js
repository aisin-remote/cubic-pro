
$(document).ready(function(){

    tCatalog = $('#table-price_catalogue').DataTable({
        processing: true,
        serverSide: true,
        ajax: SITE_URL + '/price_catalogue/get_data',
        columns: [

            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
            { data: 'source', name: 'source'},
            { data: 'price', name: 'price'},

            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
    }); 

});

    $('#btn-confirm').click(function(){
        var catalog_id = $(this).data('value');
        $('#form-delete-' + catalog_id).submit();
    });

function on_delete(catalog_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', catalog_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-confirm').click(function(){
    var catalog_id= $(this).data('value');
    $('#form-delete-' + catalog_id).submit();
});

$('#btn-import').click(function(){
    $('#form-import').submit();
});

var tTemporaryCatalog;
$(document).ready(function(){

    tTemporaryCatalog = $('#table-temporary-price_catalogue').DataTable({
        processing: true,
        serverSide: true,
        ajax: SITE_URL + '/price_catalogue/get_data_temporary',
        columns: [
           
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
            { data: 'source', name: 'source'},
            { data: 'price', name: 'price'},

            // { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
    });
});

function on_delete_temporary(temporary_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', temporary_id);
}

function on_table_temporary()
{
    $('#modal-temporary').modal('show');
}

$('#btn-confirm').click(function(){
    var temporary_id= $(this).data('value');
    $('#form-delete-' + temporary_id).submit();
});
$('#btn-save').click(function(){
    $('#form-temporary').submit();
});

