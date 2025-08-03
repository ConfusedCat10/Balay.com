<?php
include "../database/database.php"; 

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create an account</title>

    <?php
    include "../php/head_tag.php";
    ?>


    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .step-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex: 1;
            margin: 20px;
        }

        form {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
        }
        
        .form-group {
            margin-right: 10px;
        }

        .step {
            display: none;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .step h2 {
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            margin: 5px;
            border: none;
            background-color: gold;
            color: maroon;
            border-radius: 5px;
            cursor: pointer;
        }

        button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .error {
            color: red;
        }

        .error-border {
            border: 2px solid red;
        }

        .error-text {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Role Selection Styling */
        .role-selection {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .role-option {
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 8px;
            transition: border-color: 0.3s;
            width: 100%;
        }

        .role-option:hover {
            border-color: gold;
        }

        .role-option input {
            display: none;
        }

        .role-option .role-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .role-option img {
            width: 50px;
            height: 50px;
        }

        .role-option span {
            margin-top: 10px;
            font-size: 1rem;
        }

        .role-option input:checked + .role-content {
            border-color: maroon;
        }

        .highlight-role {
            border-color: maroon;
        }

        #otp-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .otp-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 20px;
            margin: 0 5px;
        }

        .form-inline {
            display: flex;
        }

        .form-inline input, .form-inline select {
            margin-right: 5px;
        }

        .mandatory:after {
            content: "*";
            color: red;
        }

        

        @media (max-width: 600px) {
            form {
                width: 90%;
                max-width: 500px;
            }

            .form-inline {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php
        include "../php/header.php";
        ?>
    </header>

    <!-- Content -->
     <div class="step-container">
        <form action="" id="create-account-form">
            <!-- Step 1: Choose your role -->
            <div class="step" id="step-1">
                <h2>Select Your Role</h2>
                <div class="role-selection">
                    <label class="role-option">
                        <input type="radio" name="role" id="radio-tenant" value="tenant">
                        <div class="role-content">
                            <img src="/bookingapp/assets/icons/tenant-icon.jpg" alt="tenant icon" class="role-icon">
                            <span>Tenant</span>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" id="radio-owner" value="owner">
                        <div class="role-content">
                            <img src="/bookingapp/assets/icons/dormitory-icon.png" alt="dormitory icon" class="role-icon"> 
                            <span>Establishment Owner</span>
                        </div>
                    </label>
                </div>
                <div class="error" id="role-error"></div>
                <!-- <button type="button" id="next-1" class="next w3-right">Next</button> -->
            </div>

            <!-- Step 2: Tenant Form -->
            <div class="step" id="step-2-tenant">
                <h2>Tenant Information</h2>

                <label for="email" class="mandatory">Institutional Email:</label>

                <div class="form-inline">
                    <input type="text" name="email-prefix" class="w3-input w3-half" id="email-prefix" placeholder="Enter email" required>

                    <select name="email-domain" class="w3-select w3-third" id="email-domain" required>
                        <option value="@gmail.com">@gmail.com</option>
                        <option value="@s.msumain.edu.ph">@s.msumain.edu.ph</option>
                        <option value="@msumain.edu.ph">@msumain.edu.ph</option>
                    </select>
                </div>
                
                <label for="university-id" class="mandatory">University ID Number:</label>
                <input type="number" name="university-id" class="w3-input w3-medium" id="university-id" placeholder="Enter your university ID number" required>

                <!-- Common fields for both tenant and owner -->
                <label for="full-name-tenant" class="mandatory">Full name:</label>
                <div class="form-inline">
                    <input type="text" name="first-name-tenant" id="first-name-tenant" class="w3-input" placeholder="First name" required>
                    <input type="text" name="middle-name-tenant" id="middle-name-tenant" class="w3-input" placeholder="Middle name (optional)">
                    <input type="text" name="last-name-tenant" id="last-name-tenant" class="w3-input" placeholder="Last name" required>
                    <select name="ext-name-tenant" id="ext-name-tenant" class="w3-select">
                        <option value="" selected disabled>Ext. (optional)</option>
                        <option value="">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                    </select>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tenant-gender" class="mandatory">Gender:</label>
                        <select name="tenant-gender" id="tenant-gender" class="w3-select" required>
                            <option value="" selected disabled>Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tenant-contact" class="mandatory">Contact number:</label>
                        <input type="tel" maxlength="11" name="tenant-contact" id="tenant-contact" class="w3-input" placeholder="09xx xxx xxxx" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="tenant-address" class="mandatory">Home address:</label>
                    <input type="text" name="tenant-address" id="tenant-address" class="w3-input" placeholder="Enter where do you live" required>
                </div>

                <div class="form-group">
                    <label for="tenant-username" class="mandatory">Account username:</label>
                    <input type="text" name="tenant-username" id="tenant-username" class="w3-input" placeholder="Enter a username" required>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tenant-password" class="mandatory">Password:</label>
                        <input type="password" name="tenant-password" id="tenant-password" class="w3-input" placeholder="Enter a password." required>
                    </div>

                    <div class="form-group">
                        <label for="tenant-confirm-password" class="mandatory">Confirm password:</label>
                        <div class="form-inline">
                            <input type="password" name="tenant-confirm-password" id="tenant-confirm-password" class="w3-input" placeholder="Re-enter your password." required>
                            <a class="w3-tiny w3-button toggle-password" title="Click to show password"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </div>
                </div>


                <button type="button" id="prev-tenant" class="prev w3-left">Previous</button>
                <button type="button" id="next-tenant" class="next w3-right">Next</button>
            </div>

            <!-- Step 2 - Owner Form -->
            <div class="step" id="step-2-owner">
                <h2>Establishment Owner Information</h2>

                <label for="email-owner" class="mandatory">Email Address:</label>
                <input type="email" name="email-owner" id="email-owner" class="w3-input" placeholder="Enter your email address" required>

                <!-- Common fields for both tenant and owner -->
                <!-- Common fields for both tenant and owner -->
                <label for="full-name-owner" class="mandatory">Full name:</label>
                <div class="form-inline">
                    <input type="text" name="first-name-owner" id="first-name-owner" class="w3-input" placeholder="First name" required>
                    <input type="text" name="middle-name-owner" id="middle-name-owner" class="w3-input" placeholder="Middle name (optional)">
                    <input type="text" name="last-name-owner" id="last-name-owner" class="w3-input" placeholder="Last name" required>
                    <select name="ext-name-owner" id="ext-name-owner" class="w3-select">
                        <option value="" selected disabled>Ext. (optional)</option>
                        <option value="">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                    </select>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="owner-gender" class="mandatory">Gender:</label>
                        <select name="owner-gender" id="owner-gender" class="w3-select" required>
                            <option value="" selected disabled>Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="owner-contact" class="mandatory">Contact number:</label>
                        <input type="tel" maxlength="11" name="owner-contact" id="owner-contact" class="w3-input" placeholder="09xx xxx xxxx" required>
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="owner-address" class="mandatory">Home address:</label>
                    <input type="text" name="owner-address" id="owner-address" class="w3-input" placeholder="Enter where do you live" required>
                </div>

                <div class="form-group">
                    <label for="owner-username" class="mandatory">Account username:</label>
                    <input type="text" name="owner-username" id="owner-username" class="w3-input" placeholder="Enter a username" required>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="owner-password" class="mandatory">Password:</label>
                        <input type="password" name="owner-password" id="owner-password" class="w3-input" placeholder="Enter a password." required>
                    </div>

                    <div class="form-group">
                        <label for="owner-confirm-password" class="mandatory">Confirm password:</label>
                        <div class="form-inline">
                            <input type="password" name="owner-confirm-password" id="owner-confirm-password" class="w3-input" placeholder="Re-enter your password." required>
                            <a class="w3-tiny w3-button toggle-password" title="Click to show password"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </div>
                </div>


                <button type="button" id="prev-owner" class="prev w3-left">Previous</button>
                <button type="button" id="next-owner" class="next w3-right">Next</button>
            </div>

            <!-- Step 3 - OTP Verification -->
            <div class="step" id="step-3">
                <h2>Verify Your Email</h2>
                <p>Enter the 6-character OTP sent to your email:</p>
                <div id="otp-container" style="margin-top: 10px">
                    <input type="text" maxlength="1" id="otp-1" class="otp-input" oninput="moveToNextInput(this, 'otp-2')" required>
                    <input type="text" maxlength="1" id="otp-2" class="otp-input" oninput="moveToNextInput(this, 'otp-3')" required>
                    <input type="text" maxlength="1" id="otp-3" class="otp-input" oninput="moveToNextInput(this, 'otp-4')" required>
                    <input type="text" maxlength="1" id="otp-4" class="otp-input" oninput="moveToNextInput(this, 'otp-5')" required>
                    <input type="text" maxlength="1" id="otp-5" class="otp-input" oninput="moveToNextInput(this, 'otp-6')" required>
                    <input type="text" maxlength="1" id="otp-6" class="otp-input" oninput="validateOTP()" required>
                </div>
                <span id="otp-error" style="color: red;"></span>
                <button type="button" id="prev-otp" class="prev w3-left">Previous</button>
                <button type="submit" id="submit-otp-btn" class="w3-right" disabled>Submit</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-bar" style="font-size: 12px;">
            Copyright &copy; <span id="year"></span> <br>College of Information and Computing Sciences<br> Mindanao State University - Main Campus.
        </div>
    </footer>

    <div id="snackbar"></div>

    <script>
        // Global variables to track the current Step
        let currentStep = 1;
        let selectedRole = '';

        // Show step 1 by default
        document.getElementById('step-1').style.display = 'block';

        // Event listeners for Next/Previous buttons
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('click', function() {
                selectedRole = this.value;
                document.getElementById('step-1').style.display = 'none';
                document.getElementById(`step-2-${selectedRole}`).style.display = 'block';
            });
        });

        document.getElementById('prev-tenant').addEventListener('click', function() {
            showStep('step-1', 'step-2-tenant');
        });

        document.getElementById('prev-owner').addEventListener('click', function() {
            showStep('step-1', 'step-2-owner');
        });

        document.getElementById('next-tenant').addEventListener('click', function() {
            if (validateFields('tenant')) {
                showStep('step-3', 'step-2-tenant');
            }
        });

        document.getElementById('next-owner').addEventListener('click', function() {
            if (validateFields('owner')) {
                showStep('step-3', 'step-2-owner');
            }
        });

        document.getElementById('prev-otp').addEventListener('click', function() {
            showStep(`step-2-${selectedRole}`, 'step-3');
        });
        

        // Helper functions
        function showStep(nextStep, currentStep) {
            document.getElementById(currentStep).style.display = 'none';
            document.getElementById(nextStep).style.display = 'block';
        }

        // OTP validation
        function moveToNextInput(currentInput, nextInputId) {
            if (currentInput.value.length === currentInput.maxLength) {
                document.getElementById(nextInputId).focus();
            }
            validateOTP();
        }

        function validateFields(role) {
            // Common fields
            const firstName = document.getElementById(`first-name-${role}`);
            const lastName = document.getElementById(`last-name-${role}`);
            const gender = document.getElementById(`${role}-gender`);
            const contact = document.getElementById(`${role}-contact`);
            const address = document.getElementById(`${role}-address`);
            const username = document.getElementById(`${role}-username`);
            const password = document.getElementById(`${role}-password`);
            const confirmPassword = document.getElementById(`${role}-confirm-password`);

            // Tenant fields
            const instEmailPrefix = document.getElementById("email-prefix");
            const instEmailDomain = document.getElementById("email-domain");
            const idNumber = document.getElementById("university-id");

            // Owner fields
            const ownerEmail = document.getElementById("email-owner");

            clearErrors([firstName, lastName, gender, contact, address, username, password, confirmPassword]);

            if (!firstName.value || !lastName.value || !gender.value || !contact.value || !address.value || !username.value || !password.value || !confirmPassword.value) {
                showSnackbar("Please complete the fields in red.");
                highlightErrors([firstName, lastName, gender, contact, address, username, password, confirmPassword]);
                return false;
            }

            if (role === 'tenant') {
                clearErrors([instEmailPrefix, instEmailDomain, idNumber]);

                if (!instEmailPrefix.value || !instEmailDomain.value || !idNumber.value) {
                    showSnackbar("You missed necessary fields.");
                    highlightErrors([instEmailPrefix, instEmailDomain, idNumber]);
                    return false;
                }

                if (!validateIDNumber(idNumber.value)) {
                    displayError(idNumber, "You entered invalid ID number.");
                    return false;
                }
            } else if (role === 'owner') {
                clearErrors([ownerEmail]);

                if (!ownerEmail.value) {
                    displayError(ownerEmail, "You missed to enter an email address.");
                    return false;
                }

                if (!validateEmail(ownerEmail.value)) {
                    displayError(ownerEmail, "You entered an invalid email address.");
                    return false;
                }
            }

            if (!validateContact(contact.value)) {
                displayError(contact, "You entered an invalid contact number.");
                return false;
            }

            if (!validatePassword(password)) {
                return false;
            }

            if (password.value !== confirmPassword.value) {
                displayError(confirmPassword, "Passwords do not match.");
                return false;
            }

            return true;
        }

        function validateIDNumber(idNumber) {
            const re = /^[0-9]{7}$|^[0-9]{9}$/;
            return re.test(idNumber);
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function validateContact(contact) {
            const re = /^09\d{9}$/;
            return re.test(contact);
        }

        function validatePassword(password) {
            // check the length of characters
            if (password.value.length < 8) {
                displayError(password, "Password must be at least 8 characters.");
                return false;
            }

            // Add more password rules
        }

        function displayError(input, message) {
            input.classList.add("error-border");
            showSnackbar(message);
            const errorMessage = document.createElement("small");
            errorMessage.classList.add("error-text");
            errorMessage.textContent = message;
            input.parentNode.appendChild(errorMessage);
        }

        function clearErrors(fields) {
            fields.forEach(field => {
                field.classList.remove("error-border");
                const errorText = field.parentNode.querySelector(".error-text");
                if (errorText) {
                    errorText.remove();
                }
            });
        }

        function highlightErrors(fields) {
            fields.forEach(field => {
                if (!field.value) {
                    field.classList.add("error-border");
                }
            })
        }

        function validateOTP() {
            const otp1 = document.getElementById("otp-1").value;
            const otp2 = document.getElementById("otp-2").value;
            const otp3 = document.getElementById("otp-3").value;
            const otp4 = document.getElementById("otp-4").value;
            const otp5 = document.getElementById("otp-5").value;
            const otp6 = document.getElementById("otp-6").value;

            const otp = otp1 + otp2 + otp3 + otp4 + otp5 + otp6;
            const otpRegex = /^[A-Za-z0-9]{6}$/;

            if (otp.length === 6 && otpRegex.test(otp)) {
                document.getElementById("otp-error").innerText = "";
                document.getElementById("submit-otp-btn").disabled = false;
            } else {
                document.getElementById("otp-error").innerText = "Please enter a valid 6-character OTP.";
                document.getElementById("submit-otp-btn").disabled = true;
            }
        }

        // Backspace functionality to move back to the previous input
        document.querySelectorAll('.otp-input').forEach(input => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace' && this.value === '') {
                    const prevInput = this.previousElementSibling;
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.value = '';
                    }
                }
            });
        });

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = document.getElementById(`${selectedRole}-password`);
                const confirmPasswordField = document.getElementById(`${selectedRole}-confirm-password`);

                if (passwordField.type == 'password') {
                    passwordField.type = 'text';
                    confirmPasswordField.type = 'text';
                    this.title = "Click to hide the passwords.";
                    this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
                } else {
                    passwordField.type = 'password';
                    confirmPasswordField.type = 'password';
                    this.title = "Click to show the passwords.";
                    this.innerHTML = '<i class="fa-solid fa-eye"></i>';
                }
            });
        });

        // Function to show the snackbar
        function showSnackbar(message) {
            const snackbar = document.getElementById('snackbar');
            snackbar.textContent = message;
            snackbar.classList.add('show');

            // After 3 seconds, hide the snackbar
            setTimeout(() => {
                snackbar.classList.remove('show');
                snackbar.classList.add('hide');
            }, 3000);

            // Reset hide class after animation
            setTimeout(() => {
                snackbar.classList.remove('hide');
            }, 3500);
        }

        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

</body>
</html>