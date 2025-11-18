<?php
$pageTitle = "2D Baseball | Register";
$pageDescription = "Create a new account.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/signin.css?v=' . time() . '">'; 
?>

<main class="auth-page">

    <div class="auth-box">
        <div id="register-result" class="result-box hidden">
            <div class="result-icon"></div>
            <p class="result-message"></p>
            <a href="<?= $baseUrl ?>login" class="result-link">Go to Login</a>
        </div>

        <h1>Create Account</h1>

        <form id="registerForm" class="auth-form" novalidate>

            <div class="form-group">
                <label>Username</label>
                <input id="reg-username" type="text" required tabindex="1">
                <small class="error-msg"></small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input id="reg-email" type="email" required tabindex="2">
                <small class="error-msg"></small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input id="reg-password" type="password" required tabindex="3">

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
                <div class="password-actions">
                    <button type="button" id="generate-pass" class="gen-btn">Generate Strong Password</button>
                    <button type="button" id="copy-pass" class="gen-btn">Copy</button>
                </div>
                <small id="password-strength"></small>
                <small class="error-msg"></small>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input id="reg-confirm" type="password" required tabindex="4">
                <small class="error-msg"></small>
            </div>

            <button type="submit" class="auth-btn" disabled tabindex="5">Register</button>

            <p class="register-link">
                Already have an account? <a href="<?= $baseUrl ?>login">Sign In</a>
            </p>

        </form>
    </div>

    <script> 
        document.addEventListener("DOMContentLoaded", () => {       
            const regForm = document.getElementById("registerForm");
            if (regForm) regForm.addEventListener("submit", validateRegister);
            const submitBtn = regForm.querySelector(".auth-btn");
            const username = document.getElementById("reg-username");
            const email = document.getElementById("reg-email");
            const pass = document.getElementById("reg-password");
            const confirm = document.getElementById("reg-confirm");

            regForm.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    if (!submitBtn.disabled) {
                        validateRegister(e);
                    }
                }
            });

            document.querySelectorAll(".toggle-pass").forEach(btn => {
                btn.addEventListener("click", () => {
                    const input = btn.parentElement.querySelector("input");

                    const state = btn.getAttribute("data-state");

                    if (state === "closed") {
                        input.type = "text";
                        btn.setAttribute("data-state", "open");
                    } else {
                        input.type = "password";
                        btn.setAttribute("data-state", "closed");
                    }
                });
            });

            [username, email, pass, confirm].forEach(input => {
                input.addEventListener("blur", () => validateField(input));
                input.addEventListener("input", () => validateField(input));
            });

           function validateField(field) {
                clearFieldError(field);

                if (field === username) {
                    if (username.value.trim().length < 3) {
                        showError(username, "Username must be at least 3 characters.");
                    } else if (!/^[a-zA-Z0-9_]+$/.test(username.value.trim())) {
                        showError(username, "Username can only contain letters, numbers, and underscores.");
                    }
                }

                if (field === email) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(email.value.trim())) {
                        showError(email, "Invalid email format.");
                    }
                }

                if (field === pass || field === confirm) {
                    const passVal = pass.value;
                    if (passVal.length < 8 || !/[A-Z]/.test(passVal) || !/[0-9]/.test(passVal)) {
                        showError(pass, "Password must be 8+ chars, contain a number and uppercase.");
                    }

                    if (confirm.value !== pass.value) {
                        showError(confirm, "Passwords do not match.");
                    }
                }

                checkFormValidity();
            }

            function checkFormValidity() {
                const allValid = 
                    /^[a-zA-Z0-9_]{3,}$/.test(username.value.trim()) &&
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()) &&
                    pass.value.length >= 8 &&
                    /[A-Z]/.test(pass.value) &&
                    /[0-9]/.test(pass.value) &&
                    pass.value === confirm.value;

                submitBtn.disabled = !allValid;
            }

            function validateRegister(e) {
                e.preventDefault();
                clearErrors();

                let valid = true;

                [username, email, pass, confirm].forEach(input => {
                    validateField(input);
                    if (input.classList.contains("error")) valid = false;
                });

                if (!valid) return;

                fetch("<?= $baseUrl ?>app/auth/register.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        username: username.value.trim(),
                        email: email.value.trim(),
                        password: pass.value
                    })
                })
                .then(res => res.json())
                .then(data => {

                    const box  = document.getElementById("register-result");
                    const icon = box.querySelector(".result-icon");
                    const msg  = box.querySelector(".result-message");

                    box.style.display = "block";
                    box.classList.remove("hidden", "success", "error");

                    if (data.success) {
                        box.classList.add("success");
                        icon.innerHTML = `
                            <svg viewBox="0 0 60 60">
                                <!-- kruh má r=28, cx/cy=30, aby byl uvnitř celého SVG -->
                                <circle class="check-circle" cx="30" cy="30" r="28"></circle>
                                <path class="check-mark" d="M18 30 L28 40 L42 22"></path>
                            </svg>
                        `;

                        msg.textContent = "Account successfully created!";
                        box.querySelector(".result-link").style.display = "inline-block"; // ukázat link
                    } else {
                        box.classList.add("error");
                        icon.innerHTML = "❌";
                        msg.textContent = data.message || "Registration failed.";
                        box.querySelector(".result-link").style.display = "none"; // schovat link
                    }
                })
                .catch(() => {
                    const box  = document.getElementById("register-result");
                    const icon = box.querySelector(".result-icon");
                    const msg  = box.querySelector(".result-message");

                    box.style.display = "block";
                    box.classList.remove("hidden", "success");
                    box.classList.add("error");

                    icon.innerHTML = "❌";
                    msg.textContent = "Server error. Try again later.";
                    box.querySelector(".result-link").style.display = "none"; // schovat link
                });
            }

            function showStrength() {
                const strength = document.getElementById("password-strength");
                const v = pass.value;

                let score = 0;
                if (v.length >= 8) score++;
                if (/[A-Z]/.test(v)) score++;
                if (/[0-9]/.test(v)) score++;

                const texts = ["Weak", "Medium", "Strong", "Very strong"];
                const colors = ["#ff4d4d", "#ffaa33", "#4BB543", "#4da3ff"];

                strength.textContent = texts[score];
                strength.style.color = colors[score];
            }

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

            const genBtn = document.getElementById("generate-pass");
            if (genBtn) genBtn.addEventListener("click", () => {
                const newPass = generateStrongPassword();
                pass.value = newPass;
                confirm.value = newPass;

                clearFieldError(pass);
                clearFieldError(confirm);

                showStrength();

                validateField(pass);
                validateField(confirm);

                checkFormValidity();
            });


            const copyBtn = document.getElementById("copy-pass");
                if (copyBtn) copyBtn.addEventListener("click", () => {
                    if (pass.value) {
                        navigator.clipboard.writeText(pass.value)
                            .then(() => {
                                copyBtn.textContent = "Copied!";
                                setTimeout(() => copyBtn.textContent = "Copy", 1500);
                            })
                            .catch(() => {
                                alert("Unable to copy password");
                            });
                    }
                });

            function generateStrongPassword() {
                const lower = "abcdefghijklmnopqrstuvwxyz";
                const upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                const digits = "0123456789";
                const symbols = "!@#$%^&*()_+{}[]<>?";
                
                const length = 14;
                let password = [];

                password.push(upper[Math.floor(Math.random() * upper.length)]);
                password.push(digits[Math.floor(Math.random() * digits.length)]);

                const allChars = lower + upper + digits + symbols;
                for (let i = password.length; i < length; i++) {
                    password.push(allChars[Math.floor(Math.random() * allChars.length)]);
                }

                password = password.sort(() => Math.random() - 0.5);

                return password.join('');
            }

        });

    </script>
</main>

