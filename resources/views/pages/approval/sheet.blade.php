<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ url('/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('/assets/css/custom.css') }}" rel="stylesheet">
    <title>Approval Sheet: {{ $approval->approval_number }}</title>
    <link href="{{ url('/css/app.css') }}" rel="stylesheet">
    <script src="{{url('/assets/js/jquery.min.js')}}"></script>
    <style>
        body { margin: 40px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <img src="{{ url('assets/images/logo_sm.png') }}" alt="Logo" class="pull-right" style="width: 120px">
                <h3>Approval Sheet for {{ $type }}</h3>

                <table>
                    <tr>
                        <td>Approval Number</td>
                        <td>&nbsp;: {{ $approval->approval_number }}</td>
                    </tr>
                    <tr>
                        <td>Department</td>
                        <td>&nbsp;: {{ $department }}</td>
                    </tr>

                </table>

                <div class="col-md-12" align="right" style="font-size: 10px">App version : 4.3.0, Printed by : {{ auth()->user()->name }}. {{ date('Y-m-d H:i:s') }}</div>
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>No<br>&nbsp;</th> 
                            <th>Project<br>Name</th>
                            <th>Budget<br>No</th>
                            <th>Asset<br>No</th>  
                            <th>SAP Track<br>No</th>
                            <th>Budget<br>Desc</th>
                            <th>Purchase<br>Req. Item</th> 
                            <th>Budget<br>Remain</th>
                            <th>Actual<br>Price</th>
                            <th>Status<br>&nbsp;</th>
                            <th>Plan<br>GR</th>
                            <th>Actual<br>GR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>   
                        @foreach ($approval->details as $detail)
                        <tr>
                            <td>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}<?php $i++ ?></td>   
                            <td>{{ $detail->project_name }}</td>
                            <td>{{ $detail->budget_no }}</td>
                            <td>{{ $detail->assetNumber }}</td>                     
                            <td>{{ $detail->SapTrackingNumber }}</td>               
                            <td>{{ $detail->budgetDescription }}</td>
                            <td>{{ $detail->remarksDescription }}</td>             
                            <td>{{ $detail->budgetRemainingLogFormatted }}</td>
                            <td>{{ $detail->actualPriceUserFormatted }}</td>
                            <td>{{ $detail->budgetStatus }}</td>
                            <td>{{ $detail->planGrFormatted }}</td>
                            <td>{{ $detail->actualGrFormatted }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($type == 'Unbudget')
            <div class="col-md-7">
                <br>
                <div class="alert alert-danger">
                    <center><h1 class="warning">UNBUDGET</h1></center>
                </div>
            </div>
            @else
            <div class="col-md-7">
                <div class="clearfix">&nbsp;</div>

                @if ($approval->isOverExist())
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th colspan="4" class="danger">
                            <center>Overbudget Summary</center>
                        </th>
                    </tr>
                    <tr>
                        <th>Budget Number</th>
                        <th>Budget Planned</th>
                        <th>Budget Used</th>
                        <th>Budget Remain</th>
                    </tr>
                    @foreach ($overbudgets as $overbudget)
                        <tr>
                            <td>{{$overbudget->budget_no}}</td>
                            <td>{{$overbudget->budgetPlanFormatted}}</td>
                            <td>{{$overbudget->budgetUsedFormatted}}</td>
                            <td>{{$overbudget->budgetRemainingFormatted}}</td>
                        </tr>
                    @endforeach
                </table>
                @endif
            </div>
            @endif

            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <span class="pull-right">Karawang, {{$approval->createdAtFormatted}}&nbsp;</span>

                <div class="x_content">

                    @if ($type != 'Unbudget')

                    <h5><strong>{{ $type }} Budget Statistic ({{ $department }}) :</strong></h5>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Plan<small> (1y)</small></span>
                        </div>
                        <div class="w_center w_45">
                            <div class="progress">
                                <div class="progress-bar bg-blue" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                    <span class="sr-only">60% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_30">
                            <span>IDR {{ number_format($statistics['stat_plan']) }} Mio</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Used<small> (ytd now)</small></span>
                        </div>
                        <div class="w_center w_45">
                            <div class="progress">
                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{ number_format($statistics['stat_actual_percentage']) }}%;">
                                    <span class="sr-only">60% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_30">
                            <span>IDR {{ number_format($statistics['stat_actual']) }} Mio</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Outlook<small> (+ pr)</small></span>
                        </div>
                        <div class="w_center w_45">
                            <div class="progress">
                                <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{ number_format($statistics['stat_approval_total_percentage']) }}%;">
                                    <span class="sr-only">60% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_30">
                            <span>IDR {{ number_format($statistics['stat_approval_total']) }} Mio</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    @endif    
                </div>

                <table class="table table-bordered">
                     <tr>
                        <td>Approved,</td>
                        <td>Approved,</td>
                        <td>Checked,</td>
                        <td>Prepared,</td>
                    </tr>
                    <tr>
                        <td height="75" style="vertical-align: middle;"></td>
                        <td align="middle" height="75"><img src="{{ asset('img/sign-gm.png') }}" alt="approved" style="height: 80px"></td>
                        <td align="middle" height="75"><img src="{{ asset('img/sign-mgr.png') }}" alt="approved" style="height: 80px"></td>
                        <td align="middle" height="75"><img src="{{ asset('img/sign-spv.png') }}" alt="approved" style="height: 80px"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;Director&nbsp;&nbsp;&nbsp;</td>
                        <td>Group Mgr</td>
                        <td>Dept. Head</td>
                        <td>Sec. Head</td>
                    </tr>
                </table>

            </div>
            <footer>&nbsp;</footer>

        </div>
    </div>

    <script src="{{asset('/js/jspdf.debug.js')}}"></script>
    <script src="{{asset('/js/html2canvas.js')}}"></script>
    <script>
    $(document).ready(function(){
        @if ($type == 'Unbudget')                           
            @if ($approval->details->count() > 17)
                var pdf_mode = 'p';
            @else
                var pdf_mode = 'l';
            @endif
        @else
            @if (count($overbudgets) > 10)              
                var pdf_mode = 'p';
            @else
                @if ($approval->details->count() > 17)
                    var pdf_mode = 'p';
                @else
                    @if (count($overbudgets) > 4)
                        var pdf_mode = 'p';
                    @else
                        var pdf_mode = 'l';
                    @endif
                @endif
            @endif
        @endif

        var pdf = new jsPDF(pdf_mode, 'px', 'a4')
        , specialElementHandlers = {
            
            '#bypassme': function(element, renderer){
                return true
            }
        };

         pdf.addHTML($('#ElementYouWantToConvertToPdf')[0], function () {
            var filename = '{{$approval->approval_number}}';
             pdf.save(filename+'.pdf');
         });
    })
    </script>

</body>
</html>