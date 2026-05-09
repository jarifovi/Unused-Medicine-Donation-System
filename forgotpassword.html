<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MedDonate</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="forgotpassword.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo">
                <i class="fas fa-heartbeat"></i> MedDonate
            </a>

            <!-- Hamburger Button -->
            <button class="nav-toggle-btn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-links">
                <ul>
                    <li><a href="index.html"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="about.html"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="contact.html"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="login.html" class="btn-secondary active"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.html" class="btn-primary"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <section class="form-section">
            <div class="container">

                <!-- Step 1: Enter Email -->
                <div id="step1">
                    <h2>Reset Your Password</h2>
                    <p class="instruction">Enter your registered email to receive a 6-digit OTP.</p>

                    <form id="emailForm" novalidate>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" placeholder="you@example.com" required>
                            <span class="error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Send OTP</button>
                    </form>
                </div>

                <!-- Step 2: Enter OTP -->
                <div id="step2" style="display: none;">
                    <h2>Enter OTP</h2>
                    <p class="instruction">We sent a 6-digit code to your email.</p>

                    <form id="otpForm" novalidate>
                        <div class="form-group">
                            <label for="otp">Verification Code</label>
                            <input type="text" id="otp" maxlength="6" placeholder="123456" required>
                            <span class="error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Verify OTP</button>
                    </form>
                </div>

                <!-- Step 3: Reset Password -->
                <div id="step3" style="display: none;">
                    <h2>Create New Password</h2>
                    <p class="instruction">Your new password must be at least 6 characters.</p>

                    <form id="resetForm" novalidate>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" placeholder="••••••••" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" placeholder="••••••••" required>
                            <span class="error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </form>
                </div>

                <p class="form-link">
                    Remember your password? <a href="login.html">Back to Login</a>
                </p>

                <!-- Success Toast -->
                <div id="successToast" class="success-toast">
                    <i class="fas fa-check-circle"></i> Password reset successful! Redirecting...
                </div>

                <!-- Error Toast -->
                <div id="errorToast" class="error-toast">
                    <i class="fas fa-exclamation-circle"></i> <span id="errorMsg"></span>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© 2025 MedDonate. All rights reserved.</p>
            <ul class="footer-links">
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
            <div class="social-icons">
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="backToTop" title="Go to top">
        <i class="fas fa-arrow-up"></:up"></i>
    </button>

    <script>
        // === 1. Mobile Menu (No Checkbox) ===
        const toggleBtn = document.querySelector('.nav-toggle-btn');
        const navLinks = document.querySelector('.nav-links');

        toggleBtn.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            toggleBtn.classList.toggle('open');
        });

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('open');
                toggleBtn.classList.remove('open');
            });
        });

        // === 2. Password Reset Flow ===
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const successToast = document.getElementById('successToast');
        const errorToast = document.getElementById('errorToast');
        const errorMsg = document.getElementById('errorMsg');

        // Step 1: Send OTP
        document.getElementById('emailForm').addEventListener('submit', e => {
            e.preventDefault();
            clearErrors();

            const email = document.getElementById('email').value.trim();
            if (!/^\S+@\S+\.\S+$/.test(email)) return showError('Invalid email', 'email');

            const users = JSON.parse(localStorage.getItem('users') || '[]');
            const user = users.find(u => u.email === email);

            if (!user) {
                return showError('No account found with this email', 'email');
            }

            // Generate & Save OTP
            const otp = Math.floor(100000 + Math.random() * 900000);
            localStorage.setItem('resetOTP', otp);
            localStorage.setItem('resetEmail', email);
            localStorage.setItem('otpTime', Date.now());

            alert(`Your OTP is: ${otp}\n(Will expire in 5 minutes)`); // Replace with email later

            step1.style.display = 'none';
            step2.style.display = 'block';
        });

        // Step 2: Verify OTP
        document.getElementById('otpForm').addEventListener('submit', e => {
            e.preventDefault();
            clearErrors();

            const otp = document.getElementById('otp').value;
            const savedOTP = localStorage.getItem('resetOTP');
            const otpTime = localStorage.getItem('otpTime');

            if (!otp || otp.length !== 6) return showError('Enter 6-digit OTP', 'otp');
            if (!savedOTP) return showError('No OTP requested', 'otp');

            // Check expiry (5 min)
            if (Date.now() - otpTime > 5 * 60 * 1000) {
                clearResetData();
                return showError('OTP expired. Try again.', 'otp');
            }

            if (otp !== savedOTP) {
                return showError('Invalid OTP', 'otp');
            }

            step2.style.display = 'none';
            step3.style.display = 'block';
        });

        // Step 3: Reset Password
        document.getElementById('resetForm').addEventListener('submit', e => {
            e.preventDefault();
            clearErrors();

            const newPwd = document.getElementById('newPassword').value;
            const confirmPwd = document.getElementById('confirmPassword').value;

            if (newPwd.length < 6) return showError('Password too short', 'newPassword');
            if (newPwd !== confirmPwd) return showError('Passwords don\'t match', 'confirmPassword');

            const email = localStorage.getItem('resetEmail');
            let users = JSON.parse(localStorage.getItem('users') || '[]');
            const userIndex = users.findIndex(u => u.email === email);

            if (userIndex === -1) {
                showError('Session expired. Try again.', 'newPassword');
                return;
            }

            users[userIndex].password = newPwd;
            localStorage.setItem('users', JSON.stringify(users));
            clearResetData();

            showSuccess();
            setTimeout(() => window.location.href = 'login.html', 2000);
        });

        function showError(msg, fieldId) {
            const field = document.getElementById(fieldId);
            const error = field.parentElement.querySelector('.error');
            error.textContent = msg;
        }

        function clearErrors() {
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
        }

        function clearResetData() {
            localStorage.removeItem('resetOTP');
            localStorage.removeItem('resetEmail');
            localStorage.removeItem('otpTime');
        }

        function showSuccess() {
            successToast.classList.add('show');
            setTimeout(() => successToast.classList.remove('show'), 3000);
        }

        function showErrorToast(msg) {
            errorMsg.textContent = msg;
            errorToast.classList.add('show');
            setTimeout(() => errorToast.classList.remove('show'), 3000);
        }

        // === 3. Back to Top ===
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            backToTop.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
        backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // === 4. Fade-in ===
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.form-section').style.opacity = '1';
        });
    </script>
</body>
</html>