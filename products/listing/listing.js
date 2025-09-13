import { validateQuantityInput,  handleAddToCart } from "/Wad_assignment/utils/utils.js";

window.addEventListener ("load", () => {
  loadCategories();
  loadProducts();
});

document.querySelector("#searchContainer form").addEventListener("submit", (event)=>{
  event.preventDefault();
  loadProducts();
});

document.getElementById("btnFilterProduct").addEventListener("click", (event) => {
  event.preventDefault();
  loadProducts();
})

document.getElementById("btnSortProduct").addEventListener("click", () => {
  loadProducts();
})

function renderProducts(products) {
  const grid = document.getElementById("productListGrid");
  grid.innerHTML = "";

  products.forEach((product) => {
    //anchor
    const link = document.createElement("a");
    link.className = "product-link";
    link.href=`/Wad_assignment/products/details/index.php?id=${product.product_id}`;
    
    //product-container
    const container = document.createElement("div");
    container.className = "product-container";

    //image-container
    const imageContainer = document.createElement("div");
    imageContainer.className = "product-image-container";
    const image = document.createElement("img");
    image.className = "product-image";
    image.src = `/Wad_assignment/img/${product.product_img}`;
    image.alt = product.product_name;
    imageContainer.appendChild(image);

    //info-container
    const infoContainer = document.createElement("div");
    infoContainer.className = "product-infoContainer";

    //name-container
    const nameLabel = document.createElement("div");
    nameLabel.className = "product-label";
    nameLabel.textContent = "Name:";

    const nameValue = document.createElement("div");
    nameValue.className = "product-value";
    nameValue.textContent = product.product_name;

    infoContainer.appendChild(nameLabel);
    infoContainer.appendChild(nameValue);

    //price-container
    const priceLabel = document.createElement("div");
    priceLabel.className = "product-label";
    priceLabel.textContent = "Price:";

    const priceValue = document.createElement("div");
    priceValue.className = "product-value";
    priceValue.textContent = "RM " + product.product_price;

    infoContainer.appendChild(priceLabel);
    infoContainer.appendChild(priceValue);

    //stock-container
    const quantityLabel = document.createElement("div");
    quantityLabel.className = "product-label";
    quantityLabel.textContent = "Quantity: ";

    const quantityValue = document.createElement("div");
    quantityValue.className = "product-value";
    quantityValue.textContent = product.stock_qty;

    infoContainer.appendChild(quantityLabel);
    infoContainer.appendChild(quantityValue);

    //category-container
    const categoryLabel = document.createElement("div");
    categoryLabel.className = "product-label";
    categoryLabel.textContent = "Category: ";

    const categoryValue = document.createElement("div");
    categoryValue.className = "product-value";
    categoryValue.textContent = product.category_name;

    infoContainer.appendChild(categoryLabel);
    infoContainer.appendChild(categoryValue);

    //button-container
    const buttonContainer = document.createElement("div");
    buttonContainer.className = "product-button-container";

    //Add to Cart
    const addCartQtyInput = document.createElement("input");
    addCartQtyInput.className = "qtyInput";
    addCartQtyInput.type = "number";
    addCartQtyInput.min = "1";
    addCartQtyInput.value = "1";
    addCartQtyInput.max = `${product.stock_qty}`;
    validateQuantityInput(addCartQtyInput, product.stock_qty);

    const addToCartBtn = document.createElement("button");
    addToCartBtn.textContent = "Add to Cart";

    addToCartBtn.addEventListener("click", ()=>{
      handleAddToCart(product, addCartQtyInput);
    });

    buttonContainer.appendChild(addCartQtyInput);
    buttonContainer.appendChild(addToCartBtn);

    //combine all
    link.appendChild(imageContainer);
    link.appendChild(infoContainer);
    container.appendChild(link);
    container.appendChild(buttonContainer);
    grid.appendChild(container);
  })
}

function loadCategories() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "fetchCategories.php", true);
  xhr.responseType = "json";

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const categories = xhr.response;
      console.log("Categories", categories)

      if (categories && categories.length > 0 ) {
        const select = document.getElementById("filterCategory");
        categories.forEach(category => {
          const option = document.createElement("option");
          option.value = category.category_id;
          option.textContent = category.category_name;
          select.appendChild(option);
        });
      }
    } 
  }
  xhr.send();
};

function loadProducts() {
  //search
  const searchTerm = document.querySelector("#searchContainer input[name='search']").value.trim();

  //sort
  const sort = document.getElementById("sortProduct").value;
  const order = document.getElementById("orderProduct").value;

  //filter
  const filterCategory = document.getElementById("filterCategory").value;
  const filterMinPrice = document.getElementById("filterMinPrice").value;
  const filterMaxPrice = document.getElementById("filterMaxPrice").value;

  const params = new URLSearchParams({
    search: searchTerm,
    sortProduct: sort,
    orderProduct: order,
    filterCategory: filterCategory,
    filterMinPrice: filterMinPrice,
    filterMaxPrice: filterMaxPrice,
  });

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "fetchProduct.php?" + params.toString(), true);
  xhr.responseType = "json";

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const products = xhr.response.products;
      console.log("Products received: ", products);
      
      if (products) {
        renderProducts(products);
      }
    }
  };
  xhr.send();
}