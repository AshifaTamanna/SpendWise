<?php
session_start();

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_email = isset($_SESSION['old_email']) ? $_SESSION['old_email'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

unset($_SESSION['errors']);
unset($_SESSION['old_email']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SpendWise</title>
    <link rel="stylesheet" href="../css/login-style.css">
    <style>
        .success-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .success-modal.show {
            display: flex;
        }

        .success-modal-content {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px 35px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            animation: loginSlideIn 0.3s ease-out;
        }

        @keyframes loginSlideIn {
            from {
                transform: translateY(-30px);
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
            border-radius: 50%;
            background: #28a745;
            color: white;
            font-size: 42px;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }

        .success-modal h2 {
            color: #020640;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .success-modal p {
            color: #4b5563;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 14px;
        }

        .success-modal button {
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            background: #020640;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .success-modal button:hover {
            background: #2c3397;
        }
    </style>
</head>
<body>
    <?php if (!empty($success)): ?>
        <div class="success-modal show" id="successModal">
            <div class="success-modal-content">
                <div class="success-icon">✓</div>
                <h2>Login Successful!</h2>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>. You can now continue to your dashboard.</p>
                <button type="button" onclick="window.location.href='dashboard.php'">Go to Dashboard</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="login-container">
        <div class="login-left">
            <img src="../image/spendwise_logoo.png" alt="SpendWise Logo">
            <h1>SpendWise</h1>
            <p>Track your income and expenses</p>
            <p>Take control of your financial life.</p>
        </div>

        <div class="login-right">
            <h2>Welcome Back!</h2>
            <p>Login to your account</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="../controllers/login-process.php" method="POST" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($old_email); ?>">
                    <div class="error-message" id="emailError"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password">
                    <div class="error-message" id="passwordError"></div>
                </div>

                <div class="login-options">
                    <label>
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" name="login">Login</button>
            </form>

            <p class="signup-text">
                Don't have an account?
                <a href="register-view.php">Sign up</a>
            </p>
        </div>
    </div>

    <script>
        <?php if (!empty($success)): ?>
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.classList.add('show');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 2000);
            }
        <?php endif; ?>

        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorBox = document.getElementById(fieldId + 'Error');
            field.classList.add('error');
            errorBox.textContent = message;
            errorBox.classList.add('show');
        }

        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorBox = document.getElementById(fieldId + 'Error');
            field.classList.remove('error');
            errorBox.textContent = '';
            errorBox.classList.remove('show');
        }

        function validateField(fieldId) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (fieldId === 'email') {
                if (emailInput.value.trim() === '') {
                    showError('email', 'Email is required.');
                    return false;
                }

                if (!emailPattern.test(emailInput.value.trim())) {
                    showError('email', 'Enter a valid email address.');
                    return false;
                }

                clearError('email');
                return true;
            }

            if (fieldId === 'password') {
                if (passwordInput.value === '') {
                    showError('password', 'Password is required.');
                    return false;
                }

                if (passwordInput.value.length < 6) {
                    showError('password', 'Password must be at least 6 characters.');
                    return false;
                }

                clearError('password');
                return true;
            }

            return true;
        }

        emailInput.addEventListener('input', () => validateField('email'));
        passwordInput.addEventListener('input', () => validateField('password'));

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isEmailValid = validateField('email');
            const isPasswordValid = validateField('password');

            if (isEmailValid && isPasswordValid) {
                form.submit();
            }
        });
    </script>
</body>
</html>

