$(document).ready(function(){
	var tType = $('#table-eps_tracking').DataTable({
        ajax: {
            url : SITE_URL + '/eps_tracking/get_data',
            data : {
                pr_created : function() {
                    return $('#pr-created').val()
                }
            }
        },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            {
                data: 'approval_number',
                name: 'approval_number',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'user_create',
                name: 'user_create',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'approval_budget',
                name: 'approval_budget',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'approval_dep_head',
                name: 'approval_dep_head',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'approval_div_head',
                name: 'approval_div_head',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'approval_dir',
                name: 'approval_dir',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'pr_receive',
                name: 'pr_receive',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'item_code',
                name: 'item_code',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'item_description',
                name: 'item_description',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'actual_qty',
                name: 'actual_qty',
                defaultContent: '<div class="text-center">─</div>',
                class:'autonumeric text-right',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'pr_uom',
                name: 'pr_uom',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'actual_price_user',
                name: 'actual_price_user',
                defaultContent: '<div class="text-center">─</div>',
                seacrhable: false,
                sortable: false,
                render: function(data){
                    return parseInt(data).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            {
                data: 'supplier_name',
                name: 'supplier_name',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'po_date',
                name: 'po_date',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'po_number',
                name: 'po_number',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'name',
                name: 'name',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'gr_no',
                name: 'gr_no',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'gr_date',
                name: 'gr_date',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'qty_receive',
                name: 'qty_receive',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'qty_outstanding',
                name: 'qty_outstanding',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
            {
                data: 'notes',
                name: 'notes',
                defaultContent: '<div class="text-center">─</div>',
                sortable: false,
                seacrhable: false,
            },
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var period = $('#pr-created').val();

    $('#pr-created').daterangepicker({
        locale: {
            format: 'YYYY/MM/DD',
        },
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-default',
    }).attr("placeholder", "Filter PR Created Date");

    $('#btn-filter').on('click', function() {
        tType.ajax.reload();
    });

    $('#btn-reset').on('click', function () {
        $('#pr-created').val(period);

        tType.ajax.reload();
    });
});