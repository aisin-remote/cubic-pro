var tApprovalExpense;
var isDeptHead = window.isDeptHead || "";
$(document).ready(function () {
    var columns = [];

    // Tambahkan kolom checkbox jika department-head
    if (isDeptHead === "department-head") {
        columns.push({
            data: "approval_number",
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `<input type="checkbox" class="row-checkbox" value="${data}" data-total="${row.total}" />`;
            },
        });
    }

    // Kolom-kolom utama
    columns = columns.concat([
        {
            data: "departments.department_name",
            name: "departments.department_name",
        },
        { data: "approval_number", name: "approval_number" },
        { data: "total", name: "total" },
        { data: "status", name: "status" },
        {
            data: "overbudget_info",
            name: "overbudget_info",
            orderable: false,
            searchable: false,
        },
        {
            data: "action",
            name: "action",
            searching: false,
            sorting: false,
            class: "text-center",
        },
    ]);

    // Inisialisasi DataTable
    tApprovalExpense = $("#table-list-approval-expense").DataTable({
        ajax: SITE_URL + "/approval-expense/approval_expense/need_approval",
        columns: columns,
        order: [[isDeptHead === "department-head" ? 2 : 1, "asc"]], // order by approval_number
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
        },
    });

    // Hitung total dari checkbox yang dicentang
    $("#table-list-approval-expense").on(
        "change",
        ".row-checkbox",
        function () {
            calculateSelectedTotal();
        }
    );

    // Select All
    $("#table-list-approval-expense").on("change", "#select-all", function () {
        const isChecked = $(this).is(":checked");
        $(".row-checkbox").prop("checked", isChecked);
        calculateSelectedTotal();
    });

    // Fungsi untuk menjumlahkan total dari semua row yang dicentang
    function calculateSelectedTotal() {
        let total = 0;

        $(".row-checkbox:checked").each(function () {
            let raw = $(this).data("total");
            if (typeof raw === "string") {
                raw = raw.replace(/,/g, ""); // Hilangkan koma (pemisah ribuan)
            }
            const rowTotal = parseFloat(raw) || 0;
            total += rowTotal;
        });

        const formatter = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        });

        $("#selected-total").text("Total: " + formatter.format(total));
    }

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#btn-approve-selected").on("click", function () {
        const selected = $(".row-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selected.length === 0) {
            alert("Please select at least one approval.");
            return;
        }

        if (confirm("Approve " + selected.length + " selected approval(s)?")) {
            $.ajax({
                url: SITE_URL + "/approval/approve-multiple",
                type: "POST",
                data: { approval_numbers: selected },
                success: function (data) {
                    if (data.error) {
                        show_notification("Error", "error", data.error);
                    } else {
                        show_notification("Success", "success", data.success);
                        tApprovalExpense.draw();
                    }
                },
            });
        }
    });

    $("#table-approval-expense tbody").on(
        "click",
        "td.details-control",
        function () {
            var tr = $(this).closest("tr");
            var row = tApprovalExpense.row(tr);
            var tableId = "posts-" + row.data().id;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass("shown");
            } else {
                // Open this row
                row.child(template(row.data())).show();
                initTable(tableId, row.data());
                tr.addClass("shown");
                tr.next().find("td").addClass("no-padding bg-gray");
            }
        }
    );

    function initTable(tableId, data) {
        $("#" + tableId).DataTable({
            /*processing: true,
            serverSide: true,*/
            ajax: data.details_url,
            columns: [
                { data: "budget_no", name: "budget_no" },
                { data: "project_name", name: "project_name" },
                // { data: 'asset_kind', name: 'asset_kind'},
                // { data: 'asset_category', name: 'asset_category'},
                { data: "sap_costs.cc_fname", name: "sap_costs.cc_fname" },
            ],
            ordering: false,
            searching: false,
            paging: false,
            info: false,
        });
    }

    function template(d) {
        // console.log(d);

        return `

                <table class="table details-table" id="posts-${d.id}">
                    <thead>
                    <tr>
                        <th>Budget No</th>
                        <th>Project Name</th>
                        <th>Cost Center</th>
                    </tr>


            </table>

        `;
    }

    $("#btn-confirm").click(function () {
        var approval_expense_id = $(this).data("value");
        $("#form-delete-" + approval_expense_id).submit();
    });

    $.getJSON(SITE_URL + "/statistic/ex", function (dataJSON) {
        Highcharts.chart("chart1", {
            chart: {
                type: "bar",
                zoomType: "y",
            },
            credits: {
                enabled: false,
            },
            title: {
                text: null,
            },
            xAxis: {
                categories: ["% Used"],
            },
            yAxis: {
                min: 0,
                tickInterval: dataJSON[5].attrTick,
                title: {
                    text: null,
                },
            },
            tooltip: {
                pointFormat:
                    "{series.name}: <b>IDR {point.y:,.0f} Million</b> ({point.percentage:,.0f}%)<br/>",
            },
            legend: {
                reversed: true,
                y: 5,
            },
            plotOptions: {
                bar: {
                    grouping: false,
                    shadow: false,
                    borderWidth: 1,
                    events: {
                        legendItemClick: function () {
                            return false;
                        },
                    },
                },
            },
            series: [
                {
                    stack: 0,
                    stacking: "normal",
                    name: dataJSON[5].attrPlanTitle,
                    dataLabels: {
                        enabled: true,
                        align: "right",
                        color: "#FFFFFF",
                        format: "IDR {point.y:,.0f} Mio",
                    },
                    data: dataJSON[0].totPlan,
                    // color: .getOptions().colors[dataJSON[5].attrPlanColor]
                },
                {
                    name: "Dummy",
                    data: dataJSON[3].totDummy,
                    color: "blue",
                    pointPadding: 2,
                    showInLegend: false,
                    stacking: dataJSON[5].attrStack,
                },
                {
                    name: "Outlook",
                    data: dataJSON[4].totOutlook,
                    dataLabels: {
                        enabled: true,
                        format: "IDR {point.y:,.0f} Mio",
                    },
                    // color: .getOptions().colors[3],
                    pointPadding: 0.3,
                    pointPlacement: -0.0,
                    stacking: dataJSON[5].attrStack,
                },
                {
                    name: "Unbudget",
                    data: dataJSON[2].totUnbudget,
                    dataLabels: {
                        enabled: true,
                        format: "IDR {point.y:,.0f} Mio",
                    },
                    // color: .getOptions().colors[1],
                    pointPadding: 0.3,
                    pointPlacement: -0.0,
                    stacking: dataJSON[5].attrStack,
                },
                {
                    name: "Used",
                    data: dataJSON[1].totUsed,
                    dataLabels: {
                        enabled: true,
                        format: "IDR {point.y:,.0f} Mio",
                    },
                    // color: .getOptions().colors[2],
                    pointPadding: 0.3,
                    pointPlacement: -0.0,
                    stacking: dataJSON[5].attrStack,
                },
            ],
        });
    });
});

