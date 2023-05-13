 CREATE TABLE clients (
   clientId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
   clientName VARCHAR(50) NOT NULL,
   clientLastName VARCHAR(50) NOT NULL,
   clientAddress VARCHAR(200) DEFAULT NULL,
   clientCellphone VARCHAR(16) NOT NULL UNIQUE,
   clientEmail VARCHAR(60) DEFAULT NULL,
   clientNIP VARCHAR(61) NOT NULL,
   clientDue FLOAT NOT NULL,
   clientCreditLimit FLOAT NOT NULL
);
CREATE TABLE suppliers (
   supplierId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
   supplierName VARCHAR(50) NOT NULL,
   supplierLastName VARCHAR(50) NOT NULL,
   supplierAddress VARCHAR(200) DEFAULT NULL,
   supplierCellphone VARCHAR(16) NOT NULL,
   supplierEmail VARCHAR(60) DEFAULT NULL,
   supplierBrand VARCHAR(50) NOT NULL,
   supplierDue FLOAT NOT NULL DEFAULT '0'
);
CREATE TABLE users (
   userId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
   userName VARCHAR(50) NOT NULL,
   userLastName VARCHAR(50) NOT NULL,
   userAddress VARCHAR(200) DEFAULT NULL,
   userCellphone VARCHAR(16) DEFAULT NULL,
   userEmail VARCHAR(60) NOT NULL UNIQUE,
   userPassword VARCHAR(100) NOT NULL
);
CREATE TABLE products (
   productId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
   productName VARCHAR(100) NOT NULL,
   productBrand VARCHAR(50) NOT NULL,
   productDescription TEXT DEFAULT NULL,
   productCategory VARCHAR(50) NOT NULL,
   productQuantity INTEGER NOT NULL DEFAULT '0',
   productPrice FLOAT NOT NULL DEFAULT '0',
   productPricePurchase FLOAT NOT NULL DEFAULT '0',
   productSupplier INTEGER NOT NULL REFERENCES suppliers(supplierId),
   productCodebar VARCHAR(40) NOT NULL UNIQUE,
   productImage VARCHAR(60) NOT NULL
);
CREATE TABLE supppayments (
   paymentId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
   paymentAmount FLOAT NOT NULL,
   paymentDate DATE NOT NULL,
   paymentSupplierId INTEGER NOT NULL,
   paymentUserId INTEGER NOT NULL,
   FOREIGN KEY (paymentSupplierId) REFERENCES suppliers (supplierId) ON DELETE CASCADE,
   FOREIGN KEY (paymentUserId) REFERENCES users (userId) ON DELETE CASCADE
);
CREATE TABLE supppaymentproducts (
   supppaymentId INTEGER NOT NULL,
   suppProductId INTEGER NOT NULL,
   purchasePrice FLOAT NOT NULL,
   quantity INTEGER NOT NULL,
   FOREIGN KEY (supppaymentId) REFERENCES supppayments (paymentId) ON DELETE CASCADE,
   FOREIGN KEY (suppProductId) REFERENCES products (productId) ON DELETE CASCADE
);
CREATE TABLE clientReceipt (
	cReceiptId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	cReceiptTotal FLOAT NOT NULL,
   cReceiptChange FLOAT NOT NULL,
   cReceiptAmount FLOAT NOT NULL,
   cReceiptCreditAmount FLOAT NOT NULL,
	cReceiptDate DATETIME NOT NULL,
	cReceiptClientId INTEGER NOT NULL,
   cReceiptUserId INTEGER NOT NULL,
   FOREIGN KEY (cReceiptClientId) REFERENCES clients(clientId) ON DELETE CASCADE,
   FOREIGN KEY (cReceiptUserId) REFERENCES users(userId) ON DELETE CASCADE
);
CREATE TABLE clientReceiptProducts (
   cReceiptId INTEGER NOT NULL,
   cReceiptProductId INTEGER NOT NULL,
   cReceiptPrice FLOAT NOT NULL,
   cReceiptQuantity INTEGER NOT NULL,
   FOREIGN KEY (cReceiptId) REFERENCES clientreceipt (cReceiptId) ON DELETE CASCADE,
   FOREIGN KEY (cReceiptProductId) REFERENCES products (productId) ON DELETE CASCADE
);

DELIMITER $$
CREATE TRIGGER set_default_image
BEFORE INSERT ON products
FOR EACH ROW
BEGIN
    IF NEW.productImage IS NULL OR NEW.productImage = '' THEN
        SET NEW.productImage = 'default.png';
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER set_default_due_and_credit_limit
BEFORE INSERT ON clients
FOR EACH ROW
BEGIN
    SET NEW.clientDue = 0;
    SET NEW.clientCreditLimit = 0;
END$$
DELIMITER ;

INSERT INTO users (userName, userLastName, userEmail, userPassword)
VALUES ('default', 'user', 'admin@gmail.com', '$2y$10$bvZK7EFC4SQ6903V9/hR5OfoPWAqvG6A7QCq4WCuz0ZPXja49xJhO');