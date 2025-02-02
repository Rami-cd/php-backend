
CREATE TABLE Users (
    user_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT
);

CREATE TABLE Orders (
    order_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    total_price DECIMAL(10, 2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'Pending', -- e.g., Pending, Processing, Shipped, Delivered
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Shops (
    shop_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    latitude REAL,
    longitude REAL,
    contact_number VARCHAR(20),
    email VARCHAR(255)
);

CREATE TABLE Categories (
    category_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Products (
    product_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    category_id INTEGER,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);

CREATE TABLE shop_product (
    shop_product_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    shop_id INTEGER,
    product_id INTEGER,
    price DECIMAL(10, 2),
    quantity_in_stock INTEGER,
    FOREIGN KEY (shop_id) REFERENCES Shops(shop_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

CREATE TABLE Order_items (
    order_item_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER,
    product_id INTEGER,
    quantity INTEGER,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Indices for performance
CREATE INDEX idx_product_category ON Products (category_id);
CREATE INDEX idx_order_user ON Orders (user_id);
CREATE INDEX idx_order_items_order ON Order_items (order_id);
CREATE INDEX idx_order_items_product ON Order_items (product_id);
CREATE INDEX idx_shopproduct_shop ON shop_product (shop_id);
CREATE INDEX idx_shopproduct_product ON shop_product (product_id);
CREATE INDEX idx_shops_location ON Shops (latitude, longitude);