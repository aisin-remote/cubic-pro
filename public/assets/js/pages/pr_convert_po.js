var table = $('#data_table').dataTable({
        processing: true,
        serverSide: true,
        ajax: SITE_URL+"/approvalku/get_print/0",
        // "fnDrawCallback": function (oSettings) {
        //     printApproval();
        // },
        columns: [
            {data: 'department_name', name: 'department'},
            {data: 'approval_number', name: 'approval_number'},
            {data: 'total', name: 'total'},
            {data: 'status', name: 'status'},
            {data: 'overbudget_info', name: 'overbudget_info', orderable: false, searchable: false },
            {data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    function printApproval(approval_number)
    {
		 window.location.href=SITE_URL+"/pr_convert_excel/"+approval_number;
		// $.getJSON(SITE_URL+"/approval/print/"+approval_number,{},function(data){
            // if (data.error) {
                // alert(data.error);
                // return false;
            // };
            // table.ajax.reload();
            // window.location.replace(SITE_URL+"/pr_convert_excel/"+approval_number);

        // });

        // return false;
    }

    $(document).ready(function(){
        $('input[type=radio][name=is_downloaded]').on('change', function() {
            let val = $(this).val();

            if (val == '1') {
                table.api().ajax.url( SITE_URL+"/approvalku/get_print/4").load();
            } else if (val == '2') {
                table.api().ajax.url( SITE_URL+"/approvalku/get_print/1").load();
            } else if (val == '3') {
                table.api().ajax.url( SITE_URL+"/approvalku/get_print/3").load();
            }
        })
    });