// Main JavaScript for Event Booking System

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add to cart functionality
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        const eventId = $(this).data('event-id');
        const quantity = $(this).closest('.event-card, .event-details').find('.quantity-input').val() || 1;
        const button = $(this);
        const originalText = button.html();
        
        // Show loading state
        button.html('<span class="loading"></span> Adding...');
        button.prop('disabled', true);
        
        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            data: {
                action: 'add',
                event_id: eventId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update cart count
                    updateCartCount();
                    
                    // Show success message
                    showAlert('success', 'Item added to cart successfully!');
                    
                    // Reset button
                    button.html('<i class="fas fa-check"></i> Added');
                    setTimeout(() => {
                        button.html(originalText);
                        button.prop('disabled', false);
                    }, 2000);
                } else {
                    showAlert('danger', response.message || 'Failed to add item to cart');
                    button.html(originalText);
                    button.prop('disabled', false);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });

    // Update cart quantity
    $('.quantity-update').on('click', function() {
        const eventId = $(this).data('event-id');
        const action = $(this).data('action');
        const quantityInput = $(this).siblings('.quantity-input');
        let currentQuantity = parseInt(quantityInput.val());
        
        if (action === 'increase') {
            currentQuantity++;
        } else if (action === 'decrease' && currentQuantity > 1) {
            currentQuantity--;
        }
        
        quantityInput.val(currentQuantity);
        updateCartItem(eventId, currentQuantity);
    });

    // Remove from cart
    $('.remove-from-cart').on('click', function(e) {
        e.preventDefault();
        
        const eventId = $(this).data('event-id');
        const cartItem = $(this).closest('.cart-item');
        
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            $.ajax({
                url: 'api/cart.php',
                method: 'POST',
                data: {
                    action: 'remove',
                    event_id: eventId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        cartItem.fadeOut(300, function() {
                            $(this).remove();
                            updateCartTotal();
                            updateCartCount();
                        });
                        showAlert('success', 'Item removed from cart');
                    } else {
                        showAlert('danger', response.message || 'Failed to remove item');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            });
        }
    });

    // Search form submission
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        
        const searchData = {
            search: $('#search').val(),
            location: $('#location').val(),
            date: $('#date').val()
        };
        
        // Redirect to events page with search parameters
        const params = new URLSearchParams(searchData);
        window.location.href = 'events.php?' + params.toString();
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});

// Update cart item quantity
function updateCartItem(eventId, quantity) {
    $.ajax({
        url: 'api/cart.php',
        method: 'POST',
        data: {
            action: 'update',
            event_id: eventId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCartTotal();
                updateCartCount();
            } else {
                showAlert('danger', response.message || 'Failed to update quantity');
            }
        },
        error: function() {
            showAlert('danger', 'An error occurred. Please try again.');
        }
    });
}

// Update cart count in navigation
function updateCartCount() {
    $.ajax({
        url: 'api/cart.php',
        method: 'GET',
        data: { action: 'count' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#cart-count').text(response.count);
            }
        }
    });
}

// Update cart total
function updateCartTotal() {
    $.ajax({
        url: 'api/cart.php',
        method: 'GET',
        data: { action: 'total' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('.cart-total').text('$' + parseFloat(response.total).toFixed(2));
            }
        }
    });
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of main content
    $('main').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Smooth scroll to element
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Loading overlay
function showLoading() {
    const loadingHtml = `
        <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="text-center text-white">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Please wait...</div>
            </div>
        </div>
    `;
    $('body').append(loadingHtml);
}

function hideLoading() {
    $('#loading-overlay').remove();
}

// Initialize date picker for search
if (document.getElementById('date')) {
    document.getElementById('date').min = new Date().toISOString().split('T')[0];
}

// Image lazy loading
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading if supported
if ('IntersectionObserver' in window) {
    document.addEventListener('DOMContentLoaded', lazyLoadImages);
}
