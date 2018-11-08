$(document).ready(function(){

	$('[name="max_amount"]').keyup(function(){
		if ($(this).val() != '') {
			
			$('[name="max_hour"]').removeAttr('disabled');
			$('[name="max_hour"]').attr('required', 'required');
			$('#max-hour-span').text('*');

		} else {

			$('[name="max_hour"]').attr('disabled', 'disabled');
			$('[name="max_hour"]').removeAttr('required', 'required');
			$('#max-hour-span').text('');
		}
	});

	$('#form-add-edit').validate();
});