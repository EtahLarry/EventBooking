    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>EventBooking Cameroon</h5>
                    <p>Your premier destination for discovering and booking amazing events across Cameroon and Central Africa.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light">Home</a></li>
                        <li><a href="events.php" class="text-light">Events</a></li>
                        <li><a href="about.php" class="text-light">About Us</a></li>
                        <li><a href="contact.php" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-envelope"></i> nkumbelarry@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +237 652 731 798</p>
                    <p><i class="fas fa-map-marker-alt"></i> Yaoundé, Cameroon</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 EventBooking Cameroon. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>

    <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
    <!-- Notification System JavaScript -->
    <script>
    // Notification system for logged-in users
    let notificationInterval;

    // Load notifications when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();

        // Check for new notifications every 30 seconds
        notificationInterval = setInterval(loadNotifications, 30000);

        // Load notifications when dropdown is opened
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            notificationDropdown.addEventListener('click', function() {
                loadNotifications();
            });
        }
    });

    // Load notifications from server
    function loadNotifications() {
        const currentPath = window.location.pathname;
        const basePath = currentPath.includes('/user/') ? '' : 'user/';

        fetch(basePath + 'check-notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                    updateNotificationList(data.notifications);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';

                // Add pulse animation for new notifications
                badge.classList.add('animate-pulse');
                setTimeout(() => badge.classList.remove('animate-pulse'), 2000);
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Update notification list
    function updateNotificationList(notifications) {
        const list = document.getElementById('notificationList');
        if (!list) return;

        if (notifications.length === 0) {
            list.innerHTML = `
                <div class="dropdown-item text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    No notifications yet
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const readClass = notification.is_read ? '' : 'bg-light border-start border-primary border-3';
            const icon = getNotificationIcon(notification.type);

            html += `
                <div class="dropdown-item notification-item ${readClass}" style="white-space: normal; max-width: 100%;">
                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1">
                            <i class="${icon} text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${escapeHtml(notification.title)}</h6>
                            <p class="mb-1 text-muted small">${escapeHtml(notification.content)}</p>
                            <small class="text-muted">${notification.time_ago}</small>
                        </div>
                        ${!notification.is_read ? '<div class="ms-2"><span class="badge bg-primary">New</span></div>' : ''}
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
    }

    // Get icon for notification type
    function getNotificationIcon(type) {
        switch (type) {
            case 'message_reply': return 'fas fa-reply';
            case 'booking': return 'fas fa-ticket-alt';
            case 'system': return 'fas fa-cog';
            default: return 'fas fa-bell';
        }
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Mark all notifications as read
    function markAllNotificationsRead() {
        const currentPath = window.location.pathname;
        const basePath = currentPath.includes('/user/') ? '' : 'user/';

        fetch(basePath + 'mark-notifications-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({action: 'mark_all_read'})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload to update display
                showNotificationToast('All notifications marked as read', 'success');
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }

    // Show toast notification
    function showNotificationToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove from DOM after hiding
        toast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toast);
        });
    }

    // Clean up interval when page unloads
    window.addEventListener('beforeunload', function() {
        if (notificationInterval) {
            clearInterval(notificationInterval);
        }
    });
    </script>

    <!-- Notification Styles -->
    <style>
    .notification-dropdown {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-radius: 10px;
    }

    .notification-item {
        border-radius: 8px;
        margin: 2px 8px;
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        background-color: rgba(0,123,255,0.1) !important;
    }

    .animate-pulse {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    #notificationBadge {
        font-size: 0.7rem;
        min-width: 18px;
        height: 18px;
        line-height: 18px;
    }
    </style>
    <?php endif; ?>
</body>
</html>
