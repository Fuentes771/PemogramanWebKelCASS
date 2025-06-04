document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));
            
            // Add active class to clicked method
            this.classList.add('active');
            
            // Here you would typically show the corresponding payment details
            // For this example, we only have QRIS
        });
    });
    
    // Form submission handling
    const orderForm = document.querySelector('.order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            // Here you would typically validate the form
            // and possibly show a confirmation dialog
            
            // For this example, we'll just proceed with submission
            // You might want to add more validation here
            const nameInput = document.getElementById('name');
            if (nameInput.value.trim() === '') {
                e.preventDefault();
                alert('Please enter your name');
                nameInput.focus();
            }
        });
    }
});