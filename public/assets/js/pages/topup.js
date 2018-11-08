var tTopUp;
$(document).ready(function(){

	tTopUp = $('#table-topup').DataTable({
		ajax: {
            url:SITE_URL + '/topup_history',
            data: function(d) {
                d.from_date = $('[name="from_date"]').val();
                d.to_date = $('[name="to_date"]').val();
            }
        },
        columns: [
            { data: 'transidmerchant', name: 'transidmerchant'},
            { data: 'user.user_data.name', name: 'user.user_data.name'},
            { data: 'totalamount', name: 'totalamount', class: 'text-right'},
            { data: 'payment_channel', name: 'payment_channel'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'created_at', name: 'created_at'},
            { data: 'payment_date_time', name: 'payment_date_time'},
            { data: 'paymentcode', name: 'paymentcode'},
        ],
	});

    $('[name="from_date"]').change(function(){
        tTopUp.draw();
    });

    $('[name="to_date"]').change(function(){
        tTopUp.draw();
    });


});
