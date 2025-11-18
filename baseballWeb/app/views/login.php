<?php
$pageTitle = "2D Baseball | Sign In";
$pageDescription = "Login to your account.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/signin.css?v=' . time() . '">';
?>

<main class="auth-page">

    <div class="auth-box">
        <h1>Sign In</h1>

        <form id="loginForm" class="auth-form" novalidate>

            <div class="form-group">
                <label>Email or Username</label>
                <input id="login-id" type="text" required tabindex="1">
                <small class="error-msg"></small>
            </div>

            <div class="form-group password-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input id="login-password" type="password" required tabindex="2">
                    <span class="toggle-pass" data-state="closed">
                        <svg class="icon-eye eye-closed" viewBox="0 0 24 24">
                            <path d="M3 3l18 18M10.7 10.7a3 3 0 104.6 4.6M6.4 6.4A11.7 11.7 0 003 12c1.8 4 6 7 9 7 1.7 0 3.3-.5 4.7-1.4" stroke="#275ea7" stroke-width="2" fill="none"/>
                        </svg>
                        <svg class="icon-eye eye-open" viewBox="0 0 24 24">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#275ea7" stroke-width="2" fill="none"/>
                            <circle cx="12" cy="12" r="3" fill="#275ea7"/>
                        </svg>
                    </span>
                </div>
                <small class="error-msg"></small>
            </div>

            <div class="form-group">
                <a href="#" class="forgot-password">Forgot password?</a>
            </div>

            <div class="form-group remember-wrapper">
                <label>
                    <input type="checkbox" id="remember-me" tabindex="4" name="remember-wrapper">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="auth-btn" disabled tabindex="5">Sign In</button>

            <p class="register-link">
                New here? <a href="<?= $baseUrl ?>register">Register here</a>
            </p>

        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const loginForm = document.getElementById("loginForm");
            const loginId = document.getElementById("login-id");
            const loginPass = document.getElementById("login-password");
            const submitBtn = loginForm.querySelector(".auth-btn");

            document.querySelectorAll(".toggle-pass").forEach(btn => {
                btn.addEventListener("click", () => {
                    const input = btn.parentElement.querySelector("input");
                    input.type = btn.getAttribute("data-state") === "closed" ? "text" : "password";
                    btn.setAttribute("data-state", btn.getAttribute("data-state") === "closed" ? "open" : "closed");
                });
            });

            [loginId, loginPass].forEach(input => {
                input.addEventListener("input", () => {
                    validateField(input);
                    checkFormValidity();
                });
                input.addEventListener("blur", () => validateField(input));
            });

            function validateField(input) {
                clearFieldError(input);
                if (!input.value.trim()) {
                    showError(input, "This field is required");
                }
            }

            function checkFormValidity() {
                submitBtn.disabled = !loginId.value.trim() || !loginPass.value.trim();
            }

            loginForm.addEventListener("submit", e => {
                e.preventDefault();
                clearErrors();

                validateField(loginId);
                validateField(loginPass);

                if (loginId.value.trim() && loginPass.value.trim()) {
                    alert("Login is valid. (Backend not connected yet)");
                }
            });

            function showError(input, msg) {
                const group = input.closest(".form-group");
                const errorMsg = group.querySelector(".error-msg");
                input.classList.add("error");
                errorMsg.textContent = msg;
                errorMsg.style.opacity = 1;
            }

            function clearErrors() {
                document.querySelectorAll(".error").forEach(i => i.classList.remove("error"));
                document.querySelectorAll(".error-msg").forEach(e => {
                    e.textContent = "";
                    e.style.opacity = 0;
                });
            }

            function clearFieldError(input) {
                const group = input.closest(".form-group");
                const errorMsg = group.querySelector(".error-msg");
                input.classList.remove("error");
                errorMsg.textContent = "";
                errorMsg.style.opacity = 0;
            }

            loginForm.addEventListener("keydown", e => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    if (!submitBtn.disabled) loginForm.dispatchEvent(new Event("submit"));
                }
            });
        });
    </script>
</main>
