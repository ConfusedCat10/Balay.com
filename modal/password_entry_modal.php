<!-- Modal for Password Entry -->
<div class="modal" id="passwordModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()" title="Click to close this dialog.">&times;</span>
            <h3 style="color: black">Enter Password</h3>

            <!-- Password input with toggle button overlaid -->
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Enter your password" onkeydown="enterPassword(event);" required>
                    <span id="togglePassword" onclick="togglePassword()">
                        <i id="toggleIcon" title="Click to show the password." class="fas fa-eye slash"></i>
                    </span>
                </div>
                <button type="submit" style="margin-top: 10px;" class="btn-primary" name="login">Login</button>

            <!-- Forgot Password Link -->
            <div class="forgot-password">
                <a href="/bookingapp/pages/account_recovery.php" id="forgotPasswordBtn">Forgot Password?</a>
            </div>

        </div>
    </div>