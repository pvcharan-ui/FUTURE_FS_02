// js/app.js — minimal cart using localStorage, defensive checks

(function(){
    // wait until DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
      // defensive checks
      if (typeof PRODUCTS === 'undefined' || !Array.isArray(PRODUCTS)) {
        console.error('PRODUCTS is not defined or not an array. Check js/products.js');
        return;
      }
  
      const productsEl = document.getElementById('products');
      const cartCountEl = document.getElementById('cart-count');
      const cartEl = document.getElementById('cart');
      const cartItemsEl = document.getElementById('cart-items');
      const cartTotalEl = document.getElementById('cart-total');
      const openCartBtn = document.getElementById('open-cart');
      const closeCartBtn = document.getElementById('close-cart');
      const checkoutBtn = document.getElementById('checkout');
      const clearCartBtn = document.getElementById('clear-cart');
      const messageEl = document.getElementById('message');
  
      let cart = JSON.parse(localStorage.getItem('mini_cart')) || {};
  
      // render products
      function renderProducts(){
        productsEl.innerHTML = '';
        PRODUCTS.forEach(p => {
          const card = document.createElement('div');
          card.className = 'card';
          card.innerHTML = `
            <img src="${p.img}" alt="${p.title}">
            <h3>${p.title}</h3>
            <p class="price">₹${p.price}</p>
            <button class="button" data-id="${p.id}">Add to cart</button>
          `;
          productsEl.appendChild(card);
        });
        // bind add buttons
        document.querySelectorAll('.card .button').forEach(btn=>{
          btn.addEventListener('click', e => {
            const id = e.target.dataset.id;
            addToCart(Number(id));
          });
        });
      }
  
      // cart helpers
      function saveCart(){ localStorage.setItem('mini_cart', JSON.stringify(cart)); updateCartUI(); }
      function addToCart(id){
        if(cart[id]) cart[id].qty++;
        else{
          const prod = PRODUCTS.find(p=>p.id===id);
          if(!prod){ console.error('Product not found for id', id); return; }
          cart[id] = { id: prod.id, title: prod.title, price: prod.price, qty: 1 };
        }
        saveCart();
        showMessage('Added to cart');
      }
      function removeFromCart(id){
        delete cart[id];
        saveCart();
      }
      function changeQty(id, delta){
        if(!cart[id]) return;
        cart[id].qty += delta;
        if(cart[id].qty <= 0) removeFromCart(id);
        saveCart();
      }
      function clearCart(){
        cart = {};
        saveCart();
        showMessage('Cart cleared');
      }
  
      function updateCartUI(){
        const items = Object.values(cart);
        cartCountEl.innerText = items.reduce((s,i)=>s+i.qty,0) || 0;
        cartItemsEl.innerHTML = '';
        let total = 0;
        if(items.length === 0){
          cartItemsEl.innerHTML = '<p>Your cart is empty</p>';
        } else {
          items.forEach(it=>{
            total += it.price * it.qty;
            const row = document.createElement('div');
            row.className = 'cart-row';
            row.innerHTML = `
              <div style="flex:1"><strong>${it.title}</strong><div>₹${it.price} × ${it.qty}</div></div>
              <div>
                <button class="qty-btn" data-id="${it.id}" data-delta="-1">-</button>
                <button class="qty-btn" data-id="${it.id}" data-delta="1">+</button>
                <button class="qty-btn" data-id="${it.id}" data-action="remove">Remove</button>
              </div>
            `;
            cartItemsEl.appendChild(row);
          });
          // bind qty buttons
          cartItemsEl.querySelectorAll('.qty-btn').forEach(b=>{
            b.addEventListener('click', e=>{
              const id = Number(e.target.dataset.id);
              if(e.target.dataset.action === 'remove'){
                removeFromCart(id);
              } else {
                const delta = Number(e.target.dataset.delta);
                changeQty(id, delta);
              }
            });
          });
        }
        cartTotalEl.innerText = total;
      }
  
      // UI helpers
      function showCart(){ cartEl.classList.remove('hidden'); }
      function hideCart(){ cartEl.classList.add('hidden'); }
      function showMessage(text){
        messageEl.classList.remove('hidden');
        messageEl.className = 'message';
        messageEl.innerText = text;
        setTimeout(()=>{ messageEl.classList.add('hidden') }, 1500);
      }
  
      // events
      openCartBtn.addEventListener('click', showCart);
      closeCartBtn.addEventListener('click', hideCart);
      checkoutBtn.addEventListener('click', ()=>{
        const items = Object.values(cart);
        if(items.length === 0){ showMessage('Cart is empty'); return; }
        const total = items.reduce((s,i)=>s+i.price*i.qty,0);
        showMessage('Order placed. Total: ₹' + total);
        clearCart();
      });
      clearCartBtn.addEventListener('click', clearCart);
  
      // init
      renderProducts();
      updateCartUI();
    });
  })();
  