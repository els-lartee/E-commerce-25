document.addEventListener("DOMContentLoaded", function () {

    // Log product views
    document.querySelectorAll(".product-card").forEach(card => {
        const productId = card.dataset.productId;
        if (productId) {
            logInteraction(productId, "view");
        }
    });

});

function logInteraction(productId, action, duration = 0) {
    fetch("actions/log_interaction.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `product_id=${productId}&action=${action}&duration=${duration}`
    });
}

function startARTryOn(productId) {
    logInteraction(productId, "ar_tryon");

    // your AR logic continues here
}
