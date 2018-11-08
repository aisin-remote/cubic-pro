var tFaq;
$(document).ready(function(){

	tFaq = $('#table-faq').DataTable({
		ajax: SITE_URL + '/faq/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'question', name: 'question'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-faq tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tFaq.row( tr );

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

    function format (d) {

        render =   '<div class="row">'+
                        '<div class="col-md-12">' +
                        '<p>'+ d.answer +'</p>' +
                        '</div>'+
                        '<div class="clearfix"></div>' + 
                    '</div>';

        return render;
    }

    $('#btn-confirm').click(function(){
        var faq_id = $(this).data('value');
        $('#form-delete-' + faq_id).submit();
    });

});

function on_delete(faq_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', faq_id);
}