@extends('layouts.master')

@section('title')
	Detail List Approval
@endsection

@section('content')

@php($active = 'approval_master')

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title"> Detail Information of Expense Approval Sheet</h4>
                <ol class="breadcrumb p-0 m-0">
                   
                    <li>
                        <a href="{{ url('approval/ex') }}" class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" id="back">Back</a>
                    </li>
				
                </ol>
            </div>
        </div>
    </div>
                  
   <div class="row">
	<div class="col-md-12">
		<div style="width:100%;overflow:scroll;">
			<table id="example" class="table display nowrap table-bordered responsive-utilities jambo_table">
				<thead>
					<tr>
						<!--<th>No<br>&nbsp;</th>  -->
						<th>Budget<br>Number</th>
						<th>Equipment<br>Number</th> 
						<th>SAP<br>Track No</th>  
						<th>SAP<br>Asset No</th> 
						<th>SAP<br>Acc Code (GL Account)</th> 
						<th>SAP<br>Cost Center</th> 
						<th>Budget<br>Desc</th>  
						<th>PR Item/<br>Specs.</th>   
						<th>Project<br>Name</th>
						<th>Budget<br>Remain</th>
						<th class='bg-primary'>Budget<br>Reserved</th>
						<th class='bg-primary'>Actual<br>Price</th>
						<th class='bg-primary'>Price<br>Download</th> 
						<th>Currency</th>  
						<th>Actual<br>Qty</th>
						<th class='bg-primary'>Status<br>(Resv. vs Act.)</th>
						<th>Actual<br>GR</th>
						<th>SAP Vendor</th>   
						<th>Collective<br>Number</th>
						<th>Requirement No</th> 
						<th>SAP<br>TaxCode</th> 
					</tr>
				</thead>
			</table>
		</div>
	</div>
   </div>
</div>                                

