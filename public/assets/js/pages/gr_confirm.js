var tGR;
$(document).ready(function(){

	tGR = $('#table-gr_confirm').DataTable({
		ajax: SITE_URL + '/gr_confirm/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'po_number', name: 'po_number'},
            { data: 'approval_master.approval_number', name: 'approval_master.approval_number'},
            { data: 'user.name', name: 'user.name'},
            { data: 'user.department.department_name', name: 'user.department.department_name'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-gr_confirm tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tGR.row(tr);
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
               { data: 'approval_detail.remarks', name: 'approval_detail.remarks'},
               { data: 'approval_detail.pr_specs', name: 'approval_detail.pr_specs'},
               { data: 'approval_detail.pr_uom', name: 'approval_detail.pr_uom'},
               { data: 'qty_order', name: 'qty_order'},
               { data: 'qty_receive', name: 'qty_receive'},
               { data: 'qty_outstanding', name: 'qty_outstanding'},
               { data: 'gr_no', name: 'gr_no'},
               { data: 'notes', name: 'notes'},
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
                        <th>Description</th>
                        <th>Specification</th>
                        <th>UoM</th>
                        <th>Qty Order</th>
                        <th>Qty Receive</th>
                        <th>Qty Outstanding</th>
                        <th>GR Number</th>
                        <th>Notes</th>
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
var tTemporaryBom;
$(document).ready(function(){

    tTemporaryBom = $('#table-bom_temporary').DataTable({
        ajax: SITE_URL + '/bom/get_data_temporary',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     true,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'parts.part_number', name: 'parts.part_number'},
            { data: 'supplier_code', name: 'supplier_code'},
            { data: 'model', name: 'model'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
            $('[data-toggle="tooltip"]').tooltip();
        }
    });


    $('#table-bom_temporary tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tTemporaryBom.row(tr);
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
        var bom_id = $(this).data('value');
        $('#form-delete-' + bom_id).submit();
    });

});

var tMasterPrice;
$(document).ready(function(){

    tMasterPrice = $('#table-detail').DataTable({
        ajax: SITE_URL + '/bom_data/get_data',
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
            { data: 'suppliers.supplier_name', name: 'suppliers.supplier_name'},
            { data: 'source', name: 'source'},
            { data: 'qty', name: 'qty'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
            $('[data-toggle="tooltip"]').tooltip();
        }
    });


    $('#table-masterprice tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tMasterPrice.row( tr );

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
        var masterprice_id = $(this).data('value');
        $('#form-delete-' + masterprice_id).submit();
    });

});