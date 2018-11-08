var tSalesData;
$(document).ready(function(){

	tSalesData = $('#table-salesdata').DataTable({
		ajax: SITE_URL + '/salesdata/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'customers.customer_code', name: 'customers.customer_code'},
            { data: 'market', name: 'market'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-salesdata tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tSalesData.row( tr );

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

    

    $('#btn-confirm').click(function(){
        var salesdata_id = $(this).data('value');
        $('#form-delete-' + salesdata_id).submit();
    });

});

function on_delete(salesdata_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', salesdata_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-confirm').click(function(){
    var salesdata_id= $(this).data('value');
    $('#form-delete-' + salesdata_id).submit();
});

$('#btn-import').click(function(){
    $('#form-import').submit();
});

var tTemporarySalesData;
$(document).ready(function(){

    tTemporarySalesData = $('#table-temporary-salesdata').DataTable({
        ajax: SITE_URL + '/salesdata/get_data_temporary',
        columns: [
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'customers.customer_code', name: 'customers.customer_code'},
            { data: 'market', name: 'market'},

            // { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
    });
});