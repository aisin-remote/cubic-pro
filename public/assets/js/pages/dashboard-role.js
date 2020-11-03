var format = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,
});

function initInterval(period) {
    var currentYear = period;
    var nextYear = parseInt(currentYear) + 1;
    var minDate = '01/04/' + currentYear;
    var maxDate = '31/03/' + nextYear;

    var confInt = {
        locale: {
            format: 'DD/MM/YYYY',
        },
        startDate: minDate,
        endDate: maxDate,
        minDate: minDate,
        maxDate: maxDate,
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-default'
    };

    $('#interval').daterangepicker(confInt);
}

function getParam() {
    var interval = $('#interval').val().split(' - ');
    var departments = JSON.parse($('#departments').val());

    return {
        startDate : interval[0],
        endDate : interval[1],
        type : $('#plan-type').val(),
        period : $('#period').val(),
        departments : departments
    }
}

function generatePlanChart() {
    var param = getParam();

    $.ajax({
        data: param,
        url: '/dashboard/get/plan',
        type: "get",
        success: function (data) {
            var data = data.data;
            var capex = parseInt(data.capex) / 1000000000;
            var expense = parseInt(data.expense) / 1000000000;
            $('#capex-plan > span').text(capex.toFixed(2));
            $('#expense-plan > span').text(expense.toFixed(2));
            var dataChart = {
                'capex-used' : {
                    'data' : [(data.capex - data.total_capex), data.total_capex, data.total_uc],
                    'labels' : ['Free', 'Normal Used', 'Unbudget'],
                    'colors': [dynamicColors(), dynamicColors(), dynamicColors()]
                },
                'expense-used' : {
                    'data' : [(data.expense - data.total_expense), data.total_expense, data.total_ue],
                    'labels' : ['Free', 'Normal Used', 'Unbudget'],
                    'colors': [dynamicColors(), dynamicColors(), dynamicColors()]
                }
            };

            for(var i in dataChart) {
                $('#'+i).replaceWith($('<canvas id="'+i+'"></canvas>'));
                document.getElementById('no-data-'+i).style.display = 'none';
                document.getElementById(i).style.display = 'block';

                var ctx = document.getElementById(i).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: dataChart[i].data,
                            backgroundColor: dataChart[i].colors,
                        }],
                        labels: dataChart[i].labels,
                    },
                    options: {
                        showDatasetLabels : true,
                        legend: {
                            display: true,
                            position:'bottom',
                            labels: {
                                boxWidth: 15,
                                boxHeight: 2,
                            },
                        },
                        plugins: {
                            labels: {
                                render: 'label',
                                fontColor: 'white',
                                precision: 2,
                                fontStyle: 'bold'
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index];
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    return label + ' : ' + format.format(value);
                                }
                            }
                        }
                    }
                });

                if (dataChart[i].data[0] < 1) {
                    document.getElementById('no-data-'+i).style.display = 'block';
                    document.getElementById(i).style.display = 'none';
                }
            }
        },
        statusCode: {
            500 : function (data) {
                console.log('Internal server error');
            }
        }
    });
}

function last (array, n) {
    if (array == null)
        return 0;

    if (array.length < 1)
        return 0;

    if (n == null)
        return array[array.length - 1];
    return array.slice(Math.max(array.length - n, 0));
}

function generatePanelTitle() {
    var title = $('#panel-title').val();
    $('.panel-title').text(title);
}

