var tType;
$(document).ready(function(){

	tType = $('#table-upload_po').DataTable({
		ajax: SITE_URL + '/upload_po/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'approval_number', name: 'approval_number'},
            { data: 'created_at', name: 'created_at'},
            { data: 'po_number', name: 'po_number'},
            { data: 'po_date', name: 'po_date'},
            
        ],
        drawCallback: function(){
            $('[data-toggle="tooltip"]').tooltip();
            xeditClasser();
            initEditable();
        }
	});


    $('#btn-confirm').click(function(){
        var upload_po_id = $(this).data('value');
        $('#form-delete-' + upload_po_id).submit();
    });

});

function on_delete(upload_po_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', upload_po_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});

function xeditClasser() {
    $('tbody tr').each(function(i, e) {
        var pk = $(this).find('td:nth-child(1)');
        var po_number = $(this).find('td:nth-child(3)');
        var po_date = $(this).find('td:nth-child(4)');
        po_number.html('<a href="#" class="editable" data-type="text" data-pk="'+pk.text()+'" data-name="po_number" data-title="Enter PO Number">'+po_number.text()+'</a>');
        po_date.html('<a href="#" class="editable" data-type="date" data-pk="'+pk.text()+'" data-name="po_date" data-title="Enter PO Number">'+po_date.text()+'</a>');
    });


    
}

function initEditable()
{
    $('.editable').editable({
        url: SITE_URL + '/UploadPo/xedit',
        params: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        validate: function(value) {
            if($.trim(value) == '') return 'This field is required';
        },
        display: function(value, response) {
            return false;   //disable this method
        },
        success: function(data, config) {
            console.log(data);
            if (data.error) {
                return data.error;
            };

            $(this).text(data.value);
        }
    });
}
