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
        <h1>Reset Password</h1>
        <form id="resetPasswordForm" novalidate>
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
        <div id="resultMessage" class="result-box hidden"><p></p></div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("resetPasswordForm");
    const tokenInput = document.getElementById("token");
    const passwordInput = document.getElementById("password");
    const passwordConfirmInput = document.getElementById("passwordConfirm");
    const resultBox = document.getElementById("resultMessage");
    const resultMsg = resultBox.querySelector("p");

    function validatePasswords() {
        const password = passwordInput.value.trim();
        const confirm = passwordConfirmInput.value.trim();
        if (password.length < 8) {
            passwordInput.nextElementSibling.textContent = "Password must be at least 8 characters";
            return false;
        } else {
            passwordInput.nextElementSibling.textContent = "";
        }
        if (password !== confirm) {
            passwordConfirmInput.nextElementSibling.textContent = "Passwords do not match";
            return false;
        } else {
            passwordConfirmInput.nextElementSibling.textContent = "";
        }
        return true;
    }

    form.addEventListener("submit", e => {
        e.preventDefault();
        if (!validatePasswords()) return;

        fetch("<?= $baseUrl ?>/app/auth/reset_password_process.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "token=" + encodeURIComponent(tokenInput.value.trim()) +
                  "&password=" + encodeURIComponent(passwordInput.value.trim()) +
                  "&passwordConfirm=" + encodeURIComponent(passwordConfirmInput.value.trim())
        })
        .then(res => res.json())
        .then(data => {
            resultBox.classList.remove("hidden");
            if (data.success) {
                resultBox.classList.add("success");
                resultMsg.textContent = "Password has been reset successfully.";
                form.style.display = "none";
            } else {
                resultBox.classList.add("error");
                resultMsg.textContent = data.message || "Failed to reset password.";
            }
        })
        .catch(err => {
            console.error(err);
            resultBox.classList.remove("hidden");
            resultBox.classList.add("error");
            resultMsg.textContent = "Server error.";
        });
    });
});
</script>
