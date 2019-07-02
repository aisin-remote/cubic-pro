var tDepartment;
$(document).ready(function(){

	tRate = $('#table-item').DataTable({
		aaSorting: [],
		ajax: SITE_URL + '/item/get_data',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        columns: [
            { data: 'item_category.category_name', name: 'item_category.category_name'},
            { data: 'item_code', name: 'item_code'},
            { data: 'item_description', name: 'item_description'},
            { data: 'item_specification', name: 'item_specification'},
            { data: 'item_brand', name: 'item_brand'},
            { data: 'item_price', name: 'item_price', class: 'autonumeric text-right'},
            { data: 'uom.uom_fname', name: 'uom.uom_fname'},
            { data: 'supplier.supplier_name', name: 'supplier.supplier_name'},
            { data: 'lead_times', name: 'lead_times'},
            { data: 'remarks', name: 'remarks'},
            { data: 'tags', name: 'tags'},
            { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(){
        	$('[data-toggle="tooltip"]').tooltip();
        }
	});


    $('#btn-confirm').click(function(){
        var item_id = $(this).data('value');
        $('#form-delete-' + item_id).submit();
    });

});

function on_delete(item_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', item_id);
}

function on_import()
{
    $('#modal-import').modal('show');
}

$('#btn-import').click(function(){
    $('#form-import').submit();
});

var item_document_dropzone = new Dropzone ('#upload-doc-item', { 
    url: SITE_URL + 'master/item/upload_doc', 
    parallelUploads: 1,
    maxFiles: 1,
    previewTemplate: previewTemplate,
    autoQueue: false, 
    previewsContainer: "#preview-items-doc",
    clickable: "#button-browse-doc",
    acceptedFiles: '.xls, .xlsx, .pdf',
    params: { _token:  $('meta[name="csrf-token"]').attr('content')}, 
    init : function() {
        item_document_dropzone = this;

        this.on('addedfile', function(file){
            if (file.type != 'application/vnd.ms-excel' && file.type != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type != 'application/pdf') {
                $('.import-area').hide();
                console.log(file.type);
                show_gritter('error', 'Error', 'File tidak diterima');
                this.removeFile(file);
                $('.small-drag-drop').css('border-color', '#ccc');
            }
            else {
                //$('#file').val(file.name);
                $('.small-drag-drop').css('border-color', '#ccc');
                $('#preview-edit-doc').attr('style', 'display:none');
            } 
        });

        this.on('removedfile', function(file){
            $('#file').val('');
        });

        this.on('maxfilesexceeded', function(file)
        {
            this.removeAllFiles();
            this.addFile(file);
        });

        this.on('sending', function(file, xhr, formData) {
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('item_code', $('#item-code').val());
        });

        this.on("success", function(file, response) {
            this.removeAllFiles();
            $('#file').val(response);
            //on_save_item();
        });

        // this.on('error', function(xhr, status, error = '') {
        //     show_gritter('error', 'Error', error.statusText);
        // });

    }

});

function upload_pic(){
    item_picture_dropzone.enqueueFiles(item_picture_dropzone.getFilesWithStatus(Dropzone.ADDED));
}

function on_remove_pic(){
    $('#preview-edit-file').attr('style', 'display:none');
}
