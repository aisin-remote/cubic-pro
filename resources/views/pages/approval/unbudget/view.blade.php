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
                <h4 class="page-title"> Detail Information of Capex Approval Sheet</h4>
                <ol class="breadcrumb p-0 m-0">
                    @if ((\Entrust::can('validate_quotation') && ($master->status === 0)) || (\Entrust::can('dept_head_approve') && ($master->status === 1)) || (\Entrust::can('gm_approve') && ($master->status === 2)) || (\Entrust::can('dir_approve') && ($master->status === 3)))
						<li class="active">
							<a href="{{ url('approval/ub/unvalidated')}}" class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" id="back">Back</a>
						</li>
						<li>
							<a href="#" class="btn btn-success btn-bordered waves-effect waves-light m-b-20" id="validate" onclick="validateApproval(&#39;{{ $master->approval_number }}&#39;)">Approve {{ $master->approval_number }}</a>
						</li>
					@else
						<li>
							<a href="{{ url('approval/ub') }}" class="btn btn-primary btn-bordered waves-effect waves-light m-b-20" id="back">Back</a>
						</li>
					@endif
                </ol>
            </div>
        </div>
    </div>
                  
   <div class="row">
	<div class="col-md-12">
		<div class="card-box">
			<table id="example" class="table display nowrap table-bordered responsive-utilities jambo_table">
				<thead>
					<tr>
						<th>No<br>&nbsp;</th>  
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
	
    var table = $('table').dataTable({
        "ajax": "{{ url('approval/detail').'/'.$master->approval_number }}",
        "ordering": false,
        "scrollX": true,
        "paging": false,
        "searching": false,
		"fnDrawCallback": function (oSettings) {
			budgetView();  
					
			budgetStatusStyler();
			
			@if (\Entrust::can('update_actual_price'))
				// xeditClasser();
				// initEditable();
				// initSapVendorEditable();
				// initSapTaxEditable();
				// initCurrencyEditable();

			@elseif(\Entrust::hasRole(['budget']))
				// xeditClasser();
				// initGLAccountEditable();
				// initSapCostCenterEditable();

			@elseif(\Entrust::can('asset_register'))  
				// xeditSapAssetNumberClasser();
				// initEditable();
			@endif
			
		}
        
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
					window.location.replace("{{ url('approval').'/cx/unvalidated' }}");
				}
                
            });
        };

        return false;
    }
	function budgetView()
    {
        $('tbody tr[role="row"]').each(function(i, e) {
            var budget_no = $(this).find('td:nth-child(2)');

            // set budget_no anchor
            budget_no.html('<a href="{{ url("capex") }}/'+budget_no.text()+'" >'+budget_no.text()+'</a>');

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
	
	function xeditClasser()
    {
        $('tbody tr').each(function(i, e) {
            var budget_no = $(this).find('td:nth-child(2)');        
            var sap_gl_account_capex = $(this).find('td:nth-child(6)');
            var sap_cost_center = $(this).find('td:nth-child(7)');          
            var actual_price_purchasing = $(this).find('td:nth-child(13)'); 
            var price_to_download = $(this).find('td:nth-child(14)'); 
            var currency = $(this).find('td:nth-child(15)');        
            var sap_vendor_code = $(this).find('td:nth-child(19)'); 
            var po_number = $(this).find('td:nth-child(20)');  
            var sap_tax_code = $(this).find('td:nth-child(22)'); 
            
            // set actual_price_purchasing anchor
            actual_price_purchasing.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="actual_price_purchasing" data-title="Enter Actual Price">'+actual_price_purchasing.text()+'</a>');

            // dev-4.0, Ferry, 20161219, Assign SAP Vendor code
            sap_vendor_code.html('<a href="#" class="cmb_editable" data-pk="'+budget_no.text()+'" data-name="sap_vendor_code" data-value="'+sap_vendor_code.text().split(' - ', 1)+'" data-title="Select SAP Vendor">'+sap_vendor_code.text()+'</a>');

            // dev-4.0, Ferry, 20170310, Assign SAP Tax code
            sap_tax_code.html('<a href="#" class="cmb_editable_tax" data-pk="'+budget_no.text()+'" data-name="sap_tax_code" data-value="'+sap_tax_code.text().split(' - ', 1)+'" data-title="Select SAP Tax">'+sap_tax_code.text()+'</a>');

            // set po_number anchor
            po_number.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="po_number" data-title="Enter Collective Number">'+po_number.text()+'</a>');

            //sap gl_account
            sap_gl_account_capex.html('<a href="#" class="cmb_editable_account" data-pk="'+budget_no.text()+'" data-name="sap_gl_account_capex" data-value="'+sap_gl_account_capex.text().split(' - ', 1)+'" data-title="Select GL Account">'+sap_gl_account_capex.text()+'</a>');
 
            //sap cost center
            sap_cost_center.html('<a href="#" class="cmb_editable_costcenter" data-pk="'+budget_no.text()+'" data-name="sap_cost_center" data-value="'+sap_cost_center.text().split(' - ', 1)+'" data-title="Select Cost Center">'+sap_cost_center.text()+'</a>');

            //price to download
            price_to_download.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="price_to_download" data-value="'+price_to_download.text().split(' - ', 1)+'" data-title="Enter Price Foreign Currency">'+price_to_download.text()+'</a>');

             //currency
            currency.html('<a href="#" class="cmb_editable_currency" data-pk="'+budget_no.text()+'" data-name="currency" data-value="'+currency.text().split(' - ', 1)+'" data-title="Select Currency">'+currency.text()+'</a>');

        }); 
    }
	function initGLAccountEditable()
    {
        function getSource() {
            var url = "{{ url('ajax/account-list-capex') }}";
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
                }
            });


        }).fail(function() {
                alert("Error getting SAP GL Account from Database!")
		});
    }
	function initSapCostCenterEditable()
    {
        function getSource() {
            var url = "{{ url('ajax/cost-center-list') }}";
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