$(document).ready(function() {
    initQuantity();
});

function initQuantity() {
    // Handle product quantity input
    if ($(".product_quantity").length) {
        $(".cart_list li").each(function(v, i) {
            var input = $(this).find(".quantity_input");
            var incButton = $(this).find(".quantity_inc");
            var decButton = $(this).find(".quantity_dec");

            var originalVal;
            var endVal;

            incButton.on("click", function() {
                originalVal = input.val();
                endVal = parseFloat(originalVal) + 1;
                input.val(endVal);
                id = input.data("id");
                data = updateCart(id, endVal);
                updateDOM(id, data);
            });

            decButton.on("click", function() {
                originalVal = input.val();
                if (originalVal > 0) {
                    endVal = parseFloat(originalVal) - 1;
                    input.val(endVal);
                    id = input.data("id");
                    data = updateCart(id, endVal);
                    updateDOM(id, data);
                }
            });
        });
    }
}

function updateCart(id, qty) {
    var res = $.ajax({
        url: `${SITE_URL}/cart/${id}`,
        type: "put",
        dataType: "json",
        data: {
            qty: qty
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        async: false
    });

    return res.responseJSON;
}

function updateDOM(id, result) {
    $("#field_total_" + id).text("Rp. " + result.data["total_formatted"]);
    $(".field_total_amount").text("Rp. " + result.total);
}

$(".btn-remove").click(function(e) {
    e.preventDefault();
    var id = $(this).data("id");
    $.ajax({
        url: `${SITE_URL}/cart/${id}`,
        type: "delete",
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: function(data) {
            $(".field_total_amount").text("Rp. " + data.total);
            $(`#${id}`).fadeOut("fast");
        }
    });
});

$("#btn-checkout").click(function() {
    $("#modal-cart").modal("show");
});
