<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventeny - Event Tickets</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>
<body>
    <div class="header">
        <div class="nav">
            <a href="#" class="logo">
                <i class="fas fa-ticket-alt"></i> Eventeny
            </a>
            <button class="cart-btn" onclick="openCart()">
                <i class="fas fa-shopping-cart"></i>
                Cart (<span id="cartCount">0</span>)
            </button>
        </div>
    </div>

    <div class="container">
        <div class="hero">
            <h1>Discover Amazing Events</h1>
            <p>Find and purchase tickets for the best events in your area</p>
        </div>

        <div id="ticketsContainer">
            <div class="loading">
                <div class="spinner"></div>
                <p>Loading tickets...</p>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Shopping Cart</h2>
                <button class="close-btn" onclick="closeCart()">&times;</button>
            </div>
            
            <div id="cartContent">
                <!-- Cart items will be loaded here -->
            </div>
            
            <div id="checkoutSection" style="display: none;">
                <h3>Review Your Order</h3>
                <div id="orderSummary">
                    <!-- Order summary will be loaded here -->
                </div>
                <button class="checkout-btn" onclick="completeCheckout()">
                    <i class="fas fa-credit-card"></i> Complete Purchase
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/api.js"></script>
    <script src="js/app.js"></script>
</body>
</html>


