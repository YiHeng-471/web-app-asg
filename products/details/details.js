import { validateQuantityInput,  handleAddToCart } from "/Wad_assignment/utils/utils.js";

window.addEventListener("load", () => {
  const params = new URLSearchParams(window.location.search);
  const product_id = params.get("id");

  if(!product_id) {
    document.getElementById("details-container").innerHTML = "<p>Product not found</p>"
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "/Wad_assignment/products/details/getDetails.php?id=" + product_id, true);
  xhr.responseType = "json";

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const product = xhr.response.product;
      console.log("Product received: ", product);

      if (product) {
        renderProductDetails(product);
      } else {
        document.getElementById("details-container").textContent = "Product not found.";
      }
    }
  };
  xhr.send();
})

function renderProductDetails(product) {
  //title
  document.title = "Kard | " + product.product_name + " Details";

  //container
  const detailsContainer = document.getElementById("details-container");
  detailsContainer.innerHTML = "";

  //content-container
  const detailsContentContainer = document.createElement("div");
  detailsContentContainer.className = "details-content-container";
    //name container
    const detailsNameContainer = document.createElement("div");
    detailsNameContainer.className = "details-name-container";
      //name
      const detailsName = document.createElement("h2");
      detailsName.className = "details-name";
      detailsName.textContent = `${product.product_name}`;
      detailsNameContainer.appendChild(detailsName);

    //image-info-container
    const detailsImageInfoContainer = document.createElement("div");
    detailsImageInfoContainer.className = "details-image-info-container";
      //image-container
      const detailsImageContainer = document.createElement("div");
      detailsImageContainer.className = "details-image-container";
        //image
        const detailsImage = document.createElement("img");
        detailsImage.className = "details-image";
        detailsImage.src = `/Wad_assignment/img/${product.product_img}`;
        detailsImage.alt = product.product_name;
        detailsImageContainer.appendChild(detailsImage);

      //info-contaniner
      const detailsInfoContainer = document.createElement("div");
      detailsInfoContainer.className = "details-info-container";
        //description
        const detailsDescription = document.createElement("p");
        detailsDescription.className = "details-description";
        detailsDescription.textContent = "Description: " + product.product_desc;
        detailsInfoContainer.appendChild(detailsDescription);

        //price
        const detailsPrice = document.createElement("p");
        detailsPrice.className = "details-price";
        detailsPrice.textContent = "Price: RM" + product.product_price;
        detailsInfoContainer.appendChild(detailsPrice);

        //stock
        const detailsQuantity = document.createElement("p");
        detailsQuantity.className = "details-quantity";
        detailsQuantity.textContent = "Quantity: " + product.stock_qty;
        detailsInfoContainer.appendChild(detailsQuantity);

        //category
        const detailsCategory = document.createElement("p");
        detailsCategory.className = "details-category";
        detailsCategory .textContent = "Category: " + product.category_name;
        detailsInfoContainer.appendChild(detailsCategory );
  
    detailsImageInfoContainer.appendChild(detailsImageContainer);
    detailsImageInfoContainer.appendChild(detailsInfoContainer);

    //Input Button Container 
    const detailsInputBtnContainer = document.createElement("div");
    detailsInputBtnContainer.className = "details-input-button-container"

      //input-container
      const detailsInputContainer = document.createElement("div");
      detailsInputContainer.className = "details-input-container";
        const detailsAddCartQtyInput = document.createElement("input");
        detailsAddCartQtyInput.className = "details-add-cart-quantity-input";
        detailsAddCartQtyInput.type = "number";
        detailsAddCartQtyInput.min = "1";
        detailsAddCartQtyInput.value = "1";
        detailsAddCartQtyInput.max = product.stock_qty;
        
        validateQuantityInput(detailsAddCartQtyInput, product.stock_qty);

      detailsInputContainer.appendChild(detailsAddCartQtyInput);

      //button-container
      const detailsButtonContainer = document.createElement("div");
      detailsButtonContainer.className = "details-button-container";

        const detailsAddToCartBtn = document.createElement("button");
        detailsAddToCartBtn.textContent = "Add to Cart";

        detailsAddToCartBtn.addEventListener("click", ()=>{
          handleAddToCart(product, detailsAddCartQtyInput);
        })
      detailsButtonContainer.appendChild(detailsAddToCartBtn);

    detailsInputBtnContainer.appendChild(detailsButtonContainer);
    detailsInputBtnContainer.appendChild(detailsInputContainer);
  
  detailsContentContainer.appendChild(detailsNameContainer);
  detailsContentContainer.appendChild(detailsImageInfoContainer);
  detailsContentContainer.appendChild(detailsInputBtnContainer);

  //combine all
  detailsContainer.appendChild(detailsContentContainer);
}