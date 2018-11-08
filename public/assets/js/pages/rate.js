var tRate;
$(document).ready(function(){

	tRate = $('#table-rate').DataTable({
		ajax: SITE_URL + '/rate/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { data: 'group_location.name', name: 'group_location.name'},
            { data: 'type.name', name: 'type.name'},
            { data: 'amount_formatted', name: 'amount_formatted', class: 'text-right' },
            { data: 'real_amount_formatted', name: 'real_amount_formatted', class: 'text-right' },
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});

     $('#table-rate tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tRate.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( template(row.data()) ).show();
            tr.addClass('shown');
        }
    });


    function template(d) {
        return `

            <table class="table table-primary table-hover">
                <tr>
                    <td>Tarif jam selanjutnya</td>
                    <td>${ d.next_amount_formatted }</td>
                </tr>
                <tr>
                    <td>Tarif potongan jam selanjutnya</td>
                    <td>${ d.next_real_amount_formatted }</td>
                </tr>
                <tr>
                    <td>Maksimal tarif</td>
                    <td>${ d.max_amount_formatted }</td>
                </tr>
                <tr>
                    <td>Maksimal tarif potongan</td>
                    <td>${ d.max_real_amount_formatted }</td>
                </tr>
                <tr>
                    <td>Maksimal jam</td>
                    <td>${ d.max_hour == null ? 0 : d.max_hour } Jam</td>
                </tr>
            </table>

        `;
    }


    $('#btn-confirm').click(function(){
        var rate_id = $(this).data('value');
        $('#form-delete-' + rate_id).submit();
    });

});

function on_delete(rate_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', rate_id);
}