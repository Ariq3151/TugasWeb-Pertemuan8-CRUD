CREATE DATABASE IF NOT EXISTS inventaris_db;

USE inventaris_db;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL,
    category_id INT,
    supplier_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

INSERT INTO categories (name) VALUES
('Elektronik'),
('Alat Tulis'),
('Furniture'),
('Jaringan'),
('Aksesoris');

INSERT INTO suppliers (name, phone) VALUES
('PT Maju Jaya', '081234567890'),
('CV Sumber Rezeki', '081234567891'),
('PT Teknologi Nusantara', '081234567892'),
('CV Berkah Abadi', '081234567893'),
('PT Sentosa', '081234567894');

INSERT INTO products (name, price, stock, category_id, supplier_id) VALUES
('Laptop', 7500000, 10, 1, 3),
('Keyboard', 250000, 20, 5, 3),
('Meja Belajar', 850000, 8, 3, 4),
('Kabel LAN', 50000, 30, 4, 1),
('Buku Tulis', 15000, 50, 2, 2);