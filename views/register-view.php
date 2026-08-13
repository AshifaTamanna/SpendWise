<?php

session_start();

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_name = isset($_SESSION['old_name']) ? $_SESSION['old_name'] : '';
$old_email = isset($_SESSION['old_email']) ? $_SESSION['old_email'] : '';

unset($_SESSION['success']);
unset($_SESSION['errors']);
unset($_SESSION['old_name']);
unset($_SESSION['old_email']);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register - SpendWise</title>

    <link
        rel="stylesheet"
        href="../css/register.css"
    >

    <style>
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-group input.error,
        .form-group textarea.error {
            border-color: #dc3545 !important;
            background-color: #fff5f5;
        }

        .form-group input:focus.error {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* SUCCESS MODAL */
        .success-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .success-modal.show {
            display: flex;
        }

        .success-modal-content {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }

        .success-modal h2 {
            color: #28a745;
            margin: 20px 0 10px 0;
        }

        .success-modal p {
            color: #666;
            margin-bottom: 30px;
        }

        .success-modal button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .success-modal button:hover {
            background-color: #218838;
        }
    </style>

</head>


<body>

    <!-- SUCCESS POPUP MODAL -->
    <div class="success-modal" id="successModal">
        <div class="success-modal-content">
            <div class="success-icon">✓</div>
            <h2>Registration Successful!</h2>
            <p>Your account has been created successfully. You can now login.</p>
            <button onclick="location.href='login.php'">Go to Login</button>
        </div>
    </div>

    <div class="register-container">

        
        <!-- LEFT SIDE -->

        <div class="register-left">

            <img
                src="../image/spendwise_logoo.png"
                alt="SpendWise Logo"
            >

            <h1>SpendWise</h1>

            <p>
                Track your spending.<br>
                Manage your money.<br>
                Build better financial habits.
            </p>

        </div>

       
        <!-- RIGHT SIDE -->
       

        <div class="register-right">

            <h2>Create Account</h2>

            <p>
                Start managing your finances today.
            </p>

          
            <form
                action="../controllers/register-process.php"
                method="POST"
                id="registerForm"
                novalidate
            >

                <!-- NAME -->

                <div class="form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your full name"
                        value="<?php echo htmlspecialchars($old_name); ?>"
                    >

                    <div class="error-message" id="nameError"></div>

                </div>

                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php echo htmlspecialchars($old_email); ?>"
                    >

                    <div class="error-message" id="emailError"></div>

                </div>

                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Create a password (min 6 characters)"
                    >

                    <div class="error-message" id="passwordError"></div>

                </div>

                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                    >

                    <div class="error-message" id="confirm_passwordError" aria-live="polite"></div>

                </div>

                <!-- TERMS -->

                <div class="terms">

                    <label>

                        <input
                            type="checkbox"
                            name="terms"
                            id="terms"
                        >

                        <span>

                            I agree to the

                            <a href="#">
                                Terms & Conditions
                            </a>

                        </span>

                    </label>

                    <div class="error-message" id="termsError"></div>

                </div>

                <!-- REGISTER BUTTON -->

                <button
                    type="submit"
                    name="register"
                    id="submitBtn"
                >
                    Create Account
                </button>

            </form>

            <!-- LOGIN -->

            <p class="login-text">

                Already have an account?

                <a href="login.php">
                    Sign in
                </a>

            </p>

        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Show success modal if registration was successful
        <?php if (!empty($success)): ?>
            document.getElementById('successModal').classList.add('show');
        <?php endif; ?>

        // Form Validation
        const form = document.getElementById('registerForm');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const termsInput = document.getElementById('terms');

        // Re-check a field while the user corrects it, so the red message
        // stays directly below the related input until it is valid.
        [nameInput, emailInput, passwordInput, confirmPasswordInput].forEach((input) => {
            input.addEventListener('input', () => validateField(input.id));
            input.addEventListener('blur', () => validateField(input.id));
        });
        termsInput.addEventListener('change', () => validateField('terms'));

        // Clear error message
        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorId = fieldId + 'Error';
            const errorElement = document.getElementById(errorId);

            field.classList.remove('error');
            if (errorElement) {
                errorElement.classList.remove('show');
                errorElement.textContent = '';
            }
        }

        // Show error message
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorId = fieldId + 'Error';
            const errorElement = document.getElementById(errorId);

            field.classList.add('error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }
        }

        function validateField(fieldId) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            clearError(fieldId);

            if (fieldId === 'name' && nameInput.value.trim().length < 2) {
                showError('name', nameInput.value.trim() === '' ? 'Name is required.' : 'Name must be at least 2 characters.');
                return false;
            }

            if (fieldId === 'email' && (emailInput.value.trim() === '' || !emailPattern.test(emailInput.value.trim()))) {
                showError('email', emailInput.value.trim() === '' ? 'Email is required.' : 'Enter a valid email address.');
                return false;
            }

            if (fieldId === 'password' && passwordInput.value.length < 6) {
                showError('password', passwordInput.value === '' ? 'Password is required.' : 'Password must be at least 6 characters.');
                return false;
            }

            if (fieldId === 'confirm_password' && (confirmPasswordInput.value === '' || passwordInput.value !== confirmPasswordInput.value)) {
                showError('confirm_password', confirmPasswordInput.value === '' ? 'Please confirm your password.' : 'Passwords do not match.');
                return false;
            }

            if (fieldId === 'terms' && !termsInput.checked) {
                showError('terms', 'You must agree to the Terms & Conditions.');
                return false;
            }

            return true;
        }

        // Validate form
        function validateForm() {
            return ['name', 'email', 'password', 'confirm_password', 'terms']
                .map(validateField)
                .every(Boolean);
        }

        // Form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                // Form is valid, submit it
                form.submit();
            }
        });

        // Show existing errors from PHP (if any)
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                // Parse error message and show appropriate field error
                <?php 
                    $errorMessage = $error;
                    if (strpos($error, 'Name') !== false) {
                        echo "showError('name', '" . addslashes($errorMessage) . "');";
                    } elseif (stripos($error, 'email') !== false) {
                        echo "showError('email', '" . addslashes($errorMessage) . "');";
                    } elseif (strpos($error, 'Confirm password') !== false || strpos($error, 'Passwords') !== false) {
                        echo "showError('confirm_password', '" . addslashes($errorMessage) . "');";
                    } elseif (strpos($error, 'Password') !== false) {
                        echo "showError('password', '" . addslashes($errorMessage) . "');";
                    } elseif (strpos($error, 'Terms') !== false) {
                        echo "showError('terms', '" . addslashes($errorMessage) . "');";
                    }
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </script>

</body>

</html>
