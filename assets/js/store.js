// assets/js/store.js
document.addEventListener('DOMContentLoaded', () => {
  // attach add-to-cart handlers
  document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('click', async (e) => {
      const el = e.target;
      if (!el.classList.contains('add-btn')) return;
      const pid = form.dataset.productId;
      const qtyInput = form.querySelector('.qty');
      const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value || 1)) : 1;
      await addToCart(pid, qty);
    });
  });

  // attach update-cart handler (single button)
  const updateBtn = document.getElementById('update-cart-btn');
  if (updateBtn) {
    updateBtn.addEventListener('click', async (e) => {
      const form = document.getElementById('cart-update-form');
      if (!form) return;
      // collect qtys
      const fd = new FormData();
      fd.append('action', 'update');
      // add all qty inputs named qty[ID]
      form.querySelectorAll('input[name^="qty"]').forEach(inp => {
        fd.append(inp.name, inp.value);
      });
      const res = await fetch('/FUTURE_FS_02/cart_api.php', { method:'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        flash('Cart updated');
        // reload to show recalculated totals (or you can update DOM dynamically)
        setTimeout(()=> location.reload(), 600);
      } else {
        flash(data.message || 'Update failed');
      }
    });
  }

  // small toast
  function flash(msg, timeout = 2000) {
    let el = document.getElementById('site-toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'site-toast';
      document.body.appendChild(el);
    }
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), timeout);
  }

  async function addToCart(pid, qty) {
    const fd = new FormData();
    fd.append('action','add');
    fd.append('product_id', pid);
    fd.append('qty', qty);

    const res = await fetch('/FUTURE_FS_02/cart_api.php', { method:'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      updateCartCount(data.count);
      flash('Added to cart');
    } else {
      flash(data.message || 'Failed to add');
    }
  }

  // cart count element
  function updateCartCount(n) {
    const el = document.getElementById('cart-count');
    if (el) el.textContent = `(${n})`;
  }

  // on load, try to get live count from header data attribute
  const headerCount = document.querySelector('.nav-cart-count');
  if (headerCount) {
    updateCartCount(headerCount.dataset.count || 0);
  }
});
