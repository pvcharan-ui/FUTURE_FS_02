-- ecom.sql: create database and tables + sample products
CREATE DATABASE IF NOT EXISTS future_fs_02;
USE future_fs_02;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(100),
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255),
  stock INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150),
  email VARCHAR(150),
  address TEXT,
  total DECIMAL(10,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  qty INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- sample products (supermarket-ish)
INSERT INTO products (name, category, description, price, image, stock) VALUES
('Red Apple (1 kg)', 'Fruits', 'Fresh red apples, 1kg bag', 120.00, 'apple.jpg', 50),
('Banana (1 dozen)', 'Fruits', 'Fresh bananas, 12 pcs', 60.00, 'banana.jpg', 100),
('Whole Wheat Bread', 'Bakery', 'Freshly baked whole wheat bread', 35.00, 'bread.jpg', 40),
('Milk (1 L)', 'Dairy', 'Pack of full cream milk 1L', 50.00, 'milk.jpg', 80),
('Eggs (12 pcs)', 'Dairy', 'Farm fresh eggs, 12 pcs', 90.00, 'eggs.jpg', 60);
