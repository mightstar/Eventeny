let currentTicketId = null;
let currentImageUrl = null;

$(document).ready(function() {
    loadTickets();
    setupImageHandlers();
});

function setupImageHandlers() {
    // Handle file selection
    $('#ticket_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file', 'error');
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('File size must be less than 5MB', 'error');
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_img').attr('src', e.target.result);
                $('#image_preview').show();
                $('#current_image').hide();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Handle remove image
    $('#remove_image').on('click', function() {
        $('#ticket_image').val('');
        $('#image_preview').hide();
        $('#current_image').show();
    });
}

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
                updateStats(response.data);
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
        $('#ticketsContainer').html('<p style="text-align: center; color: #6b7280; padding: 2rem;">No tickets found. Create your first ticket!</p>');
        return;
    }

    let html = '';
    tickets.forEach(ticket => {
        const startDate = new Date(ticket.sale_start_date).toLocaleDateString();
        const endDate = new Date(ticket.sale_end_date).toLocaleDateString();
        const isActive = new Date() >= new Date(ticket.sale_start_date) && new Date() <= new Date(ticket.sale_end_date);
        
        html += `
            <div class="ticket-item">
                <div style="margin-bottom: 1rem;">
                    <img src="${ticket.image_url ? '../' + ticket.image_url : '../assets/images/empty_image.jpg'}" 
                         alt="${ticket.title}" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; border: 1px solid #e5e7eb;">
                </div>
                
                <div class="ticket-header">
                    <div>
                        <div class="ticket-title">${ticket.title}</div>
                        <div class="ticket-price">$${parseFloat(ticket.price).toFixed(2)}</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge ${ticket.visibility === 'public' ? 'public' : 'private'}">
                            ${ticket.visibility}
                        </span>
                    </div>
                </div>
                
                <div class="ticket-meta">
                    <div class="meta-item">
                        <i class="fas fa-box"></i>
                        <span>Qty: ${ticket.quantity}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Start: ${startDate}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>End: ${endDate}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-circle ${isActive ? 'text-green-500' : 'text-red-500'}"></i>
                        <span>${isActive ? 'Active' : 'Inactive'}</span>
                    </div>
                </div>
                
                ${ticket.description ? `<p style="color: #6b7280; margin-bottom: 1rem;">${ticket.description}</p>` : ''}
                
                <div class="ticket-actions">
                    <button class="btn" onclick="editTicket(${ticket.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" onclick="deleteTicket(${ticket.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    });
    
    $('#ticketsContainer').html(html);
}

function updateStats(tickets) {
    $('#totalTickets').text(tickets.length);
    $('#publicTickets').text(tickets.filter(t => t.visibility === 'public').length);
}

function openCreateModal() {
    currentTicketId = null;
    $('#modalTitle').text('Create New Ticket');
    $('#ticketForm')[0].reset();
    $('#ticketModal').show();
}

function editTicket(id) {
    currentTicketId = id;
    $('#modalTitle').text('Edit Ticket');
    
    API.getTicket(id)
        .done(function(response) {
            if (response.success) {
                const ticket = response.data;
                $('#title').val(ticket.title);
                $('#description').val(ticket.description);
                $('#price').val(ticket.price);
                $('#quantity').val(ticket.quantity);
                $('#sale_start_date').val(ticket.sale_start_date.replace(' ', 'T').substring(0, 16));
                $('#sale_end_date').val(ticket.sale_end_date.replace(' ', 'T').substring(0, 16));
                $('#visibility').val(ticket.visibility);
                
                // Handle image display
                currentImageUrl = ticket.image_url;
                if (ticket.image_url) {
                    $('#current_img').attr('src', '../' + ticket.image_url);
                    $('#current_image').show();
                    $('#image_preview').hide();
                } else {
                    $('#current_image').hide();
                    $('#image_preview').hide();
                }
                
                $('#ticketModal').show();
            } else {
                showAlert('Error loading ticket: ' + response.error, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load ticket', 'error');
        });
}

function deleteTicket(id) {
    if (confirm('Are you sure you want to delete this ticket?')) {
        API.deleteTicket(id)
            .done(function(response) {
                if (response.success) {
                    showAlert('Ticket deleted successfully', 'success');
                    loadTickets();
                } else {
                    showAlert('Error deleting ticket: ' + response.error, 'error');
                }
            })
            .fail(function() {
                showAlert('Failed to delete ticket', 'error');
            });
    }
}

function closeModal() {
    $('#ticketModal').hide();
}

$('#ticketForm').submit(function(e) {
    e.preventDefault();
    
    const formData = {
        title: $('#title').val(),
        description: $('#description').val(),
        price: parseFloat($('#price').val()),
        quantity: parseInt($('#quantity').val()),
        sale_start_date: $('#sale_start_date').val() + ':00',
        sale_end_date: $('#sale_end_date').val() + ':00',
        visibility: $('#visibility').val(),
        image_url: currentImageUrl
    };

    // Handle file upload if a new file is selected
    const fileInput = $('#ticket_image')[0];
    if (fileInput.files.length > 0) {
        uploadImage(fileInput.files[0], function(imageUrl) {
            if (imageUrl) {
                formData.image_url = imageUrl;
                saveTicket(formData);
            } else {
                showAlert('Failed to upload image', 'error');
            }
        });
    } else {
        saveTicket(formData);
    }
});

function uploadImage(file, callback) {
    const formData = new FormData();
    formData.append('image', file);
    
    $.ajax({
        url: '../api/upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                callback(response.fileUrl);
            } else {
                showAlert('Upload error: ' + response.error, 'error');
                callback(null);
            }
        },
        error: function() {
            showAlert('Failed to upload image', 'error');
            callback(null);
        }
    });
}

function saveTicket(formData) {
    if (currentTicketId) {
        API.updateTicket(currentTicketId, formData)
            .done(function(response) {
                if (response.success) {
                    showAlert('Ticket updated successfully', 'success');
                    closeModal();
                    loadTickets();
                } else {
                    showAlert('Error: ' + response.error, 'error');
                }
            })
            .fail(function() {
                showAlert('Failed to update ticket', 'error');
            });
    } else {
        API.createTicket(formData)
            .done(function(response) {
                if (response.success) {
                    showAlert('Ticket created successfully', 'success');
                    closeModal();
                    loadTickets();
                } else {
                    showAlert('Error: ' + response.error, 'error');
                }
            })
            .fail(function() {
                showAlert('Failed to create ticket', 'error');
            });
    }
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
    
    // Auto-hide after 4 seconds
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
    if (e.target.id === 'ticketModal') {
        closeModal();
    }
});
