async function loadProducts() {
  const response = await fetch('http://localhost:3000/api/products');
  const product = await response.json();
  
}