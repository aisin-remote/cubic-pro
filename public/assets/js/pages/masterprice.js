var tMasterPrice;
$(document).ready(function(){

    tMasterPrice = $('#table-masterprice').DataTable({
        ajax: SITE_URL + '/masterprice/get_data',
        columns: [
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
            { data: 'source', name: 'source'},
            { data: 'price', name: 'price'},

            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
    });

    $('#btn-confirm').click(function(){
        var masterprice_id = $(this).data('value');
        $('#form-delete-' + masterprice_id).submit();
    });

});

function on_delete(masterprice_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', masterprice_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-confirm').click(function(){
    var masterprice_id= $(this).data('value');
    $('#form-delete-' + masterprice_id).submit();
});

$('#btn-import').click(function(){
    $('#form-import').submit();
});

var tTemporaryMasterPrice;
$(document).ready(function(){

    tTemporaryMasterPrice = $('#table-temporary-masterprice').DataTable({
        ajax: SITE_URL + '/masterprice/get_data_temporary',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
            { data: 'source', name: 'source'},
            { data: 'price', name: 'price'},

            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    $('#table-temporary-masterprice tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tTemporaryMasterPrice.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
});

function on_delete_temporary(temporary_masterprice_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', temporary_masterprice_id);
}

function on_table_temporary()
{
    $('#modal-temporary').modal('show');
}

$('#btn-confirm').click(function(){
    var temporary_masterprice_id= $(this).data('value');
    $('#form-delete-' + temporary_masterprice_id).submit();
});
$('#btn-save').click(function(){
    $('#form-temporary').submit();
});

