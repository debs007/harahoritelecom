import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Update all .cart-badge elements with the current count.
 */
window.updateCartBadge = function (count) {
    document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
};

/**
 * Add a product (+ optional variant) to the cart via AJAX.
 * Call from any Blade template: onclick="addToCart(1, null, 1)"
 */
window.addToCart = function (productId, variantId = null, qty = 1) {
    const btn = event?.currentTarget;
    const originalHTML = btn?.innerHTML;

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg> Adding…`;
    }

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
        body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: qty }),
    })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                showToast(data.error, 'error');
            } else {
                window.updateCartBadge(data.count);
                showToast('Added to cart! 🛒', 'success');
            }
        })
        .catch(() => showToast('Something went wrong.', 'error'))
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        });
};

/**
 * Toggle wishlist for a product via AJAX.
 */
window.toggleWishlist = function (btn, productId) {
    fetch(`/wishlist/toggle/${productId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' },
    })
        .then(r => r.json())
        .then(data => {
            const svg = btn.querySelector('svg');
            if (svg) {
                svg.style.fill   = data.inWishlist ? '#ef4444' : 'none';
                svg.style.stroke = data.inWishlist ? '#ef4444' : 'currentColor';
            }
            showToast(data.inWishlist ? 'Added to wishlist ❤️' : 'Removed from wishlist', 'success');
        });
};

/**
 * Simple toast notification.
 */
window.showToast = function (message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-24 md:bottom-6 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-2xl
        text-sm font-semibold shadow-xl transition-all duration-300
        ${type === 'success' ? 'bg-gray-900 text-white' : 'bg-red-600 text-white'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};

// ── On page load: fetch cart count ───────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetch('/cart/count')
        .then(r => r.json())
        .then(d => window.updateCartBadge(d.count ?? 0))
        .catch(() => {});
});