@endsection
@push('js')
<script type="text/javascript">
	var table = $('#example').DataTable({
        ajax: "{{ url('approval-expense/detail').'/'.$master->approval_number }}",

        columns: [
            { data: 'budget_no', name: 'budget_no'},
            { data: 'asset_no', name: 'asset_no'},
            { data: 'sap_track_no', name: 'sap_track_no'},
            { data: 'sap_asset_no', name: 'sap_asset_no'},
            { data: 'sap_account_code1', name: 'sap_account_code'},
            { data: 'ad_sap_cc_code', name: 'ad_sap_cc_code'},
            { data: 'description', name: 'description'},
            { data: 'remarks', name: 'remarks'},
			{ data: 'project_name', name: 'project_name'},
			{ data: 'budget_remaining_log', name: 'budget_remaining_log'},
			{ data: 'budget_reserved', name: 'budget_reserved'},
			{ data: 'actual_price_user', name: 'actual_price_user'},
			{ data: 'price_to_download', name: 'price_to_download'},
			{ data: 'currency', name: 'currency'},
			{ data: 'actual_qty', name: 'actual_qty'},
			{ data: 'overbudget_info', name: 'overbudget_info'},
			{ data: 'actual_gr', name: 'actual_gr'},
			{ data: 'sap_vendor_code', name: 'sap_vendor_code'},
			{ data: 'po_number', name: 'po_number'},
			{ data: 'sap_track_no', name: 'sap_track_no'},
			{ data: 'sap_tax_code', name: 'sap_tax_code'},
            // { data: 'options', name: 'options', searching: false, sorting: false, class: 'text-center' }
        ],
        drawCallback: function(d) {
        	$('[data-toggle="popover"]').popover();
			budgetView();  
					
			budgetStatusStyler();
			@if (\Entrust::can('update-actual-price'))
				xeditClasser();
				initEditable();
				initSapVendorEditable();
				initSapTaxEditable();
				// initCurrencyEditable();

			@elseif(\Entrust::hasRole(['budget']))
				xeditClasser();
				initGLAccountEditable();
				initSapCostCenterEditable();

			@elseif(\Entrust::can('asset-register')) 
				xeditSapAssetNumberClasser();
				initEditable();
			@endif
        },
		paging:false,
		searching:false,
    });
	function validateApproval(approval_number)
    {
        var confirmed = confirm('Are you sure to validate Approval: '+approval_number+'?');

        if (confirmed == true) {
            var data = {
                _token: "{{ csrf_token() }}",
                approval_number: approval_number
            };

            $.getJSON( "{{ url('approval/approve') }}", data, function( data ) {
                if (data.error) {
                    show_notification('Error','error',data.error);
                    return false;
                }else{
					show_notification('Success','success',data.success);
					window.location.replace("{{ url('approval').'/ex/unvalidated' }}");
				}
                
            });
        };

        return false;
    }
	function budgetView()
    {
        $('tbody tr[role="row"]').each(function(i, e) {
             var budget_no = $(this).find('td:nth-child(1)');
			var asset_no = $(this).find('td:nth-child(2)');
			
			asset_no.html(asset_no.text());

            // set budget_no anchor
            budget_no.html('<a href="{{ url("expense/select") }}/'+budget_no.text()+'" >'+budget_no.text()+'</a>');

        });
    }
	function budgetStatusStyler()
    {
        $('tr > td:nth-child(17)').each(function(index, element) { 
            var value = $(this).text();
            if (value == 'Underbudget') {
                $(this).addClass('success');
            };

            if (value == 'Overbudget') {
                $(this).addClass('danger');
            };
        })
    }
	function xeditSapAssetNumberClasser()
    {
        $('tbody tr').each(function(i, e) {
            var id_ad = $(this).find('td:nth-child(2)').find('input:hidden').val();
            var sap_asset_no = $(this).find('td:nth-child(4)');
            
            // set sap asset number
            sap_asset_no.html('<a href="#" class="editable" data-pk="'+id_ad+'" data-name="sap_asset_no" data-title="Enter SAP Asset Number">'+sap_asset_no.text()+'</a>');
        });
    }
	function initEditable()
	{
		$('.editable').editable({
			type: 'text',
			url: SITE_URL + '/approval/xedit',
			params: {				 
				_token: $('meta[name="csrf-token"]').attr('content'),
			},
			mode:'inline',
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
				table.ajax.reload( null, false );
			}
		});
	}
	function xeditClasser()
    {
        $('tbody tr').each(function(i, e) {
			var id_ad = $(this).find('td:nth-child(2)').find('input:hidden').val();
            var budget_no = $(this).find('td:nth-child(1)');        
            var sap_gl_account_capex = $(this).find('td:nth-child(5)');
            var sap_cost_center = $(this).find('td:nth-child(6)');          
            var actual_price_purchasing = $(this).find('td:nth-child(12)'); 
            var price_to_download = $(this).find('td:nth-child(13)'); 
            var currency = $(this).find('td:nth-child(14)');        
            var sap_vendor_code = $(this).find('td:nth-child(18)'); 
            var collective_number = $(this).find('td:nth-child(19)');  
            var sap_tax_code = $(this).find('td:nth-child(21)'); 
          
            // set actual_price_purchasing anchor
            actual_price_purchasing.html('<a href="#" class="editable" data-pk="'+id_ad+'" data-name="actual_price_user" data-title="Enter Actual Price">'+actual_price_purchasing.text()+'</a>');

            // dev-4.0, Ferry, 20161219, Assign SAP Vendor code
            sap_vendor_code.html('<a href="#" class="cmb_editable" data-pk="'+id_ad+'" data-name="sap_vendor_code" data-value="'+sap_vendor_code.text().split(' - ', 1)+'" data-title="Select SAP Vendor">'+sap_vendor_code.text()+'</a>');

            // dev-4.0, Ferry, 20170310, Assign SAP Tax code
            sap_tax_code.html('<a href="#" class="cmb_editable_tax" data-pk="'+id_ad+'" data-name="sap_tax_code" data-value="'+sap_tax_code.text().split(' - ', 1)+'" data-title="Select SAP Tax">'+sap_tax_code.text()+'</a>');

            // set po_number anchor
            collective_number.html('<a href="#" class="editable" data-pk="'+id_ad+'" data-name="po_number" data-title="Enter Collective Number">'+collective_number.text()+'</a>');

            //sap gl_account
            sap_gl_account_capex.html('<a href="#" class="cmb_editable_account" data-pk="'+id_ad+'" data-name="sap_account_code" data-value="'+sap_gl_account_capex.text().split(' - ', 1)+'" data-title="Select GL Account">'+sap_gl_account_capex.text()+'</a>');
 
            //sap cost center
            sap_cost_center.html('<a href="#" class="cmb_editable_costcenter" data-pk="'+id_ad+'" data-name="sap_cc_code" data-value="'+sap_cost_center.text().split(' - ', 1)+'" data-title="Select Cost Center">'+sap_cost_center.text()+'</a>');

            //price to download
            price_to_download.html('<a href="#" class="editable" data-pk="'+id_ad+'" data-name="price_to_download" data-value="'+price_to_download.text().split(' - ', 1)+'" data-title="Enter Price Foreign Currency">'+price_to_download.text()+'</a>');

             //currency
            currency.html('<a href="#" class="editable" data-type="select" data-pk="'+id_ad+'" data-name="currency" data-source="[{value: &#39;IDR&#39;, text: &#39;IDR&#39;}, {value: &#39;USD&#39;, text: &#39;USD&#39;}, {value: &#39;JPY&#39;, text: &#39;JPY&#39;}, {value: &#39;THB&#39;, text: &#39;THB&#39;}]" data-value="'+currency.text().split(' - ', 1)+'" data-title="Select Currency">'+currency.text()+'</a>');

        }); 
    }
	function initSapTaxEditable()
	{
		function getSource() {
            var url = SITE_URL+"/getCmbTax";
            return $.ajax({
                type:  'GET',
                async: true,
                url:   url,
                dataType: "json"
            });
        }
		 getSource().done(function(result) {
            $('.cmb_editable_tax').editable({  //to keep track of selected values in single select
                type: 'select2',  
                url: "{{ url('approval/xedit') }}",
				mode:'inline',
                params: {
                    _token: "{{ csrf_token() }}",
                    approval_number: "{{ $master->approval_number }}"
                },
                autotext: 'always',
                placeholder: 'Silahkan pilih',
                source : result,
                select2: {
                    multiple : false
                },

                success: function(data, config) {
                    console.log(result);
                    if (data.error) {
                        return data.error;
                    };

                    $(this).text(data.value);
					table.ajax.reload( null, false );
                }
            });
        }).fail(function() {
			alert("Error getting Tax from Database!")
		});
	}
	function initSapVendorEditable()
	{
		 function getSource() {
            var url = SITE_URL+"/getCmbVendor";
            return $.ajax({
                type:  'GET',
                async: true,
                url:   url,
                dataType: "json"
            });
        }
		 getSource().done(function(result) {
            $('.cmb_editable').editable({  //to keep track of selected values in single select
                type: 'select2',  
                url: "{{ url('approval/xedit') }}",
				mode:'inline',
                params: {
                    _token: "{{ csrf_token() }}",
                    approval_number: "{{ $master->approval_number }}"
                },
                autotext: 'always',
                placeholder: 'Silahkan pilih',
                source : result,
                select2: {
                    multiple : false
                },

                success: function(data, config) {
                    console.log(result);
                    if (data.error) {
                        return data.error;
                    };

                    $(this).text(data.value);
					table.ajax.reload( null, false );
                }
            });
        }).fail(function() {
			alert("Error getting SAP Vendor from Database!")
		});
	}
	
	function initGLAccountEditable()
    {
        function getSource() {
            var url = SITE_URL+"/getCmbGlAccount";
            return $.ajax({
                type:  'GET',
                async: true,
                url:   url,
                dataType: "json"
            });
        }
		
        getSource().done(function(result) {
            $('.cmb_editable_account').editable({  //to keep track of selected values in single select
                type: 'select2',  
                url: "{{ url('approval/xedit') }}",
				mode:'inline',
                params: {
                    _token: "{{ csrf_token() }}",
                    approval_details_id: "{{ $master->approval_number }}"
                },
                autotext: 'always',
                placeholder: 'Silahkan pilih',
                source : result,
                select2: {
                    multiple : false
                },

                success: function(data, config) {
                    if (data.error) {
                        return data.error;
                    };

                    $(this).text(data.value);
					table.ajax.reload( null, false );
                },
				selector:2,
            });


        }).fail(function() {
                alert("Error getting SAP GL Account from Database!")
		});
    }
	function initSapCostCenterEditable()
    {
        function getSource() {
            var url = "{{ url('getCmbCostCenter') }}";
            return $.ajax({
                type:  'GET',
                async: true,
                url:   url,
                dataType: "json"
            });
        }
        getSource().done(function(result) {
            $('.cmb_editable_costcenter').editable({  //to keep track of selected values in single select
                type: 'select2',  
                url: "{{ url('approval/xedit') }}",
                params: {
                    _token: "{{ csrf_token() }}",
                    approval_number: "{{ $master->approval_number }}"
                },
				mode:'inline',
                autotext: 'always',
                placeholder: 'Silahkan pilih',
                source : result,
                select2: {
                    multiple : false
                },

                success: function(data, config) {
                    console.log(result);
                    if (data.error) {
                        return data.error;
                    };

                    $(this).text(data.value);
					table.ajax.reload( null, false );
                }
            });
        }).fail(function() {
                alert("Error getting SAP Cost Center from Database!")
            });
    }
</script>
@if (session()->has('message'))
    <script type="text/javascript">
        show_notification("{{ session('title') }}","{{ session('type') }}","{{ session('message') }}");
    </script>
@endif
@endpush