# Pastimes - Curated Secondhand Luxury Clothing Marketplace

## Project Description
Pastimes is a fully functional e-commerce web application designed for buying and selling secondhand luxury clothing. Built using PHP, MySQL, HTML, and CSS, it supports three user roles: Admin, Seller, and Customer.

## Features
- User Registration with Password Hashing (MD5) and Role Selection (Customer/Seller).
- Secure Login and Role-based Redirects.
- Admin Dashboard for User Management, Clothing Item Approval, and Order Management.
- Seller Dashboard for uploading clothing items with images and descriptions.
- Shopping Cart with dynamic Quantity Adjustment and Subtotal Calculation.
- Secure Checkout with delivery address capture.
- Internal Messaging System allowing direct communication between Buyers, Sellers, and Admins.
- Real-time Notification Bell for unread messages.

## Installation / Setup Guide

### 1. Database Setup
1. Open phpMyAdmin (usually via XAMPP or MAMP).
2. Create a new database named: `ClothingStore`.
3. Import the `myClothingStore.sql` file located in the root folder.
   - OR run `loadClothingStore.php` in your browser to create tables and insert dummy data.

### 2. Configure the Connection
- Open `DBConn.php`.
- Ensure the database name matches your local setup:
  `$conn = new mysqli("localhost", "root", "", "ClothingStore");`

### 3. Folder Structure & Images
- Ensure an `images/` folder exists in the root directory.
- The `images/` folder must have write permissions enabled to allow Seller/Admin image uploads.

## Demo User Accounts
Use these accounts to test the website:

| Role     | Username | Password      |
|----------|----------|---------------|
| Admin    | admin    | admin         |
| Seller   | katlego  | Kk.atleg06MM  |
| Customer | kgomotso | RosinaThe1.   |

## Technologies Used
- PHP (Procedural & OOP concepts)
- MySQL Database (phpMyAdmin)
- HTML5 / CSS3 (Custom `styling.css`)
- JavaScript (Basic DOM manipulation for cart)
- FontAwesome (Icons)


## Demo video
The demo video is included in this repository.

NOTE: Due to GitHub size limitation, please download the video file to view it.

[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/OFWe9D1G)
