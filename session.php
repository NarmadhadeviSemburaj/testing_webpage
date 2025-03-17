// Set session timeout to 5 minutes (300 seconds)
$timeout = 300; // 5 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Last request was more than 5 minutes ago
    session_unset();     // Unset $_SESSION variable for this page
    session_destroy();   // Destroy session data
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time stamp



<!-- Session Timeout Popup -->
<div id="sessionPopup" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Session Expiring Soon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Your session will expire in 2 minutes. Please save your work.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    // Session timeout in milliseconds (5 minutes)
        const sessionTimeout = 5 * 60 * 1000; // 5 minutes in milliseconds

        // Time before showing the popup (2 minutes before timeout)
        const popupTime = 2 * 60 * 1000; // 2 minutes in milliseconds

        // Show the session timeout popup
        setTimeout(() => {
            const sessionPopup = new bootstrap.Modal(document.getElementById('sessionPopup'));
            sessionPopup.show();
        }, sessionTimeout - popupTime);

        // Logout the user after session timeout
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, sessionTimeout);