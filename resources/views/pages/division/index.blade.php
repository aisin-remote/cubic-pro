@extends('layouts.master')

@section('title')
	Dashboard Division
@endsection

@section('content')

@php($active = 'division')

<div class="container">
    <div class="row">
		<div class="col-xs-12">
			<div class="page-title-box">
                <h4 class="page-title">Realtime Dashboard</h4>	
            </div>
		</div>
	</div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-12">
				<form class="form-inline" action="{{url('dashboard/view/'.$group_type)}}" method="get">
					<div class="form-group m-r-10">
						@if($group_type=='department')
						<select class="form-control" id="department" name="department">
							<option value="x">Please Select Department...</option>
							@foreach($departments as $dept)
								<option value="{{$dept->department_code}}" {{isset($_GET['department']) && $_GET['department'] == $dept->department_code?'selected="selected"':''}} >{{$dept->department_name}}</option>
							@endforeach
						</select>
						@elseif($group_type=='division')
						<select class="form-control" id="division" name="division">
							<option value="x">Please Select Division...</option>
							@foreach($divisions as $div)
								<option value="{{$div->division_code}}" {{isset($_GET['division']) && $_GET['division'] == $div->division_code?'selected="selected"':''}}>{{$div->division_name}}</option>
							@endforeach
						</select>
						@elseif($group_type=='all')
						
							 <!--<div class="dropdown">
								<a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-warning" data-target="#">
									Choose Divisions <span class="caret"></span>
								</a>
								<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
								  @foreach ($divisions as $div)
								  <li><a href="{{ url('dashboard/view/'.$group_type.'?division=').strtolower($div->division_code) }}">{{ $div->division_name }}</a></li>
								  @endforeach
								  <li class="divider"></li>
								  <li class="dropdown-submenu">
									<a href="#" class="btn-info">ALL Departments</a>									
								  </li>
								  <li class="divider"></li>
								  @foreach ($departments as $dept)
									  <li><a href="{{ url('dashboard/view/'.$group_type.'?department=').$dept->department_code}}">{{ $dept->department_name }}</a></li>
									
								  @endforeach
								</ul>
							</div> -->
						@endif
					</div>
					<div class="form-group m-r-10">
						<input type="text" class="form-control" name="interval" placeholder="Interval" id="interval" value="{{isset($_GET['interval']) && $_GET['interval'] != ""?urldecode($_GET['interval']):''}}">
					</div>
					<div class="form-group m-r-10">
						<select class="form-control" id="planCode" name="plan">
							<option value="O" {{isset($_GET["plan"]) && $_GET["plan"]=="O"?"selected='selected'":""}}>Original Plan</option>
							<option value="R" {{isset($_GET["plan"]) && $_GET["plan"]=="R"?"selected='selected'":""}}>Revised Plan</option>							
						</select>
					</div>
					<button type="submit" class="btn btn-custom waves-effect waves-light btn-md">
						Filter
					</button>
				</form>
				
        </div>
		
		<div class="col-md-12">
			&nbsp;
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
					<div id="chart" style="height:260px"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
					<div id="chart2" style="height:260px"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
					<div id="chart3" style="height:260px"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">All Division</h3>
				</div>
				<div class="panel-body">
					<div id="chart4" style="height:260px"></div>
				</div>
			</div>
		</div>
    </div>

</div>
<input type="hidden" name="group_type" value="{{$group_type}}"/>
@endsection
@push('css')
 <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
 <link rel="stylesheet" href="{{ url('assets/plugins/morris/morris.css') }}">
@endpush
@push('js')
<script src="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ url('assets/js/pages/division-add-edit.js') }}"></script>
<script src="{{ url('assets/plugins/morris/morris.min.js')}}"></script>
<script type="text/javascript">
	$('#interval').daterangepicker({
        format: 'MM/DD/YYYY',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-default',
    });
	function createPie(chart_id,data,url)
	{
		$.getJSON(url,data, function (dataJSON) {
			var group_name = "";
			if(data.group_type == 'department')
			{
				group_name = $('select[name="department"] option:selected').text();
			}else if(data.group_type == 'division')
			{
				group_name = $('select[name="division"] option:selected').text();
			}
			$('#'+chart_id).parent().prev().find('h3').html(data.group_type+" "+group_name);
			$('#'+chart_id).parent().prev().find('h3').after("<p>"+dataJSON.text+"</p>");
			$.plot('#'+chart_id, dataJSON.data, {
				series: {
					pie: {
						radius: 1,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: {
								opacity: 0.5
							}
						},
						show: true
					}
				},
				grid: {hoverable: true,clickable: true}
			});
			
		}).error(function(e){
			show_notification('Error','error','Getting data for chart failed...');
		});
	}
	function createBar(chart_id,data,url)
	{
		$.getJSON(url,data, function (dataJSON) {
			Morris.Bar({
			  element: 'chart4',
			  data: [
				{ y: '2006', a: 100, b: 90 },
				{ y: '2007', a: 75,  b: 65 },
				{ y: '2008', a: 50,  b: 40 },
				{ y: '2009', a: 75,  b: 65 },
				{ y: '2010', a: 50,  b: 40 },
				{ y: '2011', a: 75,  b: 65 },
				{ y: '2012', a: 100, b: 90 }
			  ],
			  xkey: 'y',
			  ykeys: ['a', 'b'],
			  labels: ['Series A', 'Series B']
			});
		});
	}
	var group_type  = $('input[name="group_type"]').val();
	var department  = $('select[name="department"]').val();
	var division 	= $('select[name="division"]').val();
	var interval 	= $('input[name="interval"]').val();
	var plan 		= $('select[name="plan"]').val();
	
	createPie('chart',{group_type:group_type,type:'pie',budget_type:'cx',department:department,division:division,interval:interval,plan:plan},SITE_URL+"/dashboard/getJSONData");
	createPie('chart2',{group_type:group_type,type:'pie',budget_type:'ex',department:department,division:division,interval:interval,plan:plan},SITE_URL+"/dashboard/getJSONData");
	createBar('chart3',{group_type:group_type,type:'bar',budget_type:'cx',department:department,division:division,interval:interval,plan:plan},SITE_URL+"/dashboard/getJSONData");
	Morris.Bar({
	  element: 'chart4',
	  data: [
		{ y: 'April', a: 100, b: 90 },
		{ y: 'Mei', a: 75,  b: 65 },
		{ y: 'Juni', a: 50,  b: 40 },
		{ y: 'Juli', a: 75,  b: 65 },
		{ y: 'Aug', a: 50,  b: 40 },
		{ y: 'Sep', a: 75,  b: 65 },
		{ y: 'Oct', a: 100, b: 90 }
	  ],
	  xkey: 'y',
	  ykeys: ['a', 'b'],
	  labels: ['Series A', 'Series B']
	});
	function labelFormatter(label, series) {
		return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
	}
</script>
@endpush