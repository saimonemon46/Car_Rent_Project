# Car Rental Management System

A comprehensive web-based car rental management system that allows users to browse vehicles, make bookings, and manage their reservations. The system includes an admin panel for managing vehicles, bookings, users, and other administrative tasks.

## 🎬 Video Demo

Watch a complete walkthrough of the NexaCore website:

[![Premium Car Rental Website Demo](https://img.youtube.com/vi/e200PCJ9cJM/maxresdefault.jpg)](https://youtu.be/e200PCJ9cJM)

[**Watch Full Demo on YouTube** →](https://youtu.be/e200PCJ9cJM)

---

## Features

### User Features

- **Vehicle Browsing**: Browse and search available vehicles with detailed information
- **Vehicle Filtering**: Search vehicles by various criteria
- **Booking System**: Easy-to-use booking interface
- **Payment Integration**: Secure payment processing
- **Booking Management**: View, manage, and track your bookings
- **User Profile**: Manage personal information and change password
- **Contact Us**: Send inquiries and feedback
- **Responsive Design**: Mobile-friendly interface

### Admin Features

- **Dashboard**: Overview of key metrics and statistics
- **Vehicle Management**: Add, edit, and delete vehicle listings
- **Brand Management**: Create and manage vehicle brands
- **Booking Management**: View all bookings (new, confirmed, canceled)
- **User Management**: Manage registered users
- **Payment History**: Track payments and confirmations
- **Contact Queries**: Manage customer inquiries
- **Subscriber Management**: Handle newsletter subscribers
- **Image Management**: Upload and manage vehicle images

## Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap, jQuery
- **Backend**: PHP
- **Database**: MySQL
- **Additional Libraries**:
  - DataTables for data management
  - Chart.js for analytics
  - Font Awesome for icons
  - File Input for file uploads

## Installation & Setup

### Prerequisites

- XAMPP (or any Apache + MySQL + PHP server)
- PHP 5.6 or higher
- MySQL 5.0 or higher
- Modern web browser

### Steps to Install

1. **Download the Project**

   ```
   Copy the project folder to: C:\xampp\htdocs\Car_Proj
   ```

2. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database (if not exists)
   - Import `backend.sql` file into your database

3. **Update Database Configuration**
   - Edit `includes/config.php` and `admin/includes/config.php`
   - Update database credentials (hostname, username, password, database name)

4. **Start the Application**
   - Open your browser and navigate to: `http://localhost/Car_Proj/`

## Project Structure

```
Car_Proj/
├── index.php                    # Home page
├── booking.php                  # Booking page
├── my_booking.php              # View user bookings
├── vehicles_details.php         # Vehicle details page
├── car_listing.php             # Car listing page
├── search.php                  # Search vehicles
├── payment.php                 # Payment page
├── payment_success.php         # Payment confirmation
├── profile.php                 # User profile
├── change_password.php         # Change password
├── contact_us.php              # Contact form
├── backend.sql                 # Database file
│
├── admin/                      # Admin panel
│   ├── index.php              # Admin login
│   ├── dashboard.php          # Admin dashboard
│   ├── manage-vehicles.php    # Manage vehicles
│   ├── manage-brands.php      # Manage brands
│   ├── manage-bookings.php    # Manage bookings
│   ├── reg-users.php          # View users
│   ├── manage-subscribers.php # Newsletter subscribers
│   ├── manage-conactusquery.php # Contact inquiries
│   └── includes/              # Admin config and headers
│
├── includes/                  # Shared includes
│   ├── config.php            # Database configuration
│   ├── header.php            # Header template
│   ├── footer.php            # Footer template
│   ├── login.php             # Login functionality
│   ├── register.php          # Registration form
│   ├── sidebar.php           # Sidebar navigation
│   └── logout.php            # Logout functionality
│
├── assets/                    # Static assets
│   ├── css/                  # Stylesheets
│   ├── images/               # Images and favicon
│   └── js/                   # JavaScript files
│
└── admin/                     # Admin resources
    ├── css/                  # Admin stylesheets
    ├── fonts/                # Font files
    ├── img/                  # Admin images
    └── js/                   # Admin JavaScript
```

## Database Setup

Import the `backend.sql` file to create the necessary tables automatically:

- Users table
- Vehicles table
- Brands table
- Bookings table
- Payments table
- Contact queries table
- Subscribers table

## Usage

### For Users

1. Register a new account on the registration page
2. Log in with your credentials
3. Browse available vehicles using the search feature
4. Click on a vehicle to view details
5. Make a booking by selecting dates and confirming
6. Proceed to payment
7. View your bookings in "My Bookings" section
8. Manage your profile and change password as needed

### For Admin

1. Log in to the admin panel at `http://localhost/Car_Proj/admin/`
2. Use admin credentials to access the dashboard
3. Manage vehicles, brands, bookings, and users from the respective sections
4. View statistics and reports on the dashboard
5. Respond to customer inquiries in the Contact Queries section

## Default Admin Credentials

Please check the database (`backend.sql`) for default admin credentials or reset them through the admin panel.

## Features Highlights

- ✅ Complete booking management system
- ✅ Secure payment processing
- ✅ User authentication and authorization
- ✅ Responsive admin dashboard
- ✅ Real-time booking status updates
- ✅ Vehicle image management
- ✅ Newsletter subscription system
- ✅ Contact form for customer inquiries
- ✅ User profile management
- ✅ Comprehensive admin controls

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## Support & Contact

For issues, questions, or suggestions, please use the contact form in the application or reach out through the admin panel contact queries section.

## License

This project is created for educational and commercial use.

---

**Last Updated**: March 2026
