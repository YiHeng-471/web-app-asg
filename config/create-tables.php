<?php
include('./config.php');

$createCustomer = " 
CREATE TABLE IF NOT EXISTS `customer` (
  `cust_id` INT NOT NULL AUTO_INCREMENT,
  `password` VARCHAR(30) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(11) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `postcode` VARCHAR(5) NOT NULL,
  `state` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`cust_id`),
  UNIQUE INDEX `id_UNIQUE` (`cust_id` ASC) VISIBLE,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `phone_number_UNIQUE` (`phone_number` ASC) VISIBLE)
ENGINE = InnoDB;
";

$createOrder = "
CREATE TABLE IF NOT EXISTS `order` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `address` MEDIUMTEXT NOT NULL,
  `order_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `order_status` VARCHAR(45) NOT NULL,
  `total_amt` DECIMAL(13,2) NOT NULL,
  `cust_id` INT NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE INDEX `order_id_UNIQUE` (`order_id` ASC) VISIBLE,
  INDEX `fk_order_customer1_idx` (`cust_id` ASC) VISIBLE,
  CONSTRAINT `fk_order_customer1`
    FOREIGN KEY (`cust_id`)
    REFERENCES `customer` (`cust_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

$createPayment = "
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` INT NOT NULL AUTO_INCREMENT,
  `pay_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `pay_amt` DECIMAL(13,2) NOT NULL,
  `pay_method` VARCHAR(45) NOT NULL,
  `transaction_status` VARCHAR(45) NOT NULL,
  `order_id` INT NOT NULL,
  `cust_id` INT NOT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE INDEX `payment_id_UNIQUE` (`payment_id` ASC) VISIBLE,
  INDEX `fk_payment_order1_idx` (`order_id` ASC) VISIBLE,
  INDEX `fk_payment_customer1_idx` (`cust_id` ASC) VISIBLE,
  CONSTRAINT `fk_payment_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `order` (`order_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_customer1`
    FOREIGN KEY (`cust_id`)
    REFERENCES `customer` (`cust_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

$createProductCategory = "
CREATE TABLE IF NOT EXISTS `product_category` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE INDEX `category_id_UNIQUE` (`category_id` ASC) VISIBLE)
ENGINE = InnoDB;
";

$createProduct = "
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` INT NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(45) NOT NULL,
  `product_desc` MEDIUMTEXT NOT NULL,
  `product_img` VARCHAR(45) NOT NULL,
  `product_price` DECIMAL(13,2) NOT NULL,
  `stock_qty` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC) VISIBLE,
  INDEX `fk_product_product_category1_idx` (`category_id` ASC) VISIBLE,
  CONSTRAINT `fk_product_product_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `product_category` (`category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

$createCartItem = "
CREATE TABLE IF NOT EXISTS `cart_item` (
  `cart_item_id` INT NOT NULL AUTO_INCREMENT,
  `cart_item_qty` INT NOT NULL,
  `cart_item_price` DECIMAL(13,2) NOT NULL,
  `cart_status` VARCHAR(45) NOT NULL,
  `cust_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  PRIMARY KEY (`cart_item_id`),
  UNIQUE INDEX `cart_item_id_UNIQUE` (`cart_item_id` ASC) VISIBLE,
  INDEX `fk_cart_item_customer1_idx` (`cust_id` ASC) VISIBLE,
  INDEX `fk_cart_item_product1_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_cart_item_customer1`
    FOREIGN KEY (`cust_id`)
    REFERENCES `customer` (`cust_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cart_item_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `product` (`product_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

$createWishlistItem = "
CREATE TABLE IF NOT EXISTS `wishlist_item` (
  `wishlist_item_id` INT NOT NULL AUTO_INCREMENT,
  `wishlist_item_qty` INT NULL,
  `cust_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  PRIMARY KEY (`wishlist_item_id`),
  UNIQUE INDEX `wishlist_item_id_UNIQUE` (`wishlist_item_id` ASC) VISIBLE,
  INDEX `fk_wishlist_item_customer1_idx` (`cust_id` ASC) VISIBLE,
  INDEX `fk_wishlist_item_product1_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_wishlist_item_customer1`
    FOREIGN KEY (`cust_id`)
    REFERENCES `customer` (`cust_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wishlist_item_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `product` (`product_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

$createOrderItem = "
CREATE TABLE IF NOT EXISTS `order_item` (
  `order_item_id` INT NOT NULL AUTO_INCREMENT,
  `order_item_qty` INT NOT NULL,
  `order_item_price` DECIMAL(13,2) NOT NULL,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  PRIMARY KEY (`order_item_id`),
  UNIQUE INDEX `order_item_id_UNIQUE` (`order_item_id` ASC) VISIBLE,
  INDEX `fk_order_item_order1_idx` (`order_id` ASC) VISIBLE,
  INDEX `fk_order_item_product1_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_order_item_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `order` (`order_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_item_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `product` (`product_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
";

if ($conn->query($createCustomer)) {
  echo "Table customer created successfully!<br>";
} else {
  echo "Error creating customer table: " . $conn->error;
  exit();
}

if ($conn->query($createOrder)) {
  echo "Table order created successfully!<br>";
} else {
  echo "Error order customer table: " . $conn->error;
  exit();
}

if ($conn->query($createPayment)) {
  echo "Table payment created successfully!<br>";
} else {
  echo "Error payment customer table: " . $conn->error;
  exit();
}


if ($conn->query($createProductCategory)) {
  echo "Table product_category created successfully!<br>";
} else {
  echo "Error product_category customer table: " . $conn->error;
  exit();
}

if ($conn->query($createProduct)) {
  echo "Table product created successfully!<br>";
} else {
  echo "Error product customer table: " . $conn->error;
  exit();
}

if ($conn->query($createCartItem)) {
  echo "Table cart_item created successfully!<br>";
} else {
  echo "Error cart_item customer table: " . $conn->error;
  exit();
}

if ($conn->query($createWishlistItem)) {
  echo "Table wishlist_item created successfully!<br>";
} else {
  echo "Error wishlist_item customer table: " . $conn->error;
  exit();
}

if ($conn->query($createOrderItem)) {
  echo "Table order_item created successfully!<br>";
} else {
  echo "Error order_item customer table: " . $conn->error;
  exit();
}

$conn->close();
