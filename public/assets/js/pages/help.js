var tHelp;
$(document).ready(function(){

	tHelp = $('#table-help').DataTable({
		ajax: SITE_URL + '/help/get_data',
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": ''
            },
            { data: 'title', name: 'title'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        order: [1, 'asc'],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#table-help tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tHelp.row( tr );

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
                        '<p>'+ d.description +'</p>' +
                        '</div>'+
                        '<div class="clearfix"></div>' + 
                    '</div>';

        return render;
    }

    $('#btn-confirm').click(function(){
        var help_id = $(this).data('value');
        $('#form-delete-' + help_id).submit();
    });

});

function on_delete(help_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', help_id);
}