function validateApproval(approval_number) {
    var confirmed = confirm(
        "Are you sure to validate Approval: " + approval_number + "?"
    );

    if (confirmed == true) {
        var data = {
            approval_number: approval_number,
        };

        $.getJSON(SITE_URL + "/approval/approve", data, function (data) {
            if (data.error) {
                show_notification("Error", "error", data.error);
                return false;
            } else {
                show_notification("Success", "success", data.success);
                tApprovalExpense.draw();
            }
        });
    }

    return false;
}
function cancelApproval(approval_number) {
    var confirmed = confirm(
        "Are you sure to cancel Approval: " + approval_number + "?"
    );

    if (confirmed == true) {
        var data = {
            approval_number: approval_number,
        };

        $.getJSON(
            SITE_URL + "/approval/cancel_approval",
            data,
            function (data) {
                if (data.error) {
                    show_notification("Error", "error", data.error);
                } else {
                    show_notification("Success", "success", data.success);
                    tApprovalExpense.draw();
                }
            }
        );
    }
}
function on_delete(approval_expense_id) {
    $("#modal-delete-confirm").modal("show");
    $("#btn-confirm").data("value", approval_expense_id);
}
function printApproval(approval_number) {
    window.location.href(
        SITE_URL + "/approval/print_approval_excel/" + approval_number
    );
}
