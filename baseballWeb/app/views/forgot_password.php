<?php
$pageTitle = "2D Baseball | Forgot Password";
$pageDescription = "Reset your account password.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/signin.css?v=' . time() . '">';
?>

<main class="auth-page">

    <div class="auth-box">
        <h1>Forgot Password?</h1>
        <p class="reset">Enter the email associated with your account and we will send you a link to reset your password.</p>

        <form id="forgotPasswordForm" class="auth-form" novalidate>
            <div class="form-group">
                <input type="email" id="email" placeholder="email" name="email" required>
                <small class="error-msg"></small>
            </div>

            <button type="submit" class="auth-btn" disabled>Send Reset Link</button>
        </form>

        <div id="resultMessage" class="result-box hidden">
            <div class="result-icon"></div>
            <p class="result-message"></p>
            <a href="<?= $baseUrl ?>support" class="result-link" style="display:none;">Contact Support</a>
        </div>

    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("forgotPasswordForm");
    const emailInput = document.getElementById("email");
    const submitBtn = form.querySelector(".auth-btn");
    const resultBox = document.getElementById("resultMessage");
    const resultMsg = resultBox.querySelector("p");
    const errorMsg = emailInput.nextElementSibling;

    function validateEmail() {
        const val = emailInput.value.trim();
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const valid = pattern.test(val);
        submitBtn.disabled = !valid;
        if (!valid && val !== '') {
            errorMsg.textContent = "Invalid email format";
            errorMsg.style.opacity = 1;
            emailInput.classList.add("error");
        } else {
            errorMsg.textContent = "";
            errorMsg.style.opacity = 0;
            emailInput.classList.remove("error");
        }
        return valid;
    }

    validateEmail();
    emailInput.addEventListener("input", validateEmail);

    form.addEventListener("submit", e => {
        e.preventDefault();
        if (!validateEmail()) return;

        fetch("<?= $baseUrl ?>app/auth/forgot_password_process.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "email=" + encodeURIComponent(emailInput.value.trim())
        })
        .then(res => res.json())
        .then(data => {
            const box  = document.getElementById("resultMessage");
            const icon = box.querySelector(".result-icon");
            const msg  = box.querySelector(".result-message");
            const link = box.querySelector(".result-link");
            box.style.display = "block";
            box.classList.remove("hidden", "success", "error");

            if (data.success) {
                box.classList.add("success");
                icon.innerHTML = `
                    <svg viewBox="0 0 60 60">
                        <circle class="check-circle" cx="30" cy="30" r="28"></circle>
                        <path class="check-mark" d="M18 30 L28 40 L42 22"></path>
                    </svg>
                `;
                msg.textContent = "Reset link has been sent.";
                link.style.display = "inline-block";
                form.style.display = "none";
            } else {
                box.classList.add("error");
                icon.innerHTML = "❌";
                msg.textContent = data.message || "Failed to send reset link.";
                link.style.display = "none";
            }
        })
        .catch(() => {
            const box  = document.getElementById("resultMessage");
            const icon = box.querySelector(".result-icon");
            const msg  = box.querySelector(".result-message");
            const link = box.querySelector(".result-link");

            box.style.display = "block";
            box.classList.remove("hidden", "success");
            box.classList.add("error");

            icon.innerHTML = "❌";
            msg.textContent = "Server error. Try again later.";
            link.style.display = "none";
        });
    });
});

</script>
