document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentDetails = document.querySelectorAll('.payment-details');
    const paymentMethodInput = document.getElementById('selected_payment_method');
    const paymentInstructions = document.querySelectorAll('.payment-instructions');
    
    // Function to switch payment method
    function switchPaymentMethod(method) {
        // Update active state of payment method buttons
        paymentMethods.forEach(m => {
            m.classList.remove('active');
            if (m.dataset.method === method) {
                m.classList.add('active');
            }
        });
        
        // Update visible payment details
        paymentDetails.forEach(detail => {
            detail.classList.remove('active');
            if (detail.id === `${method}-payment`) {
                detail.classList.add('active');
            }
        });

        // Update payment instructions visibility
        paymentInstructions.forEach(instruction => {
            instruction.style.display = 'none';
            if (instruction.dataset.method === method) {
                instruction.style.display = 'block';
            }
        });
        
        // Update hidden input value
        paymentMethodInput.value = method;
    }
    
    // Add click event to all payment methods
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            const methodType = this.dataset.method;
            switchPaymentMethod(methodType);
        });
    });
    
    // Initialize with QRIS selected
    switchPaymentMethod('qris');
    
    // Form submission handling
    const orderForm = document.querySelector('.order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            if (nameInput.value.trim() === '') {
                e.preventDefault();
                alert('Please enter your name');
                nameInput.focus();
            }
        });
    }
});