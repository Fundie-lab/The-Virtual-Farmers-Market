VFM - Virtual Farmer's Market

Welcome to the Virtual Farmer's Market (VFM) project! This project provides a platform for farmers to list their produce and for customers to browse, interact, and purchase items directly from local farmers. The platform includes real-time product availability, dynamic pricing, and product recommendations.

Project Structure

VFM/
├── DATABASE FILE/
│   ├── [farm.sql]           SQL file containing the database structure and sample data
├── README.md                    Project documentation
└──...

Features

- Bilingual Support (English and SiSwati)
- Product Search with Instant Suggestions
- Real-time Product Availability
- Farmer Location Display on a Map
- Dynamic Pricing Based on Perishability
- Community Forum for Farmers
- Chat Functionality with Farmers
- Product Recommendations

Getting Started

Prerequisites

- A web server environment supporting PHP (such as XAMPP, WAMP, or MAMP)
- MySQL database server
- Basic knowledge of PHP and SQL for setup and maintenance

Setup Instructions

1. Clone or Download the Project
   - Download the project folder (`VFM`) and save it in your server's `htdocs` or `www` directory.

2. Database Setup
   - Open your preferred MySQL management tool (e.g., phpMyAdmin).
   - Import the database file located in `DATABASE FILE/farm.sql`.
   - This will create the necessary tables and insert sample data.

3. Configure Database Connection
   - Open the `connection/config.php` file
   - Update the database host, username, password, and database name according to your local setup.

   php
   $db_host = 'localhost';
   $db_user = 'root';        // Default username for localhost
   $db_pass = '';            // Password for your MySQL
   $db_name = 'farm'; // Name of the database as per `farm.sql`
   

4. Accessing the Platform
   - Launch your web server.
   - Open a browser and navigate to `http://localhost/VFM`.

Sample Accounts

To explore the platform, you can use the following sample accounts:

- Administrator Account
  - Username: `admin`
  - Password: `fffff`

- Customer Account
  - Username: `Tiloe`
  - Password: `ffffff`

- Farmer Account 
  - Email: `greenvalley@gmail.com`
  - Password: `fffff`

Important Files

- `DATABASE FILE/farm.sql`: Contains the database schema and initial data.
- `connection/`: Core PHP files handling business logic.
- `VFM/`: Frontend files (HTML, CSS, JavaScript).

Usage

After logging in, users have access to the following features depending on their account type:

- Customers can browse products, add items to a wishlist, view farmer details and locations, and communicate with farmers directly.
- Farmers can add and manage products, receive notifications for orders, and interact with customers via the chat.
- Admin can manage user accounts, monitor platform activity, and handle any user issues.

Troubleshooting

- If you encounter issues with loading pages, ensure your server and MySQL database are running.
- Verify that the database configuration matches your MySQL credentials.

License

This project is for educational purposes only.

Acknowledgments

Thank you for exploring this project. Enjoy your virtual farmer’s market experience!
