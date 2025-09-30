# Eventeny - Dynamic Ticketing Platform

A modern, responsive ticketing platform built with PHP, MySQL, HTML/CSS/JavaScript, and jQuery. This platform allows event organizers to manage tickets with full CRUD capabilities and provides ticket buyers with a smooth browsing and purchasing experience.

Live: https://eventeny.onrender.com/


https://github.com/user-attachments/assets/132df44f-d8bb-4285-a9f9-ffe77800c708



## Features

### For Event Organizers
- ✅ **Create Tickets**: Submit ticket details including title, dates, quantity, price, visibility, and optional images
- ✅ **View Tickets**: All tickets are stored in the database and displayed in a beautiful dashboard
- ✅ **Edit Tickets**: Modify existing ticket details with immediate updates
- ✅ **Delete Tickets**: Remove tickets from the system
- ✅ **Real-time Statistics**: View total tickets and public ticket counts

### For Ticket Buyers
- ✅ **Browse Tickets**: View all available tickets in a modern grid layout
- ✅ **Add to Cart**: Select quantities and add tickets to cart without page reloads
- ✅ **Cart Modal**: Access cart via modal with dynamic updates
- ✅ **Edit Cart**: Remove items from cart with immediate updates
- ✅ **Checkout Preview**: Review selections before completing purchase
- ✅ **Complete Purchase**: Finish the transaction

## Tech Stack

- **Backend**: PHP 8.1+ with PDO for database operations
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), jQuery 3.6
- **Styling**: Custom CSS with modern design principles
- **Icons**: Font Awesome 6.0
- **Fonts**: Google Fonts (Inter)

## Installation & Setup

### Docker Setup (Recommended)

#### Prerequisites
- Docker
- Docker Compose

#### Quick Start
1. **Clone the repository**:
   ```bash
   git clone https://github.com/mightstar/Eventeny.git
   cd Eventeny
   ```

2. **Run the setup script**:
   ```bash
   ./docker-setup.sh
   ```

3. **Access the application**:
   - Application: 
      > User: http://localhost:8080

      > Admin: http://localhost:8080/admin

      > Eventeny Setup Page: http://localhost:8080/setup.php
   - phpMyAdmin: http://localhost:8081

4. **Default credentials and env** (configured via `docker-compose.yml`):
   - `DB_HOST=mysql`
   - `DB_NAME=eventeny_tickets`
   - `DB_USER=eventeny_user`
   - `DB_PASS=eventeny_password`

#### Manual Setup
```bash
# Build and start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Rebuild containers
docker-compose up --build
```

### Manual Installation (Alternative)

#### Prerequisites
- PHP 8.1 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)

### Database Setup

1. **Create Database**:
   ```sql
   CREATE DATABASE eventeny_tickets;
   ```

2. **Import Schema**:
   ```bash
   mysql -u your_username -p eventeny_tickets < database/schema.sql
   ```

3. **Configure application to connect to MySQL**
   - Preferred: set web server environment variables so PHP can read them via `$_ENV` (matches Docker behavior):
     - Apache: add to your vhost config
       ```
       SetEnv DB_HOST localhost
       SetEnv DB_NAME eventeny_tickets
       SetEnv DB_USER your_username
       SetEnv DB_PASS your_password
       ```
     - Nginx + PHP-FPM: set `env[DB_HOST]`, `env[DB_NAME]`, etc. in your `php-fpm.conf` pool or use a process manager/OS env.
   - Simpler (non-production): edit `config/database.php` directly and hard-code credentials if you are not using env vars.

4. **Create uploads directory permissions**
   Ensure the `uploads/` directory exists and is writable by the web server user:
   ```bash
   mkdir -p uploads
   chmod 755 uploads
   ```

5. **Enable URL rewriting (Apache)**
   ```bash
   a2enmod rewrite && service apache2 reload
   ```

6. **Visit the app**
   - Application: http://localhost (or your vhost)
   - Admin dashboard: http://localhost/admin/
   - Eventeny Setup Page: http://localhost/setup.php

### Environment Variables

The app reads database settings from environment variables (with local fallbacks in `config/database.php`):

- `DB_HOST` (default: `localhost`)
- `DB_NAME` (default: `eventeny_tickets`)
- `DB_USER` (default: `root`)
- `DB_PASS` (default: empty)

When using Docker, these are set for you in `docker-compose.yml`.

### Eventeny Setup Page (one-click installer)

If you're running on a local LAMP/WAMP/MAMP stack without Docker, you can use the built-in setup page to initialize the database and configuration.

1. Open `http://localhost/setup.php` (adjust host as needed)
2. Enter your MySQL credentials (host, database name, username, password)
3. Click "Setup Database"

What it does:
- Verifies PHP version and PDO MySQL extension
- Connects to MySQL and creates the database if missing
- Imports `database/schema.sql` (including sample data)
- Writes credentials into `config/database.php`
- Smoke-tests the Tickets API and links to the app pages

Notes:
- If you're using environment variables (recommended for Docker/production), the setup page's config write is not required.
- After successful setup, delete or restrict access to `setup.php`.
- In Docker, the database is auto-initialized from `database/schema.sql`; you generally don't need `setup.php`.

## Usage

### For Organizers
1. Navigate to `/admin/index.php`
2. Create new tickets with all required details
3. View, edit, or delete existing tickets
4. Monitor ticket statistics

### For Customers
1. Navigate to `/index.php`
2. Browse available tickets
3. Add tickets to cart with desired quantities
4. Review cart and proceed to checkout
5. Complete purchase (mock checkout)

## API Endpoints

### Tickets API (`/api/tickets.php`)
- `GET` - Retrieve all tickets or specific ticket by ID
- `POST` - Create new ticket
- `PUT` - Update existing ticket
- `DELETE` - Delete ticket

### Cart API (`/api/cart.php`)
- `GET` - Get cart items and totals
- `POST` - Add item to cart
- `PUT` - Update item quantity
- `DELETE` - Remove item from cart

### Checkout API (`/api/checkout.php`)
- `POST` - Process order and clear cart

## Design Features

### Modern UI/UX
- **Gradient Backgrounds**: Beautiful purple-blue gradients
- **Glass Morphism**: Backdrop blur effects for modern look
- **Smooth Animations**: Hover effects and transitions
- **Responsive Design**: Mobile-first approach
- **Interactive Elements**: Dynamic cart updates without page reloads

### User Experience
- **No Page Reloads**: All interactions use AJAX
- **Real-time Updates**: Cart and ticket counts update instantly
- **Modal Interfaces**: Smooth cart and form interactions
- **Visual Feedback**: Success/error alerts and loading states
- **Intuitive Navigation**: Clear call-to-action buttons

## Database Schema

### Tables
- **tickets**: Store ticket information
- **cart_items**: Session-based cart storage
- **orders**: Completed purchase records
- **order_items**: Individual items in orders

### Key Features
- **Session-based Cart**: No user registration required
- **Flexible Ticket System**: Support for public/private tickets
- **Date Validation**: Sale period enforcement
- **Quantity Management**: Stock tracking

## Security Considerations

- **PDO Prepared Statements**: SQL injection prevention
- **Input Validation**: Server-side validation for all inputs
- **Session Management**: Secure cart handling

## Future Enhancements

- User authentication system
- Payment gateway integration
- Email notifications
- Advanced reporting
- Multi-language support
- Mobile app integration


