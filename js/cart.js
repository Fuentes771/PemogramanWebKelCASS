let cart = JSON.parse(localStorage.getItem('beanSceneCart')) || [];

function renderCart() {
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const totalPriceContainer = document.getElementById('totalPriceContainer');
    
    console.log(cart); // Debugging: lihat isi keranjang

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>Keranjang kosong.</p>';
        totalPriceContainer.innerHTML = '';
        return;
    }

    let tableHTML = `<table>
        <thead><tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Aksi</th></tr></thead><tbody>`;
    let totalPrice = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        totalPrice += itemTotal;
        tableHTML += `<tr>
            <td>${item.name}</td>
            <td>Rp ${item.price.toLocaleString('id-ID')}</td>
            <td>${item.qty}</td>
            <td><button onclick="removeFromCart(${item.id})">Hapus</button></td>
        </tr>`;
    });
    tableHTML += `</tbody></table>`;
    cartItemsContainer.innerHTML = tableHTML;

    // Tampilkan total harga
    totalPriceContainer.innerHTML = `<h3>Total: Rp ${totalPrice.toLocaleString('id-ID')}</h3>`;
}

function removeFromCart(id) {
    cart = cart.filter(c => c.id !== id);
    localStorage.setItem('beanSceneCart', JSON.stringify(cart));
    renderCart();
}

document.getElementById('checkoutBtn').addEventListener('click', () => {
    // Simulasi proses checkout dan simpan order ke database
    alert('Silakan lakukan pembayaran melalui QRIS.');
    // Redirect ke halaman pembayaran
    window.location.href = '../php/payment.php';
});


// Inisialisasi render keranjang saat halaman dimuat
renderCart();
