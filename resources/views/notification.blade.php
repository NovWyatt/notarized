<style>
    .logout-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 350px;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .countdown-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

</style>
<div id="logoutNotification" class="logout-notification" style="display: none;">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="notification-content">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">Đăng nhập từ thiết bị khác!</h6>
                <p class="mb-0" id="notificationMessage"></p>
            </div>
            <div class="countdown-circle" id="countdownCircle">5</div>
        </div>
    </div>
</div>
<!-- Real-time Notification Script -->
<script>
    // CSRF Token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Notification elements
    const notificationElement = document.getElementById('logoutNotification');
    const messageElement = document.getElementById('notificationMessage');
    const countdownElement = document.getElementById('countdownCircle');

    let checkInterval;
    let countdownInterval;
    let countdown = 5;

    // Bắt đầu kiểm tra notification
    function startNotificationCheck() {
        if (checkInterval) clearInterval(checkInterval);

        checkInterval = setInterval(() => {
            fetch('/check-logout-notification', {
                    method: 'GET'
                    , headers: {
                        'X-CSRF-TOKEN': csrfToken
                        , 'Accept': 'application/json'
                        , 'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'logout_required') {
                        showLogoutNotification(data.notification);
                    } else if (data.status === 'session_expired') {
                        // Session đã hết hạn, tự động redirect
                        window.location.href = '/login';
                    }
                })
                .catch(error => {
                    console.error('Error checking notification:', error);
                });
        }, 2000); // Kiểm tra mỗi 2 giây
    }

    // Hiển thị thông báo logout
    function showLogoutNotification(notification) {
        clearInterval(checkInterval); // Dừng kiểm tra

        messageElement.textContent = notification.message;
        notificationElement.style.display = 'block';

        countdown = notification.countdown;
        countdownElement.textContent = countdown;

        // Bắt đầu đếm ngược
        countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                performForceLogout();
            }
        }, 1000);
    }

    // Thực hiện đăng xuất bắt buộc
    function performForceLogout() {
        fetch('/force-logout', {
                method: 'POST'
                , headers: {
                    'X-CSRF-TOKEN': csrfToken
                    , 'Accept': 'application/json'
                    , 'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'logged_out') {
                    window.location.href = '/login';
                }
            })
            .catch(error => {
                console.error('Error during force logout:', error);
                // Fallback: redirect anyway
                window.location.href = '/login';
            });
    }

    // Bắt đầu kiểm tra khi user đã đăng nhập
    @auth
    document.addEventListener('DOMContentLoaded', function() {
        startNotificationCheck();
    });

    // Dừng kiểm tra khi user rời khỏi trang
    window.addEventListener('beforeunload', function() {
        if (checkInterval) clearInterval(checkInterval);
        if (countdownInterval) clearInterval(countdownInterval);
    });
    @endauth

</script>
