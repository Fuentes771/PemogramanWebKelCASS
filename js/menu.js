document.addEventListener('DOMContentLoaded', function() {
    // Toggle cart visibility
    const cartToggle = document.querySelector('.cart-toggle');
    const cartContainer = document.getElementById('cartContainer');
    const closeCart = document.querySelector('.close-cart');
    
    cartToggle.addEventListener('click', function() {
        cartContainer.classList.toggle('open');
    });
    
    closeCart.addEventListener('click', function() {
        cartContainer.classList.remove('open');
    });
    
    // Show cart message notification
    const cartMessage = document.getElementById('cartMessage');
    if (cartMessage) {
        setTimeout(function() {
            cartMessage.classList.add('show');
            setTimeout(() => {
                cartMessage.classList.remove('show');
            }, 3000);
        }, 100);
    }
    
    // Close cart when clicking outside
    document.addEventListener('click', function(event) {
        if (cartContainer.classList.contains('open') && 
            !cartContainer.contains(event.target) && 
            event.target !== cartToggle && 
            !cartToggle.contains(event.target)) {
            cartContainer.classList.remove('open');
        }
    });
    
    // Quantity controls for menu items
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.quantity-input');
            if (input.value > 1) {
                input.stepDown();
            }
        });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentNode.querySelector('.quantity-input').stepUp();
        });
    });
    
    // Quantity controls in cart
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.qty-input');
            if (input.value > 1) {
                input.stepDown();
            }
        });
    });
    
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentNode.querySelector('.qty-input').stepUp();
        });
    });
    
    // Proceed to checkout button
    const checkoutBtn = document.querySelector('.checkout-btn[type="button"]');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            window.location.href = 'php/checkout.php';
        });
    }
});