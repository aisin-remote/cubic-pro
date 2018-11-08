var tSalesData;
$(document).ready(function(){

	tSalesData = $('#output-master').DataTable({
		ajax: SITE_URL + '/output_master/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'parts.product_code', name: 'parts.product_code'},
            { data: 'parts.product_code', name: 'parts.product_code'},
            { data: 'jan_qty', name: 'jan_qty'},
            // { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
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

var tProductCOde;
$(document).ready(function(){

	tProductCOde = $('#output-master1').DataTable({
		ajax: SITE_URL + '/output_master/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'parts.product_code', name: 'parts.product_code'},
            { data: 'parts.product_code', name: 'parts.product_code'},
            // { data: 'market', name: 'market'},
            // { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-salesdata tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tProductCOde.row( tr );

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