fetch('cart.php?action=fetch')
.then(response => response.json())
.then(data => {
  const container = document.getElementById('cartContent');
  if (!data.success || data.cartItems.length === 0) {
    container.innerHTML = '<p>Your cart is empty.</p>';
    return;
  }

  let html = `<form id="cartForm">
    <table class="cart-table">
      <thead>
        <tr>
          <th>Select</th>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>`;

  data.cartItems.forEach(item => {
    html += `<tr>
      <td><input type="checkbox" name="checkout_items" value="${item.product_id}" checked></td>
      <td>
        <div class="img-td">
          <img src="/Wad_assignment/img/${item.product_img}" alt="${item.product_name}">
          <span class="product-name">${item.product_name}</span>
        </div>
      </td>
      <td>${item.category_name}</td>
      <td>RM${parseFloat(item.product_price).toFixed(2)}</td>
      <td>
        <input type="number" class="qtyInput" data-id="${item.product_id}" name="quantity_${item.product_id}" value="${item.cart_item_qty}" min="1" max="${item.stock_qty}" style="width:60px;">
      </td>
      <td class="subtotal">RM${(item.product_price * item.cart_item_qty).toFixed(2)}</td>
      <td>
        <button type="button" class="deleteBtn" data-id="${item.product_id}">Delete</button>
      </td>
    </tr>`;
  });

  html += `</tbody>
    <tfoot>
      <tr>
        <td colspan="5" style="text-align:right;"><strong>Total:</strong></td>
        <td id="cartTotal"><strong>RM${parseFloat(data.total).toFixed(2)}</strong></td>
      </tr>
    </tfoot>
  </table>
  <button type="button" id="checkoutBtn">Checkout</button>
  </form>`;
  container.innerHTML = html;

  const form = document.getElementById('cartForm');

  if (form) {
    // Handle quantity updates
    form.querySelectorAll(".qtyInput").forEach((input, idx) => {
      input.addEventListener("input", function(e) {
        const productId = e.target.getAttribute("data-id");
        let qty = parseInt(e.target.value) || 1;
        const item = data.cartItems.find(it => it.product_id == productId);
        if (qty < 1) qty = 1;
        if (qty > item.stock_qty) qty = item.stock_qty;
        e.target.value = qty;

        // Update subtotal locally
        const subtotal = qty * item.product_price;
        form.querySelectorAll('.subtotal')[idx].textContent = "RM" + subtotal.toFixed(2);
        updateTotal();

        // Send update to server
        fetch(`cart.php?action=update&product_id=${productId}&quantity=${qty}`)
          .then(res => res.json())
          .then(resp => {
            if (!resp.success) {
              alert(resp.message || "Failed to update cart.");
              e.target.value = resp.newQty ?? item.cart_item_qty;
              item.cart_item_qty = resp.newQty ?? item.cart_item_qty; 
              updateTotal();
            } else {
              item.cart_item_qty = resp.newQty; 
            }
          });
      });
    });

    // Recalculate total when checkboxes change
    form.querySelectorAll('[name="checkout_items"]').forEach(cb => {
      cb.addEventListener("change", updateTotal);
    });

    // Dynamic subtotal and total calculation
    function updateTotal() {
      let total = 0;
      data.cartItems.forEach((item, idx) => {
        const checkbox = form.querySelectorAll('[name="checkout_items"]')[idx];
        const subtotalCell = form.querySelectorAll('.subtotal')[idx];
        if (checkbox && checkbox.checked && subtotalCell) {
          const value = parseFloat(subtotalCell.textContent.replace(/[^\d.]/g, ""));
          if (!isNaN(value)) total += value;
        }
      });
      document.getElementById('cartTotal').innerHTML = "<strong>RM" + total.toFixed(2) + "</strong>";
    }

    updateTotal();

    // Delete button functionality
    container.addEventListener('click', function(e) {
      if (e.target.classList.contains('deleteBtn')) {
        const productId = e.target.getAttribute('data-id');
        if (!confirm("Remove this item?")) return;
        fetch(`cart.php?action=remove&product_id=${productId}`)
          .then(res => res.json())
          .then(resp => {
            if (resp.success) location.reload();
            else alert("Failed to remove item.");
          });
      }
    });

    // Checkout button to place order
    document.getElementById('checkoutBtn').addEventListener('click', function() {
      const selected = [];
      data.cartItems.forEach((item, idx) => {
        const checkbox = form.querySelectorAll('[name="checkout_items"]')[idx];
        if (checkbox.checked) {
          let qty = parseInt(form.querySelectorAll(".qtyInput")[idx].value) || 1;
          if (qty < 1) qty = 1;
          if (qty > item.stock_qty) {
            alert(`Quantity for ${item.product_name} exceeds available stock.`);
            qty = item.stock_qty;
          }
          selected.push({product_id: item.product_id, quantity: qty});
        }
      });
      if (selected.length === 0) {
        alert("Please select at least one item to checkout.");
        return;
      }
      fetch('/Wad_assignment/order/order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({items: selected})
      })
      .then(res => res.json())
      .then(resp => {
        alert(resp.message || "Order placed!");
        if (resp.success) location.reload();
      });
    });
  }
});