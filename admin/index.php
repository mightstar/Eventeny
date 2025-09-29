<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventeny - Organizer Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-ticket-alt"></i> Eventeny Dashboard</h1>
            <p>Manage your event tickets with ease</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2><i class="fas fa-plus-circle"></i> Create New Ticket</h2>
                <p>Add a new ticket type for your event</p><br/>
                <button class="btn" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Create Ticket
                </button>
            </div>

            <div class="card">
                <h2><i class="fas fa-chart-bar"></i> Quick Stats</h2>
                <p>Total Tickets: <span id="totalTickets">0</span></p>
                <p>Public Tickets: <span id="publicTickets">0</span></p><br/>
                <button class="btn btn-secondary" onclick="loadTickets()">
                    <i class="fas fa-refresh"></i> Refresh
                </button>
            </div>
        </div>

        <div class="tickets-list">
            <div class="card">
                <h2><i class="fas fa-list"></i> Your Tickets</h2>
                <div id="ticketsContainer">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Loading tickets...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Ticket Modal -->
    <div id="ticketModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Create New Ticket</h2>
            <form id="ticketForm">
                <div class="form-group">
                    <label for="title">Ticket Title *</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sale_start_date">Sale Start Date *</label>
                        <input type="datetime-local" id="sale_start_date" name="sale_start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="sale_end_date">Sale End Date *</label>
                        <input type="datetime-local" id="sale_end_date" name="sale_end_date" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="visibility">Visibility *</label>
                        <select id="visibility" name="visibility" required>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ticket_image">Ticket Image</label>
                        <input type="file" id="ticket_image" name="ticket_image" accept="image/*">
                        <div id="image_preview" style="margin-top: 10px; display: none;">
                            <img id="preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                            <button type="button" id="remove_image" style="margin-left: 10px; background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Remove</button>
                        </div>
                        <div id="current_image" style="margin-top: 10px; display: none;">
                            <p style="color: #6b7280; font-size: 0.875rem;">Current image:</p>
                            <img id="current_img" src="" alt="Current" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn">Save Ticket</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/api.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>


