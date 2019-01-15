var tBomSemi;
$(document).ready(function(){

	tBomSemi = $('#table-bom_semi').DataTable({
		ajax: SITE_URL + '/bom_semi/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            // { data: 'fiscal_year', name: 'fiscal_year'},
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'parts.part_name', name: 'parts.part_name'},
            { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
            { data: 'suppliers.supplier_name', name: 'suppliers.supplier_name'},
            { data: 'model', name: 'model'},
            //  { data: 'reject_ratio', name: 'reject_ratio'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-bom_semi tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tBomSemi.row(tr);
        var tableId = 'posts-' + row.data().id;

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(template(row.data())).show();
            initTable(tableId, row.data());
            tr.addClass('shown');
            tr.next().find('td').addClass('no-padding bg-gray');
        }
    });

    function initTable(tableId, data) {
        $('#' + tableId).DataTable({
            /*processing: true,
            serverSide: true,*/
            ajax: data.details_url,
            columns: [
               { data: 'parts.part_number', name: 'parts.part_number'},
               { data: 'parts.part_name', name: 'parts.part_name'},
               { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
               { data: 'suppliers.supplier_name', name: 'suppliers.supplier_name'},
               { data: 'source', name: 'source'},
               { data: 'qty', name: 'qty'},
            ],
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });
    }

     function template(d) {

        // console.log(d);

        return `

                <table class="table details-table" id="posts-${d.id}">
                    <thead>
                    <tr>
                        <th>Part Number</th>
                        <th>Part Name</th>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Source</th>
                        <th>Qty</th>
                    </tr>


            </table>

        `;
    }

    $('#btn-confirm').click(function(){
        var bom_id = $(this).data('value');
        $('#form-delete-' + bom_id).submit();
    });

});

function on_delete(bom_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', bom_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-confirm').click(function(){
    var bom_id= $(this).data('value');
    $('#form-delete-' + bom_id).submit();
});

$('#btn-import').click(function(){
    $('#form-import').submit();
});

var tTemporaryBomSemi;
$(document).ready(function(){

    tTemporaryBomSemi = $('#table-bom_semi_temporary').DataTable({
        ajax: SITE_URL + '/bom_semi/get_data_temporary',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            // { data: 'fiscal_year', name: 'fiscal_year'},

            { data: 'parts.part_number', name: 'parts.part_number'},
            // { data: 'parts.part_name', name: 'parts.part_name'},
            { data: 'model', name: 'model'},
            //  { data: 'reject_ratio', name: 'reject_ratio'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
            $('[data-toggle="tooltip"]').tooltip();
        }
    });


    $('#table-bom_semi_temporary tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tTemporaryBomSemi.row(tr);
        var tableId = 'posts-' + row.data().id;

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(template(row.data())).show();
            initTable(tableId, row.data());
            tr.addClass('shown');
            tr.next().find('td').addClass('no-padding bg-gray');
        }
    });

    function initTable(tableId, data) {
        $('#' + tableId).DataTable({
            /*processing: true,
            serverSide: true,*/
            ajax: data.details_url_temporary,
            columns: [
               { data: 'parts.part_number', name: 'parts.part_number'},
               { data: 'suppliers.supplier_code', name: 'suppliers.supplier_code'},
               { data: 'suppliers.supplier_name', name: 'suppliers.supplier_name'},
               { data: 'source', name: 'source'},
               { data: 'qty', name: 'qty'},
            ],
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });
    }

     function template(d) {

        // console.log(d);

        return `

                <table class="table details_temporary-table" id="posts-${d.id}">
                    <thead>
                    <tr>
                        <th>Part Number</th>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Source</th>
                        <th>Qty</th>
                    </tr>


            </table>

        `;
    }

    $('#btn-confirm').click(function(){
        var bom_semi_id = $(this).data('value');
        $('#form-delete-' + bom_semi_id).submit();
    });

});