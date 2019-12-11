
var data = $.ajax({
    url: `${SITE_URL}/dashboard/get`,
    data: {
        plan_type: $('[name="plan_type"]').val(),
        interval: $('[name="interval"]').val(),
        division: $('[name="division"]').val(),
        department: $('[name="department"]').val(),
    },
    type: 'get',
    dataType: 'json',
    async: false
}).responseJSON;

var data1 = [data.data.capexes.free, data.data.capexes.unbudget, data.data.capexes.normal_used];
var data2 = [data.data.expenses.free, data.data.expenses.unbudget, data.data.expenses.normal_used];
var data3 = data.data.capex_bar;
var data4 = data.data.expense_bar;


$('#capex-plan').text(`Capex Plan : IDR ${ data1.reduce((total, num) => total + num) } Billion`)
$('#expense-plan').text(`Expense Plan : IDR ${ data2.reduce((total, num) => total + num) } Billion`)

var data = {
    datasets: [{
        data: data1,
        backgroundColor: ['rgba(249, 200, 81, 1)', 'rgba(24, 136, 226, 1)', 'rgba(58, 201, 214, 1)']
    }],

    labels: [
        'Free',
        'Unbudget',
        'Normal Used'
    ]
};

var datadata = {
    datasets: [{
        data: data2,
        backgroundColor: ['rgba(249, 200, 81, 1)', 'rgba(24, 136, 226, 1)', 'rgba(58, 201, 214, 1)']
    }],

    labels: [
        'Free',
        'Unbudget',
        'Normal Used'
    ]
};

var myPieChart = new Chart($('#chart'), {
    type: 'pie',
    data: data,
    options: {
        tooltips: {
            callbacks:{
                label: (i, data) => {
                    var value = (data.datasets[0].data[i.index]);
                    var percent = Math.round(value / data.datasets[0].data.reduce((total, num) => total + num) * 100);
                    return `IDR ${value} bil (${percent}%)`;
                }
            }
        }
    }
});

var myPieChart = new Chart($('#chart2'), {
    type: 'pie',
    data: datadata,
    options: {
        tooltips: {
            callbacks:{
                label: (i, data) => {
                    var value = (data.datasets[0].data[i.index]);
                    var percent = Math.round(value / data.datasets[0].data.reduce((total, num) => total + num) * 100);
                    return `IDR ${value} bil (${percent}%)`;
                }
            }
        }
    }
});


var stackedBar = new Chart($('#chart3'), {
    type: 'bar',
    data: {

    datasets: [{
            label: 'Cum Plan',
            data: data3.cum_plan,
            backgroundColor: 'transparent',
            borderColor: 'rgba(54, 162, 235, 1)',
            type: 'line'
        },{
            label: 'Cum Actual',
            data: data3.cum_actual,
            backgroundColor: 'transparent',
            borderColor: 'rgba(153, 102, 255, 1)',
            type: 'line'
        },
        {
            label: 'Plan',
            backgroundColor: 'rgba(255, 99, 132, 1)',
            data: data3.plan
        },{
            label: 'Unbudget',
            backgroundColor: 'rgba(255, 206, 86, 1)',
            data: data3.unbudget
        },{
            label: 'Actual',
            backgroundColor: 'rgba(75, 192, 192, 1)',
            data: data3.actual
        }],

        labels: formatLabel(data3.keys)
    },
    options: {
        scales: {
            yAxes: [{
              scaleLabel: {
                display: true,
                labelString: 'Amount IDR in Billion'
              }
            }]
        },
    }
});

var stackedBar2 = new Chart($('#chart4'), {
    type: 'bar',
    data:  {
        datasets: [{
            label: 'Cum Plan',
            data: data4.cum_plan,
            backgroundColor: 'transparent',
            borderColor: 'rgba(54, 162, 235, 1)',
            type: 'line'
        },{
            label: 'Cum Actual',
            data: data4.cum_actual,
            backgroundColor: 'transparent',
            borderColor: 'rgba(153, 102, 255, 1)',
            type: 'line'
        },
        {
            label: 'Plan',
            backgroundColor: 'rgba(255, 99, 132, 1)',
            data: data4.plan
        },{
            label: 'Unbudget',
            backgroundColor: 'rgba(255, 206, 86, 1)',
            data: data4.unbudget
        },{
            label: 'Actual',
            backgroundColor: 'rgba(75, 192, 192, 1)',
            data: data4.actual
        }],

        labels: formatLabel(data4.keys)
    },
    options: {
        scales: {
            yAxes: [{
              scaleLabel: {
                display: true,
                labelString: 'Amount IDR in Billion'
              }
            }]
        },
    }
});

let intVal = $('#interval').val();
let intValArr = intVal.split(' - ');

let intVal1 = intValArr[0];
let intVal2 = intValArr[1];

$('#interval').daterangepicker({
    locale: {
        format: 'DD/MM/YYYY',
    },
    startDate: intVal1,
    endDate: intVal2,
    buttonClasses: ['btn', 'btn-sm'],
    applyClass: 'btn-success',
    cancelClass: 'btn-default',
});


$(document).ready(function(){
    $('.dropdown-submenu a.test').on("click", function(e){
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
});


function formatLabel(data) {
    return data.map(function(month) {
        switch(month){
            case '01' :
                return 'Jan';
            case '02' :
                return 'Feb';
            case '03' :
                return 'Mar';
            case '04' :
                return 'Apr';
            case '05' :
                return 'May';
            case '06' :
                return 'Jun';
            case '07' :
                return 'Jul';
            case '08' :
                return 'Aug';
            case '09' :
                return 'Sep';
            case '10' :
                return 'Oct';
            case '11' :
                return 'Sep';
            default :
                return 'Dec'
        }
    })
}

$('.change-plan').click(function(e) {
    e.preventDefault();
    var value = $(this).data('value');

    if (value === 'ori') {
        $('#plan-type').text('Original Plan');
    } else {
        $('#plan-type').text('Revised Plan');
    }

    $('[name="plan_type"]').val(value);

})