function generateSummaryChart() {
    var param = getParam();
    $('.fy-active').text($('#period').val());

    $.ajax({
        data: param,
        url: '/dashboard/get/summary',
        type: "get",
        success: function (data) {
            var data = data.data;
            var monthsStr = {
                "Apr" : "04",
                "May" : "05",
                "Jun" : "06",
                "Jul" : "07",
                "Aug" : "08",
                "Sep" : "09",
                "Oct" : "10",
                "Nov" : "11",
                "Dec" : "12",
                "Jan" : "01",
                "Feb" : "02",
                "Mar" : "03"
            };

            var planCapexInMonths = [];
            var actualCapexInMonths = [];
            var unbudgetCapexInMonths = [];
            var planExpenseInMonths = [];
            var actualExpenseInMonths = [];
            var unbudgetExpenseInMonths = [];
            var cumulativePlanCapex = [];
            var cumulativePlanExpense = [];
            var cumulativeActualCapex = [];
            var cumulativeActualExpense = [];

            for (var monthIndex in monthsStr) {
                if (typeof data.capexes[monthsStr[monthIndex]] == 'undefined') {
                    planCapexInMonths.push(0);
                    cumulativePlanCapex.push(last(cumulativePlanCapex));
                } else {
                    planCapexInMonths.push(parseInt(data.capexes[monthsStr[monthIndex]].total))
                    cumulativePlanCapex.push(last(cumulativePlanCapex) + parseInt(data.capexes[monthsStr[monthIndex]].total));
                }

                if (typeof data.expenses[monthsStr[monthIndex]] == 'undefined') {
                    planExpenseInMonths.push(0);
                    cumulativePlanExpense.push(last(cumulativePlanExpense));
                } else {
                    planExpenseInMonths.push(parseInt(data.expenses[monthsStr[monthIndex]].total))
                    cumulativePlanExpense.push(last(cumulativePlanExpense) + parseInt(data.expenses[monthsStr[monthIndex]].total));
                }

                var totalActualCx = last(cumulativeActualCapex);
                var totalActualEx = last(cumulativeActualExpense);

                if (typeof data.total_capex_per_month[monthsStr[monthIndex]] == 'undefined') {
                    actualCapexInMonths.push(0);
                } else {
                    actualCapexInMonths.push(parseInt(data.total_capex_per_month[monthsStr[monthIndex]].total));
                    totalActualCx += parseInt(data.total_capex_per_month[monthsStr[monthIndex]].total);
                }

                if (typeof data.total_expense_per_month[monthsStr[monthIndex]] == 'undefined') {
                    actualExpenseInMonths.push(0);
                } else {
                    actualExpenseInMonths.push(parseInt(data.total_expense_per_month[monthsStr[monthIndex]].total));
                    totalActualEx += parseInt(data.total_expense_per_month[monthsStr[monthIndex]].total);
                }

                if (typeof data.total_uc_per_month[monthsStr[monthIndex]] == 'undefined') {
                    unbudgetCapexInMonths.push(0);
                } else {
                    unbudgetCapexInMonths.push(parseInt(data.total_uc_per_month[monthsStr[monthIndex]].total));
                    totalActualCx += parseInt(data.total_uc_per_month[monthsStr[monthIndex]].total);
                }

                if (typeof data.total_ue_per_month[monthsStr[monthIndex]] == 'undefined') {
                    unbudgetExpenseInMonths.push(0);
                } else {
                    unbudgetExpenseInMonths.push(parseInt(data.total_ue_per_month[monthsStr[monthIndex]].total));
                    totalActualEx += parseInt(data.total_ue_per_month[monthsStr[monthIndex]].total);
                }

                cumulativeActualCapex.push(totalActualCx);
                cumulativeActualExpense.push(totalActualEx);
            }

            var elementChartBar = {
                'chart-summary-capex' : {
                    'planInMonths' : planCapexInMonths,
                    'actualInMonths' : actualCapexInMonths,
                    'unbudgetInMonths' : unbudgetCapexInMonths,
                    'cumulativePlan' : cumulativePlanCapex,
                    'cumulativeActual' : cumulativeActualCapex
                },
                'chart-summary-expense' : {
                    'planInMonths' : planExpenseInMonths,
                    'actualInMonths' : actualExpenseInMonths,
                    'unbudgetInMonths' : unbudgetExpenseInMonths,
                    'cumulativePlan' : cumulativePlanExpense,
                    'cumulativeActual' : cumulativeActualExpense
                }
            }

            for (element in elementChartBar) {
                $('#'+element).replaceWith($('<canvas id="'+element+'"></canvas>'));
                var label = element == 'chart-summary-capex' ? 'Capex Summary' : 'Expense Summary';

                var ctx = document.getElementById(element).getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(monthsStr),
                        datasets: [
                            {
                                label: 'Plan',
                                backgroundColor: dynamicColors(),
                                stack: 'Stack 0',
                                data: elementChartBar[element].planInMonths
                            },
                            {
                                label: 'Actual',
                                backgroundColor: dynamicColors(),
                                stack: 'Stack 1',
                                data: elementChartBar[element].actualInMonths
                            },
                            {
                                label: 'Unbudget',
                                backgroundColor: dynamicColors(),
                                stack: 'Stack 1',
                                data: elementChartBar[element].unbudgetInMonths
                            },
                            {
                                label: 'Cum. Plan',
                                data: elementChartBar[element].cumulativePlan,
                                type: 'line',
                                backgroundColor: dynamicColors(),
                                fill: false,
                            },
                            {
                                label: 'Cum. Actual',
                                data: elementChartBar[element].cumulativeActual,
                                type: 'line',
                                backgroundColor: dynamicColors(),
                                fill: false,
                            }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: label
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.datasets[tooltipItem.datasetIndex].label;
                                    var value = format.format(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);

                                    return label + " : " + value;
                                }
                            }
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                ticks: {
                                    callback: function(label, index, labels) {
                                        return label/1000000000;
                                    }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Amount IDR in Billion'
                                }
                            }]
                        },
                        plugins: {
                            labels: {
                                render: function (args) {
                                    return '';
                                },
                            }
                        }
                    }
                });
            }
        },
        statusCode: {
            500 : function (data) {
                console.log('Internal server error');
            }
        }
    });
}

function dynamicColors() {
    let r = Math.floor(Math.random() * 255);
    let g = Math.floor(Math.random() * 255);
    let b = Math.floor(Math.random() * 255);
    return "rgb(" + r + "," + g + "," + b + ")";
};

$(document).ready(function() {
    initInterval($('#period').val());

    generatePlanChart();
    generateSummaryChart();
    generatePanelTitle();

    $('.dropdown-submenu a.test').on("click", function(e){
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    $('#period').change(function() {
        $('#interval').data('daterangepicker').remove();
        initInterval($(this).val());
    });

    $('#form-filter').on('submit', function(e) {
        e.preventDefault();

        generatePlanChart();
        generateSummaryChart();
        generatePanelTitle();
    });

    $('#department-code').on('change', function() {
        $('#departments').val($(this).val());
        $('#panel-title').val($(this).find('option:selected').data('title'))
    });

    // download excel
    $('#download-budget').on('click', function() {
        var query = $.param(getParam());

        var url = '/dashboard/download/budget?' + query;

        window.open(url, '_blank');
    });

    $('#download-approval').on('click', function() {
        var query = $.param(getParam());

        var url = '/dashboard/download/approval?' + query;

        window.open(url, '_blank');
    });
});