// Elements
const step1 = document.getElementById('step1');
const step2Otp = document.getElementById('step2-otp');
const step2Message = document.getElementById('step2-message');
const step3Confirmation = document.getElementById('step3-confirmation');
const otpTimer = document.getElementById('otpTimer');
const resendOTPLink = document.getElementById('resendOTPLink');
const confirmationMessage = document.getElementById('confirmationMessage');
    
// Step 1 Buttons
document.getElementById('viaEmailBtn').addEventListener('click', () => showStep2Otp());
document.getElementById('viaSMSBtn').addEventListener('click', () => showStep2Otp());
document.getElementById('appealAdminBtn').addEventListener('click', () => showStep2Message());

// Back Buttons
document.getElementById('backToStep1').addEventListener('click', () => showStep1());
document.getElementById('backToStep1Message').addEventListener('click', () => showStep1());

function showStep1() {
    step1.classList.remove('hidden');
    step2Otp.classList.add('hidden');
    step2Message.classList.add('hidden');
    step3Confirmation.classList.add('hidden');
}

function showStep2Otp() {
    step1.classList.add('hidden');
    step2Otp.classList.remove('hidden');
    step2Message.classList.add('hidden');
    step3Confirmation.classList.add('hidden');
    startOtpTimer(300); // Start the 5-minute countdown
}

function showStep2Message() {
    step1.classList.add('hidden');
    step2Otp.classList.add('hidden');
    step2Message.classList.remove('hidden');
    step3Confirmation.classList.add('hidden');
}

function showStep3Confirmation(message) {
    step1.classList.add('hidden');
    step2Otp.classList.add('hidden');
    step2Message.classList.add('hidden');
    step3Confirmation.classList.remove('hidden');
    confirmationMessage.textContent = message;
}

// OTP Countdown Timer
function startOtpTimer(seconds) {
    let timeLeft = seconds;
    const timer = setInterval(() => {
        let minutes = Math.floor(timeLeft / 60);
        let secondsLeft = timeLeft % 60;
        otpTimer.textContent = `OTP valid for ${minutes}:${secondsLeft < 10 ? '0' + secondsLeft : secondsLeft} minutes.`;
        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(timer);
            resendOTPLink.classList.remove('disabled');
        }
    }, 1000);
}

// Resend OTP link disabled
resendOTPLink.classList.add('disabled');

// Simulate sending OTP and message
document.getElementById('submitOtpBtn').addEventListener('click', () => showStep3Confirmation('Your OTP has been submitted.'));
document.getElementById('submitMessageBtn').addEventListener('click', () => showStep3Confirmation('Your message has been sent.'));