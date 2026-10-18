CREATE TABLE admins(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50) UNIQUE,password_hash VARCHAR(255));
CREATE TABLE menu_items(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),category VARCHAR(50),description VARCHAR(255),price DECIMAL(8,2),available TINYINT DEFAULT 1);
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,order_code VARCHAR(30) UNIQUE,customer_name VARCHAR(100),phone VARCHAR(15),total DECIMAL(8,2),
status ENUM('Pending Payment','Confirmed','Preparing','Ready','Completed','Cancelled') DEFAULT 'Pending Payment',
payment_status ENUM('Pending','Paid','Failed') DEFAULT 'Pending',razorpay_order_id VARCHAR(50),razorpay_payment_id VARCHAR(50),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE order_items(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT,menu_item_id INT,name VARCHAR(100),price DECIMAL(8,2),qty INT,FOREIGN KEY(order_id) REFERENCES orders(id));
INSERT INTO menu_items(name,category,description,price) VALUES('Veg Sandwich','Snacks','Grilled, with chutney',40),('Samosa','Snacks','2 pieces',20),('Masala Chai','Drinks','Hot tea',10);
