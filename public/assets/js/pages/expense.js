var tExpense;
$(document).ready(function(){
    var is_budget = $('#is_budget').val();
    var csrfToken = $('#csrf-token').val();
    tExpense = $('#table-expense').DataTable({
        responsive : true,
        processing : true,
        serverSide : true,
        ajax : SITE_URL + '/expense/get_data',
        fnDrawCallback : function (oSettings) {
            budgetStatusStyler();
            budgetClosingStyler();
            budgetView();
			if(is_budget==1){
				xeditClasser();
				initEditable();
				initSelectable();
			}
        },
        columns : [
            { data: 'budget_no', name: 'budget_no'},
            { data: 'description', name: 'description'},
            { data: 'qty_plan', name: 'qty_plan'},
            { data: 'qty_used', name: 'qty_used'},
            { data: 'qty_remaining', name: 'qty_remaining'},
            {
                data: 'budget_plan',
                name: 'budget_plan',
                className: "center",
                orderable: true,
                searchable: true,
                render: function(data){
                    return parseInt(data).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            {
                data: 'budget_used',
                name: 'budget_used',
                className: "center",
                orderable: true,
                searchable: true,
                render: function(data){
                    return parseInt(data).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            {
                data: 'budget_remaining',
                name: 'budget_remaining',
                className: "center",
                orderable: true,
                searchable: true,
                render: function(data){
                    return parseInt(data).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            {
                data: 'plan_gr',
                name: 'plan_gr',
                className: "center",
                orderable: true,
                searchable: true,
                render: function(data){
                    return moment(data).format('DD-MMM-YYYY');
                }
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'is_closed',
                name: 'is_closed'
            },
            {
                data: null,
                className: "center",
                orderable: false,
                searchable: false,
                render: function(data){
                    if (is_budget == 1) {
                        return '<button class="btn btn-danger btn-xs" data-toggle="tooltip"  title="Hapus" onclick="on_delete('+data.id+')"><i class="mdi mdi-close"></i></button> <form action="/expense/'+data.id+'" method="POST" id="form-delete-'+data.id+'" style="display:none"><input type="hidden" name="_token" value="'+csrfToken+'"><input type="hidden" name="_method" value="DELETE"></form>'
                    } else {
                        return '';
                    }
                }
            }
        ],
        drawCallback: function(d) {
        	$('[data-toggle="popover"]').popover();
        }
    });

    $('#btn-confirm').click(function(){
        var capex_id = $(this).data('value');
        $('#form-delete-' + capex_id).submit();
    });

});

function on_delete(capex_id)
{
    $('#modal-delete-confirm').modal('show');
    $('#btn-confirm').data('value', capex_id);
}

function on_close()
{
	$('[data-toggle="popover"]').popover('hide');
}

function budgetView()
    {
        $('tbody tr[role="row"]').each(function(i, e) {
            var budget_no = $(this).find('td:nth-child(1)');

            // set budget_no anchor
            budget_no.html(`<a href="${SITE_URL}/expense/select/${budget_no.text()}" >${budget_no.text()}</a>`);

        });
    }

function budgetStatusStyler()
{
    $('tr > td:nth-child(7)').each(function(index, element) {
        var value = $(this).text();
        if (value == 'Underbudget') {
            $(this).addClass('success');
        };

        if (value == 'Overbudget') {
            $(this).addClass('danger');
        };
    })
}

function budgetClosingStyler()
{
    $('tr > td:nth-child(8)').each(function(index, element) {
        var value = $(this).text();
        if (value == 'Open') {
            $(this).addClass('info');
        };

        if (value == 'Closed') {
            $(this).addClass('active');
        };
    })
}

function xeditClasser()
{
    var url = SITE_URL + '/expense/xedit';
    $('tbody tr').each(function(i, e) {
        var budget_no = $(this).find('td:nth-child(1)');
        var equipment_name = $(this).find('td:nth-child(2)');
        var budget_plan = $(this).find('td:nth-child(6)');//3
        var budget_remaining = $(this).find('td:nth-child(8)');//5  // hotfix-3.4.11, Ferry, 20160422, Edit budget remaining
        var plan_gr = $(this).find('td:nth-child(9)');//6
        var closing_status = $(this).find('td:nth-child(11)');//8

        // set equipment_name anchor
        equipment_name.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="description" data-title="Enter Equipment Name">'+equipment_name.text()+'</a>');

        // set budget_no anchor
        // budget_no.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="budget_no" data-title="Enter Budget Number">'+budget_no.text()+'</a>');

        // set budget_plan anchor
        budget_plan.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="budget_plan" data-title="Enter Budget Plan">'+budget_plan.text()+'</a>');

        // hotfix-3.4.11, Ferry, 20160421, set budget_remaining anchor
        budget_remaining.html('<a href="#" class="editable" data-pk="'+budget_no.text()+'" data-name="budget_remaining" data-title="Enter Budget Remaining">'+budget_remaining.text()+'</a>');

        // set plan_gr anchor
        // plan_gr.html('<a href="#" class="datepickerable" data-pk="'+budget_no.text()+'" data-name="plan_gr" data-title="Enter Budget Plan" data-value="1984-05-15" data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY" data-template="DD MMM YYYY">'+plan_gr.text()+'</a>');

        // set closing_status anchor
        closing_status.html('<a href="#" class="selectable" data-type="select" data-pk="'+budget_no.text()+'" data-name="is_closed" data-title="Enter Quantity Plan" data-value="'+closing_status.text()+'" data-source="[{value: &#39;Open&#39;, text: &#39;Open&#39;}, {value: &#39;Closed&#39;, text: &#39;Closed&#39;}]">'+closing_status.text()+'</a>');
    });
}
function initEditable()
{
    $('.editable').editable({
        type: 'text',
        url: SITE_URL + '/expense/xedit',
        params: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
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
			tExpense.ajax.reload( null, false );
        }
    });
}

function initSelectable () {
    $('.selectable').editable({
        showbuttons: true,
        url: SITE_URL + '/expense/xedit',
        params: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },

        display: function(value, sourceData) {
             var colors = {"Closed": "warning", "Open": "info"},
                 elem = $.grep(sourceData, function(o){return o.value == value;});

            if (value == 'Closed') {
                $(this).text(elem[0].text).parent().removeClass('info').addClass(colors[value]);
            };

            if (value == 'Open') {
                $(this).text(elem[0].text).parent().removeClass('warning').addClass(colors[value]);
            };
        },
        success: function(data, config) {
            if (data.error) {
                return data.error;
            };

            tExpense.ajax.reload( null, false );
        }
    });
}

function initDatepickerable () {
    $('.datepickerable').editable({
        type: 'combodate',
        url: "{{ url('expense/xedit') }}",
        params: {
            _token: "{{ csrf_token() }}",
        },
    });
}




