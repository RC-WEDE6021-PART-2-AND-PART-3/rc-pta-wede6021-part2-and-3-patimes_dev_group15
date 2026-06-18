# Pastimes - Curated Secondhand Luxury Clothing Marketplace

## Project Description
Pastimes is a fully functional e-commerce web application designed for buying and selling secondhand luxury clothing. Built using PHP, MySQL, HTML, and CSS, it supports three user roles: Admin, Seller, and Customer.

##GITHUB LINK:
https://github.com/RC-WEDE6021-PART-2-AND-PART-3/rc-pta-wede6021-part2-and-3-patimes_dev_group15.git

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


## Demo Video
A video demonstration of the working web application is included in this repository.

**⚠️ IMPORTANT:** Due to GitHub's 25MB file size limitation for web viewing, the video file is stored using Git LFS. 
**To view the video:**
1. Click on the file `PastimesGroup15_FinalPOE.MOV` in the repository list.
2. Click the **Download** button (or the three dots `...` > Download) on the right side of the screen.
3. Open the downloaded `.MOV` file on your computer to watch the full demonstration.
