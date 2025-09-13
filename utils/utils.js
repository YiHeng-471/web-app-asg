export function validateQuantityInput(qtyInput, maxQty) {
  qtyInput.addEventListener("input", () => {
    let value = parseInt (qtyInput.value) || 1;

    if (value < 1) {
      value = 1;
    }

    if (value > maxQty) {
      value = maxQty;
    } 

    qtyInput.value = value;
  });
};

export function handleAddToCart(product, qtyInput) {
  let qty = parseInt (qtyInput.value) || 1;

  if (qty < 1) {
    alert("Please enter a valid quantity (at least 1).");
    qty = 1;
    qtyInput.value = 1;
    return;
  }

  if (qty > product.stock_qty) {
    alert(`Insufficient amount. Only ${product.stock_qty} items available in stock.`);
    qty = product.stock_qty;
    qtyInput.value = product.stock_qty;
    return;
  }

  addToCart(product.product_id, qty);
};

function addToCart(product_id, addCartQty) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/Wad_assignment/cart/cart.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try{
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            if (response.maxStock !== undefined && addCartQty > response.maxStock) {
              alert(`Only ${response.maxStock} items available in stock.`);
            } else {
              alert(response.message);
            }
          } else {
            alert("Failed to add to cart: " + response.message);
          }
        } catch(err) {
          console.error("Invalid JSON response: ", xhr.responseText);
          alert("Unexpected server response.");
        }
      } else {
        console.error("XHR failed with status: ", xhr.status);
      }
    }
  };
  xhr.send(JSON.stringify({product_id: product_id, quantity:addCartQty}));
};

