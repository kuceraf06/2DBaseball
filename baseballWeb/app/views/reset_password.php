<?php
$pageTitle = "2D Baseball | Reset Password";
$pageDescription = "Set a new password for your account.";
$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/signin.css?v=' . time() . '">';

require_once __DIR__ . '/../db/connect.php';

$token = trim($_GET['token'] ?? '');
if ($token === '') {
    die("Invalid password reset link.");
}

$stmt = $db->prepare("SELECT user_id, expires FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset || strtotime($reset['expires']) < time()) {
    die("This reset link is invalid or has expired.");
}
?>

<main class="auth-page">
    <div class="auth-box">
        <div id="login-result" class="result-box hidden">
            <div class="result-icon"></div>
            <p class="result-message"></p>
            <a href="<?= $baseUrl ?>login" class="result-link">Go to Login</a>
        </div>
        <h1>Reset Password</h1>
        <form id="resetPasswordForm" class="auth-form" novalidate>
            <input type="hidden" id="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <input type="password" id="password" placeholder="New Password" required>
                <small class="error-msg"></small>
            </div>
            <div class="form-group">
                <input type="password" id="passwordConfirm" placeholder="Confirm New Password" required>
                <small class="error-msg"></small>
            </div>
            <button type="submit" class="auth-btn">Reset Password</button>
        </form>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("resetPasswordForm");
    const tokenInput = document.getElementById("token");

    const pass = document.getElementById("password");
    const confirm = document.getElementById("passwordConfirm");

    const resultBox = document.getElementById("login-result");
    const resultIcon = resultBox.querySelector(".result-icon");
    const resultMsg = resultBox.querySelector(".result-message");
    const resultLink = resultBox.querySelector(".result-link");

    function showError(input, msg) {
        const group = input.closest(".form-group");
        const errorMsg = group.querySelector(".error-msg");

        input.classList.add("error");
        errorMsg.textContent = msg;
        errorMsg.style.opacity = 1;
    }

    function clearFieldError(input) {
        const group = input.closest(".form-group");
        const errorMsg = group.querySelector(".error-msg");

        input.classList.remove("error");
        errorMsg.textContent = "";
        errorMsg.style.opacity = 0;
    }

    function validateField(field) {
        clearFieldError(field);

        const passVal = pass.value.trim();
        const confirmVal = confirm.value.trim();

        if (field === pass) {
            if (
                passVal.length < 8 ||
                !/[A-Z]/.test(passVal) ||
                !/[0-9]/.test(passVal)
            ) {
                showError(pass, "Password must be 8+ chars, contain a number and uppercase.");
            }
        }

        if (field === confirm || field === pass) {
            if (confirmVal !== passVal) {
                showError(confirm, "Passwords do not match.");
            }
        }
    }

    function validateBeforeSubmit() {
        clearFieldError(pass);
        clearFieldError(confirm);

        let ok = true;

        const passVal = pass.value.trim();
        const confirmVal = confirm.value.trim();

        if (
            passVal.length < 8 ||
            !/[A-Z]/.test(passVal) ||
            !/[0-9]/.test(passVal)
        ) {
            showError(pass, "Password must be 8+ chars, contain a number and uppercase.");
            ok = false;
        }

        if (confirmVal !== passVal) {
            showError(confirm, "Passwords do not match.");
            ok = false;
        }

        return ok;
    }

    [pass, confirm].forEach(input => {
        input.addEventListener("input", () => validateField(input));
        input.addEventListener("blur", () => validateField(input));
    });

    form.addEventListener("submit", e => {
        e.preventDefault();

        if (!validateBeforeSubmit()) return;

        fetch("<?= $baseUrl ?>app/auth/reset_password_process.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body:
                "token=" + encodeURIComponent(tokenInput.value.trim()) +
                "&password=" + encodeURIComponent(pass.value.trim()) +
                "&passwordConfirm=" + encodeURIComponent(confirm.value.trim())
        })
        .then(res => res.json())
        .then(data => {

        resultBox.classList.remove("hidden", "success", "error");
        
        box.scrollIntoView({ behavior: "smooth", block: "center" });

        if (data.success) {
            window.location.href = "<?= $baseUrl ?>login";
            return;
        } else {
                resultBox.classList.add("error");

                resultIcon.innerHTML = "❌";
                resultMsg.textContent = data.message || "Failed to reset password.";
                resultLink.style.display = "none";
            }

            resultBox.classList.remove("hidden");
        })
        .catch(() => {
            resultBox.classList.remove("hidden", "success", "error");
            resultBox.classList.add("error");
            
            box.scrollIntoView({ behavior: "smooth", block: "center" });

            resultIcon.innerHTML = "❌";
            resultMsg.textContent = "Server error.";
            resultLink.style.display = "none";

            resultBox.classList.remove("hidden");
        });
    });
});
</script>
