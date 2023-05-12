 CREATE TABLE clients (
   clientId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
   clientName VARCHAR(50) NOT NULL,
   clientLastName VARCHAR(50) NOT NULL,
   clientAddress VARCHAR(200) DEFAULT NULL,
   clientCellphone VARCHAR(16) NOT NULL UNIQUE,
   clientEmail VARCHAR(60) DEFAULT NULL,
   clientNIP VARCHAR(61) NOT NULL,
   clientDue FLOAT NOT NULL DEFAULT '0',
   clientCreditLimit FLOAT NOT NULL DEFAULT '0'
);
CREATE TABLE suppliers (
   supplierId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
   supplierName VARCHAR(50) NOT NULL,
   supplierLastName VARCHAR(50) NOT NULL,
   supplierAddress VARCHAR(200) DEFAULT NULL,
   supplierCellphone VARCHAR(16) NOT NULL,
   supplierEmail VARCHAR(60) DEFAULT NULL,
   supplierBrand VARCHAR(50) NOT NULL,
   supplierDue FLOAT NOT NULL DEFAULT '0'
);
CREATE TABLE users (
   userId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
   userName VARCHAR(50) NOT NULL,
   userLastName VARCHAR(50) NOT NULL,
   userAddress VARCHAR(200) DEFAULT NULL,
   userCellphone VARCHAR(16) DEFAULT NULL,
   userEmail VARCHAR(60) NOT NULL UNIQUE,
   userPassword VARCHAR(100) NOT NULL
);
CREATE TABLE products (
   productId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
   productName VARCHAR(100) NOT NULL,
   productBrand VARCHAR(50) NOT NULL,
   productDescription TEXT DEFAULT NULL,
   productCategory VARCHAR(50) NOT NULL,
   productQuantity INTEGER NOT NULL DEFAULT '0',
   productPrice FLOAT NOT NULL DEFAULT '0',
   productPricePurchase FLOAT NOT NULL DEFAULT '0',
   productSupplier INTEGER NOT NULL REFERENCES suppliers(supplierId),
   productCodebar VARCHAR(40) NOT NULL UNIQUE,
   productImage VARCHAR(60) DEFAULT 'default_png',
);
CREATE TABLE supppayments (
   paymentId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
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
