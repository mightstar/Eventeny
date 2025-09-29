class EventenyAPI {
    constructor() {
        this.baseUrl = this.getBaseUrl();
    }

    getBaseUrl() {
        const protocol = window.location.protocol;
        const host = window.location.host;
        return `${protocol}//${host}`;
    }

    request(endpoint, method = 'GET', data = null, options = {}) {
        const url = `${this.baseUrl}/${endpoint}`;
        const config = {
            url: url,
            type: method,
            ...options
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            config.contentType = 'application/json';
            config.data = JSON.stringify(data);
        } else if (data && (method === 'GET' || method === 'DELETE')) {
            // For GET requests, append data as query parameters
            const params = new URLSearchParams(data);
            config.url += `?${params.toString()}`;
        }

        return $.ajax(config);
    }

    // ==================== TICKET API METHODS ====================

    getTickets() {
        return this.request('api/tickets.php');
    }

    getTicket(id) {
        return this.request('api/tickets.php', 'GET', { id: id });
    }

    createTicket(ticketData) {
        return this.request('api/tickets.php', 'POST', ticketData);
    }

    updateTicket(id, ticketData) {
        return this.request('api/tickets.php', 'PUT', ticketData, {
            url: `${this.baseUrl}/api/tickets.php?id=${id}`
        });
    }

    deleteTicket(id) {
        return this.request('api/tickets.php', 'DELETE', { id: id });
    }

    // ==================== CART API METHODS ====================

    getCart() {
        return this.request('api/cart.php');
    }

    addToCart(itemData) {
        return this.request('api/cart.php', 'POST', itemData);
    }

    updateCartItem(itemData) {
        return this.request('api/cart.php', 'PUT', itemData);
    }

    removeFromCart(itemData) {
        return this.request('api/cart.php', 'DELETE', itemData);
    }

    // ==================== CHECKOUT API METHODS ====================

    checkout() {
        return this.request('api/checkout.php', 'POST');
    }
}

// Create global instance
window.API = new EventenyAPI();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = EventenyAPI;
}
