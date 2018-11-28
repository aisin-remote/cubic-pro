var tCapex;
$(document).ready(function(){

    tCapex = $('#table-approval-capex').DataTable({
        ajax: SITE_URL + '/approval/get_list/ex/need_approval',
        "fnDrawCallback": function (oSettings) {
            budgetStatusStyler();
        },
        columns: [
           {data: 'name', name: 'name'},
           {data: 'approval_number', name: 'approval_number'},
           {data: 'total', name: 'total'},
           {data: 'status', name: 'status'},
           {data: 'overbudget_info', name: 'overbudget_info', orderable: false, searchable: false },
           {data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

});


function budgetStatusStyler()
{
    $('tr > td:nth-child(5)').each(function(index, element) {       
        var value = $(this).text();

        if (value == 'All underbudget') {
            $(this).addClass('success');
        };

        if (value == 'Overbudget exist') {
            $(this).addClass('danger');
        };
    })
}

function cancelApproval(approval_number)
{
    var confirmed = confirm('Are you sure to cancel Approval: '+approval_number+'?');

    if (confirmed == true) {
         $('#'+approval_number).hide();
        var data = {
            _token: "{{ csrf_token() }}",
            approval_number: approval_number
        };

        $.post( "{{ url('approval/cancel') }}", data, function( data ) {
            if (data.error) {
                alert(data.error);
                return false;
            };

            console.log('update data table');
            table.api().ajax.reload( null, false );
        });
    };

    return false;
}
    

function validateApproval(approval_number)
{
    var confirmed = confirm('Are you sure to validate Approval: '+approval_number+'?');

    if (confirmed == true) {
        $('#'+approval_number).hide();
        var data = {
            _token: "{{ csrf_token() }}",
            approval_number: approval_number
        };

        $.post( "{{ url('approval/approve') }}", data, function( data ) {
            if (data.error) {
                alert(data.error);
                return false;
            };

            console.log('update data table');
            table.api().ajax.reload( null, false );
        });
    };

    // return false;
}

    
$(function () {

    $.getJSON("{{ url('statistic/ex') }}", function (dataJSON) {

        Highcharts.setOptions({
            lang: {
                decimalPoint: '.',
                thousandsSep: ','
            }
        });

        $('#chart1').highcharts({
            chart: {
                type: 'bar',
                zoomType: 'y'
            },
            credits: {
                enabled: false
            },
            title: {
                text: null,
            },
            xAxis: {
                categories: ['% Used'],
            },
            yAxis: {
                min: 0,
                tickInterval: dataJSON[5].attrTick,
                title: {
                    text: null
                }
            },
            tooltip: {
                pointFormat: '{series.name}: <b>IDR {point.y:,.0f} Million</b> ({point.percentage:,.0f}%)<br/>'
            },
            legend: {
                reversed: true,
                y: 5
            },
            plotOptions: {
                bar: {
                    grouping: false,
                    shadow: false,
                    borderWidth: 1,
                    events: {
                        legendItemClick: function () {
                            return false; 
                        }
                    }
                },
            },
            series: [{
                stack: 0,
                stacking: 'normal',
                name: dataJSON[5].attrPlanTitle,
                dataLabels: {
                    enabled: true,
                    align: 'right',
                    color: '#FFFFFF',
                    format: 'IDR {point.y:,.0f} Mio'
                },
                data: dataJSON[0].totPlan,
                color: Highcharts.getOptions().colors[dataJSON[5].attrPlanColor]
            }, {
                name: 'Dummy',
                data: dataJSON[3].totDummy,
                color: 'White',
                pointPadding: 2,
                showInLegend: false,
                stacking: dataJSON[5].attrStack
            }, {
                name: 'Outlook',
                data: dataJSON[4].totOutlook,
                dataLabels: {
                    enabled: true,
                    format: 'IDR {point.y:,.0f} Mio'
                },
                color: Highcharts.getOptions().colors[3],
                pointPadding: 0.3,
                pointPlacement: -0.0,
                stacking: dataJSON[5].attrStack
            }, {
                name: 'Unbudget',
                data: dataJSON[2].totUnbudget,
                dataLabels: {
                    enabled: true,
                    format: 'IDR {point.y:,.0f} Mio'
                },
                color: Highcharts.getOptions().colors[1],
                pointPadding: 0.3,
                pointPlacement: -0.0,
                stacking: dataJSON[5].attrStack
            }, {
                name: 'Used',
                data: dataJSON[1].totUsed,
                dataLabels: {
                    enabled: true,
                    format: 'IDR {point.y:,.0f} Mio'
                },
                color: Highcharts.getOptions().colors[2],
                pointPadding: 0.3,
                pointPlacement: -0.0,
                stacking: dataJSON[5].attrStack
            }]
        });
    });
});

