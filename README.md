# Thrift with Ramo

A responsive PHP + MySQL storefront and admin dashboard for selling refurbished heels and boots with WhatsApp ordering.

## Features
- Responsive customer site with home, shop, product details, about, and contact pages
- Simple public shopping experience without customer accounts
- WhatsApp order links with product details pre-filled
- Private admin login with secure password hashing
- Add, edit, delete, and mark products as available or sold out
- Product image upload with validation for JPG, JPEG, PNG, and WebP
- MySQL-ready schema for products and admin users

## Default admin login
- Username: admin
- Password: thriftwithramo123

## Local setup for XAMPP
1. Save this project in `C:\xampp\htdocs\thrift`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and create a database named `thrift_with_ramo` or import the SQL file from `database/schema.sql`.
4. Visit `http://localhost/thrift` in your browser.
5. Go to `http://localhost/thrift/admin/login.php` to manage products.

## Managing products
- Add new shoes from the admin dashboard.
- Click Edit to change the price, size, description, or availability.
- Use the Sold Out / Available action to update inventory quickly from a phone.
- Upload a product image from the dashboard with clear validation.

## WhatsApp ordering
Each available shoe includes a button that opens WhatsApp with a pre-filled order message and the product name, size, and price.

## Security notes
- Passwords are stored using `password_hash`.
- All user input is sanitized before storing or displaying.
- Product and admin actions use prepared SQL statements.
- Image uploads are validated for extension and size.

## Files structure
- `admin/` – private management pages
- `assets/` – CSS, JavaScript, and default image
- `database/` – database schema
- `includes/` – shared config and helper functions
- `uploads/` – uploaded product images
