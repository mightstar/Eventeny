let cartItems = [];
let isCheckoutMode = false;

$(document).ready(function() {
    loadTickets();
    loadCart();
});

function loadTickets() {
    $('#ticketsContainer').html(`
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading tickets...</p>
        </div>
    `);

    API.getTickets()
        .done(function(response) {
            if (response.success) {
                displayTickets(response.data);
            } else {
                showAlert('Error loading tickets: ' + response.error, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load tickets', 'error');
        });
}

function displayTickets(tickets) {
    if (tickets.length === 0) {
        $('#ticketsContainer').html(`
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h2>No tickets available</h2>
                <p>Check back later for new events!</p>
            </div>
        `);
        return;
    }

    let html = '<div class="tickets-grid">';
    tickets.forEach(ticket => {
        const isAvailable = new Date() >= new Date(ticket.sale_start_date) && 
                          new Date() <= new Date(ticket.sale_end_date) &&
                          ticket.visibility === 'public';
        
        html += `
            <div class="ticket-card">
                <img src="${ticket.image_url || 'assets/images/empty_image.jpg'}" 
                     alt="${ticket.title}" 
                     class="ticket-image">
                <div class="ticket-content">
                    <h3 class="ticket-title">${ticket.title} <span class="badge ${ticket.visibility === 'public' ? 'public' : 'private'}">${ticket.visibility}</span></h3>
                    ${ticket.description ? `<p class="ticket-description">${ticket.description}</p>` : ''}
                    <div class="ticket-price">$${parseFloat(ticket.price).toFixed(2)}</div>
                    
                    <div class="ticket-meta">
                        <div class="meta-item">
                            <i class="fas fa-box"></i>
                            <span>Available: ${ticket.quantity}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Ends: ${new Date(ticket.sale_end_date).toLocaleDateString()}</span>
                        </div>
                    </div>
                    
                    <div class="quantity-selector">
                        <label>Qty:</label>
                        <div class="quantity-input">
                            <button class="quantity-btn" onclick="updateQuantity(${ticket.id}, -1)">-</button>
                            <input type="number" id="qty_${ticket.id}" value="1" min="1" max="${ticket.quantity}">
                            <button class="quantity-btn" onclick="updateQuantity(${ticket.id}, 1)">+</button>
                        </div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> ${ticket.quantity} tickets available
                        </div>
                    </div>
                    
                    <button class="add-to-cart-btn" 
                            onclick="addToCart(${ticket.id})" 
                            ${!isAvailable ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus"></i>
                        ${isAvailable ? 'Add to Cart' : 'Not Available'}
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    $('#ticketsContainer').html(html);
}

function updateQuantity(ticketId, change) {
    const input = $(`#qty_${ticketId}`);
    const currentValue = parseInt(input.val());
    const newValue = Math.max(1, currentValue + change);
    input.val(newValue);
}

function addToCart(ticketId) {
    const quantity = parseInt($(`#qty_${ticketId}`).val());
    
    API.addToCart({
        ticket_id: ticketId,
        quantity: quantity
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Added to cart!', 'success');
            loadCart();
        } else {
            showAlert('Error: ' + response.error, 'error');
        }
    })
    .fail(function() {
        showAlert('Failed to add to cart', 'error');
    });
}

function loadCart() {
    API.getCart()
        .done(function(response) {
            if (response.success) {
                cartItems = response.data.items;
                updateCartCount(response.data.itemCount);
                if (isCheckoutMode) {
                    displayCheckout();
                } else {
                    displayCart();
                }
            }
        })
        .fail(function() {
            console.error('Failed to load cart');
        });
}

function displayCart() {
    if (cartItems.length === 0) {
        $('#cartContent').html(`
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Your cart is empty</p>
            </div>
        `);
        $('#checkoutSection').hide();
        return;
    }

    let html = '';
    let total = 0;
    
    cartItems.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="cart-item">
                <img src="${item.image_url || 'assets/images/empty_image.jpg'}" 
                     alt="${item.title}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-title">${item.title}</div>
                    <div class="cart-item-price">$${parseFloat(item.price).toFixed(2)} × ${item.quantity}</div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                        <i class="fas fa-box"></i> Stock: ${item.quantity} available
                    </div>
                </div>
                <div class="cart-item-actions">
                    <button class="remove-btn" onclick="removeFromCart(${item.ticket_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="cart-total">
            <div class="total-row">
                <span>Total:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        </div>
        <button class="checkout-btn" onclick="proceedToCheckout()">
            <i class="fas fa-arrow-right"></i> Proceed to Review
        </button>
    `;
    
    $('#cartContent').html(html);
    $('#checkoutSection').hide();
}

function proceedToCheckout() {
    isCheckoutMode = true;
    displayCheckout();
}

function displayCheckout() {
    let html = '';
    let total = 0;
    
    cartItems.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="cart-item">
                <img src="${item.image_url || 'assets/images/empty_image.jpg'}" 
                     alt="${item.title}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-title">${item.title}</div>
                    <div class="cart-item-price">$${parseFloat(item.price).toFixed(2)} × ${item.quantity} = $${itemTotal.toFixed(2)}</div>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="cart-total">
            <div class="total-row">
                <span>Order Total:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        </div>
    `;
    
    $('#cartContent').html(html);
    $('#checkoutSection').show();
}

function removeFromCart(ticketId) {
    API.removeFromCart({
        ticket_id: ticketId
    })
    .done(function(response) {
        if (response.success) {
            showAlert('Item removed from cart', 'success');
            loadCart();
        } else {
            showAlert('Error: ' + response.error, 'error');
        }
    })
    .fail(function() {
        showAlert('Failed to remove item', 'error');
    });
}

function completeCheckout() {
    API.checkout()
        .done(function(response) {
            if (response.success) {
                showAlert('Order completed successfully! Order ID: ' + response.orderId, 'success');
                closeCart();
                loadCart();
                loadTickets(); // Refresh ticket list to show updated quantities
            } else {
                let errorMessage = 'Error: ' + response.error;
                if (response.details && response.details.length > 0) {
                    errorMessage += '\n\nDetails:\n' + response.details.join('\n');
                }
                showAlert(errorMessage, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to complete checkout', 'error');
        });
}

function openCart() {
    isCheckoutMode = false;
    loadCart();
    $('#cartModal').show();
}

function closeCart() {
    $('#cartModal').hide();
    isCheckoutMode = false;
}

function updateCartCount(count) {
    $('#cartCount').text(count);
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const alert = $(`
        <div class="alert ${alertClass}">
            <span>${message}</span>
            <button class="alert-close" onclick="closeAlert(this)">&times;</button>
        </div>
    `);
    
    $('body').append(alert);
    
    setTimeout(() => {
        alert.addClass('show');
    }, 10);
    
    setTimeout(() => {
        if (alert.hasClass('show')) {
            closeAlert(alert.find('.alert-close')[0]);
        }
    }, 4000);
}

function closeAlert(closeBtn) {
    const alert = $(closeBtn).parent();
    alert.removeClass('show');
    setTimeout(() => {
        alert.remove();
    }, 300);
}

// Close modal when clicking outside
$(document).click(function(e) {
    if (e.target.id === 'cartModal') {
        closeCart();
    }
});