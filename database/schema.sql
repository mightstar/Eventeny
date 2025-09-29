CREATE DATABASE IF NOT EXISTS eventeny_tickets;
USE eventeny_tickets;

-- Tickets table
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    sale_start_date DATETIME NOT NULL,
    sale_end_date DATETIME NOT NULL,
    visibility ENUM('public', 'private') DEFAULT 'public',
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cart items table (for session-based cart)
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    ticket_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);

-- Orders table (for completed purchases)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    ticket_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);

-- Insert sample tickets
INSERT INTO tickets (title, description, price, quantity, sale_start_date, sale_end_date, visibility, image_url) VALUES
('Concert VIP Pass', 'Exclusive VIP access to the main concert with backstage meet & greet', 299.99, 50, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'public', 'uploads/eventeny-ticket.png'),
('General Admission', 'Standard admission to the concert', 89.99, 200, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'public', 'uploads/eventeny-ticket.png'),
('Student Discount', 'Special pricing for students with valid ID', 49.99, 100, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'public', 'uploads/eventeny-ticket.png'),
('Private Event Access', 'Exclusive private event for members only', 199.99, 25, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'private', 'uploads/eventeny-ticket.png');


