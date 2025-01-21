<div class="modal" id="accountRecoveryModal">
    <div class="wizard-step" id="step1">
        <h2>Choose How to Recover Your Account</h2>
        <button class="option-btn" id="viaEmailBtn">Send via Email</button>
        <button class="option-btn" id="viaSMSBtn">Send via SMS or Phone Call</button>
        <button class="option-btn" id="appealAdminBtn">Appeal to Site Administrator</button>
    </div>

    <div class="wizard-step" id="step2-otp" style="display: none;">
        <h2>Enter OTP Code</h2>
        <div class="otp-inputs">
            <input type="text" maxlength="1" id="otp1">
            <input type="text" maxlength="1" id="otp2">
            <input type="text" maxlength="1" id="otp3">
            <input type="text" maxlength="1" id="otp4">
            <input type="text" maxlength="1" id="otp5">
            <input type="text" maxlength="1" id="otp6">
        </div>
        <p class="timer" id="optTimer">OTP valid for 5:00 minutes.</p>
        <p>Did't receive the OTP? <a href="#" class="resend-link disabled" id="resendOTPLink">Click Resend</a>.</p>
        <button class="submit-btn">Submit OTP</button>
        <button class="back-btn" id="backToStep1">Go Back</button>
    </div>

    <div class="wizard-step" id="step2-message" style="display: none;">
        <h2>Message to Administrator</h2>
        <textarea name="" id="adminMessage" class="message-field" placeholder="Enter your message..."></textarea>
        <button class="submit-btn">Send Message</button>
        <button class="back-btn" id="backToStep1Message">Go Back</button>
    </div>

    <div class="wizard-step" id="step3-confirmation" style="display: none;">
        <h2>Confirmation</h2>
        <p id="confirmationMessage"></p>
        <button class="submit-btn" onclick="location.reload()">Close</button>
    </div>
</div>
