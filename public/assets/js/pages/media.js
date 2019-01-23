/*

Media For uploading files
created by : ыулианто, Thrusday 16 Nov 2017

*/

var previewNode = document.querySelector('.template');
previewNode.id = '';
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var mediaDropzone = new Dropzone('.drag-and-drop', { // Make the whole body a dropzone
	url: SITE_URL + '/media/uploads',
	thumbnailWidth: 300,
	thumbnailHeight: 200,
	previewTemplate: previewTemplate,
	autoQueue: true,
	previewsContainer: '.preview', // Define the container to display the previews
	clickable: '#browse-file', // Define the element that should be used as click trigger to select files.
	acceptedFiles: '.jpg, .jpeg, .png, .mp4',
	params: { _token:  $('meta[name="csrf-token"]').attr('content')},
	uploadMultiple: true,
	parallelUploads: 100,

});

mediaDropzone.on('addedfile', function(file) {

  if (file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'video/mp4'){
    show_notification('Error', 'error', 'File not supported');
    mediaDropzone.removeFile(file);
    console.log(file.type);
  }

  else if (file.size >= 7000000){
    show_notification('Error', 'error', 'File too big');
    mediaDropzone.removeFile(file);
    console.log(file.size);
  }
  else{
    $('.nav-tabs a[href="#media"]').tab('show');
  }

  $('.drag-and-drop').css('border', '3px dashed #e1e1e1');

});

mediaDropzone.on('successmultiple', function(file, data){
	mediaDropzone.removeAllFiles();
	get_data();
	show_notification(data.title, data.type, data.message);
	$('.details').hide();
});

mediaDropzone.on('error', function(sts, xhr, error){
	 if (error != undefined) {
    	show_notification('Error', 'error', error);
		mediaDropzone.removeAllFiles();
    }
});

$(window).load(function(){
	get_data();
});

$(document).ready(function(){

	$('.remove-img').click(function(){
		id = $('[name="media_id"]').val();
		on_remove(id);
	});

	$('[name="keyword"]').keyup(function(){
		get_data();
		$('.details').hide();
	});

	$('.drag-and-drop').on('dragover', function(){
	    $(this).css('border', '3px dashed #5b90bf');
	});

	$('.drag-and-drop').on('dragleave', function(){
	    $(this).css('border', '3px dashed #e1e1e1');
	});

});

function get_data()
{
	$.ajax({
		url: SITE_URL + '/media/get_data',
		type: 'get',
		data: {
			keyword: $('[name="keyword"]').val(),
		},
		datType: 'html',
		beforeSend: function()
		{
			$('.loading').fadeIn();
		},
		success: function(data)
		{
			$('.load-data').html(data);
		},
		error: function(sts, xhr, error)
		{
			show_notification('Error', 'error', error);
		},
		complete: function(data)
		{
			$('.loading').fadeOut();
			$('.image-browser img').click(function(){
	            $('.image-browser').children().removeClass('selected');
	            id = $(this).data('value');
	            select_data(id);
	            $(this).parent().addClass('selected');
          	});

          	$('.image-gallery img').click(function(){

          		if ($(this).parent().hasClass('selected')) {
          			$('.details').hide();
          			$(this).parent().removeClass('selected');

          		} else {
          			id = $(this).data('value');
		            select_data(id);
		            $(this).parent().addClass('selected');
          		}

          		var img_length = $('.image-gallery .selected').length;

          		if (img_length < 1) {
          			$('#count-selected').text('');
          		} else {
          			$('#count-selected').text(img_length + ' image(s) selected');
          		}

          			

          	});

		}
	});
}

function select_data(id)
{
	$.ajax({
		url: SITE_URL + '/media/select_data/'+id,
		type: 'get',
		datType: 'json',
		beforeSend: function()
		{
			$('.loading').fadeIn();
		},
		success: function(data)
		{
			data = $.parseJSON(data);
			file = data.name;

			$('.details').show();
			
			if (file.substr( (file.lastIndexOf('.') +1)) == 'mp4') {
				$('.thumbnail').attr('src', SITE_URL + '/uploads/video_thumb.png');
			} else {
				$('.thumbnail').attr('src', SITE_URL + '/uploads/thumbs/'+ data.name);
			}
			
			
			$('.filename').text(data.name);
			$('.created_at').text(data.created_at);
			$('.filesize').text(data.size);
			$('[name="filename"]').val(data.name);
			$('[name="caption"]').val(data.caption);
			$('[name="alt"]').val(data.alt);
			$('[name="image_description"]').val(data.description);
			$('[name="media_id"]').val(data.id);
		},
		error: function(sts, xhr, error)
		{
			show_notification('Error', 'error', error);
		},
		complete: function(data)
		{
			$('.loading').fadeOut();
		}
	});
}

function on_remove(id)
{
	$.ajax({
		url: SITE_URL + '/media/'+id,
		type: 'delete',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function()
		{
			$('.loading').fadeIn();
		},
		success: function(data)
		{
			show_notification(data.title, data.type, data.message);
		},
		error: function(sts, xhr, error)
		{
			show_notification('Error', 'error', error);
		},
		complete: function(data)
		{
			get_data();
			$('.details').hide();
		}
	});
}

function open_modal()
{
	$('#modal-media').modal('show');
}

function on_remove_image()
{
	$('[name="image_id"]').val('');
	$('#image').hide();
	$('#btn-remove-img').hide();
}

function on_create_gallery()
{
	$('#modal-gallery').modal('show');
}