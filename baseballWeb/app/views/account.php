<?php
session_start();
require_once __DIR__ . '/../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$pageTitle = "2D Baseball | About";
$pageDescription = "Account page for 2D Baseball Simulator allows to manage account’s details.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/account.css?v=' . time() . '">';
?>
<main class="main-content account-page">
    <header class="account-hero">
        <h1 class="account-title">Account <span>Information<span></h1>
        <p class="account-subtitle">Manage your account’s details.</p>
    </header>

    <div class="account-content">

        <section class="account-section">

            <div class="account-group">
                <label class="account-label">User ID</label>
                <p><?= htmlspecialchars($user['id']) ?></p>
            </div>

            <div class="account-group">
                <label class="account-label">Username</label>

                <div class="account-row">
                    <input type="text" id="usernameInput" class="account-input" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    <button class="account-btn edit-btn" id="editUsernameBtn"><i class='bx bx-edit-alt'></i></button>
                    <button class="account-btn save-btn hidden" id="saveUsernameBtn"><i class='bx bxs-save'></i></button>
                    <button class="account-btn cancel-btn-small hidden" id="cancelUsernameBtn"><i class='bx bx-x'></i></button>
                </div>
            </div>

            <div class="account-group">
                <label class="account-label">Email</label>

                <div class="account-row">
                    <input type="email" id="emailInput" class="account-input" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    <button class="account-btn edit-btn" id="editEmailBtn"><i class='bx bx-edit-alt'></i></button>
                    <button class="account-btn save-btn hidden" id="saveEmailBtn"><i class='bx bxs-save'></i></button>
                    <button class="account-btn cancel-btn-small hidden" id="cancelEmailBtn"><i class='bx bx-x'></i></button>
                </div>
            </div>

            <div class="account-group">
                <label class="account-label">Password</label>
                <button class="account-btn action-btn" id="openPasswordModal">Change Password</button>
            </div>

            <div class="account-group">
                <label class="account-label">Danger Zone</label>
                <button class="account-btn delete-btn" id="openDeleteModal">Delete Account</button>
            </div>

        </section>

        <div class="modal-overlay" id="passwordModal">
            <div class="modal">
                <h3>Change Password</h3>

                <div class="password-row">
                    <div class="password-wrapper">
                        <input type="password" class="modal-input" id="currentPass" name="current" placeholder="Current Password">
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
                </div>

                <div class="password-row">
                    <div class="password-wrapper">
                        <input type="password" class="modal-input" id="newPass" name="new" placeholder="New Password">
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
                </div>

                <div class="password-row">
                    <div class="password-wrapper">
                        <input type="password" class="modal-input" id="confirmPass" name="confirm" placeholder="Confirm New Password">
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="modal-btn cancel-btn" id="closePasswordModal">Cancel</button>
                    <button class="modal-btn save-btn" id="savePasswordBtn" disabled>Save</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="deleteModal">
            <div class="modal delete-modal">
                <h3>Delete Account</h3>
                <p>This action cannot be undone.</p>

                <div class="password-row">
                    <div class="password-wrapper">
                        <input type="password" class="modal-input" id="deletePass" placeholder="Enter Your Password">
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
                </div>

                <div class="modal-actions">
                    <button class="modal-btn cancel-btn" id="closeDeleteModal">Cancel</button>
                    <button class="modal-btn delete-confirm-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const currentPass = document.getElementById('currentPass');
    const newPass = document.getElementById('newPass');
    const confirmPass = document.getElementById('confirmPass');
    const savePasswordBtn = document.getElementById('savePasswordBtn');

    document.querySelectorAll(".toggle-pass").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = btn.parentElement.querySelector("input");
            const state = btn.getAttribute("data-state");
            input.type = state === "closed" ? "text" : "password";
            btn.setAttribute("data-state", state === "closed" ? "open" : "closed");
        });
    });

    const passwordModal = document.getElementById('passwordModal');

    document.getElementById('openPasswordModal').onclick = () => {
        passwordModal.style.display = 'flex';
        savePasswordBtn.disabled = true;

        [currentPass, newPass, confirmPass].forEach(i => i.value = "");
        [currentPass, newPass, confirmPass].forEach(i => i.classList.remove("error"));
        document.querySelectorAll(".passError-msg").forEach(e => e.remove());
    };

    document.getElementById('closePasswordModal').onclick = () => {
        passwordModal.style.display = 'none';

        [currentPass, newPass, confirmPass].forEach(i => i.value = "");
        [currentPass, newPass, confirmPass].forEach(i => i.classList.remove("error"));
        document.querySelectorAll(".passError-msg").forEach(e => e.remove());

        savePasswordBtn.disabled = true;
    };

    document.getElementById('openDeleteModal').onclick = () =>
        document.getElementById('deleteModal').style.display = 'flex';
    document.getElementById('closeDeleteModal').onclick = () =>
        document.getElementById('deleteModal').style.display = 'none';

    document.querySelector('.delete-confirm-btn').addEventListener('click', () => {
        const pass = document.getElementById('deletePass').value.trim();
        if (!pass) return alert("Enter your password to confirm");

        fetch('<?= $baseUrl ?>app/auth/delete_account.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'password=' + encodeURIComponent(pass)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert("Your account has been deleted. Goodbye!");
                window.location.href = '<?= $baseUrl ?>';
            } else {
                alert(data.message || "Failed to delete account");
            }
        })
        .catch(err => alert("Error: " + err));
    });
    
    function clearPassErrors() {
        [currentPass, newPass, confirmPass].forEach(input => {
            input.classList.remove("error");
            let msg = input.closest(".password-row")?.querySelector(".error-msg");
            if(msg) { msg.textContent = ""; msg.style.opacity = 0; }
        });
    }

    function showPassError(input, msg) {
        input.classList.add("error");
        let row = input.closest(".password-row");
        let msgElem = row.querySelector(".passError-msg");
        if (!msgElem) {
            msgElem = document.createElement("small");
            msgElem.classList.add("passError-msg");
            row.appendChild(msgElem);
        }
        msgElem.textContent = msg;
        msgElem.style.opacity = 1;
    }

    function validatePasswordFields() {
        let valid = true;

        if (currentPass.value.trim() === "") {
            showPassError(currentPass, "Current password required.");
            valid = false;
        } else {
            removePassError(currentPass);
        }

        const val = newPass.value;
        if (val.length < 8 || !/[A-Z]/.test(val) || !/[0-9]/.test(val)) {
            showPassError(newPass, "Password must be 8+ chars, include number & uppercase.");
            valid = false;
        } else {
            removePassError(newPass);
        }

        if (confirmPass.value !== newPass.value) {
            showPassError(confirmPass, "Passwords do not match.");
            valid = false;
        } else {
            removePassError(confirmPass);
        }

        savePasswordBtn.disabled = !valid;

        return valid;
    }

    function removePassError(input) {
        input.classList.remove("error");
        const row = input.closest(".password-row");
        const msg = row?.querySelector(".passError-msg");
        if (msg) msg.remove();
    }

    [currentPass, newPass, confirmPass].forEach(input =>
        input.addEventListener('input', validatePasswordFields)
    );

    savePasswordBtn.addEventListener('click', () => {
        if (!validatePasswordFields()) return;

        fetch('<?= $baseUrl ?>app/auth/update_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:
                'current=' + encodeURIComponent(currentPass.value) +
                '&new=' + encodeURIComponent(newPass.value)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Password updated successfully.");
                currentPass.value = newPass.value = confirmPass.value = "";
                document.getElementById('passwordModal').style.display = 'none';
            } else {
                showPassError(currentPass, data.message || "Error updating password.");
            }
        })
        .catch(err => alert("Error: " + err));
    });

    const editBtn = document.getElementById('editUsernameBtn');
    const saveBtn = document.getElementById('saveUsernameBtn');
    const cancelUsernameBtn = document.getElementById('cancelUsernameBtn');
    const usernameInput = document.getElementById('usernameInput');

    let originalUsername = usernameInput.value;

    function clearFieldError(input) {
        input.classList.remove("error");
        let msg = input.closest(".account-group").querySelector(".error-msg");
        if(msg) { msg.textContent = ""; msg.style.opacity = 0; }
    }

    function showError(input, msg) {
        input.classList.add("error");
        let msgElem = input.closest(".account-group").querySelector(".error-msg");
        if(!msgElem) {
            msgElem = document.createElement("small");
            msgElem.classList.add("error-msg");
            input.closest(".account-group").appendChild(msgElem);
        }
        msgElem.textContent = msg;
        msgElem.style.opacity = 1;
    }

    function validateUsernameField() {
        clearFieldError(usernameInput);
        const val = usernameInput.value.trim();
        let valid = true;

        if(val.length < 3) {
            showError(usernameInput, "Username must be at least 3 characters.");
            valid = false;
        } else if(!/^[a-zA-Z0-9_]+$/.test(val)) {
            showError(usernameInput, "Username can only contain letters, numbers, and underscores.");
            valid = false;
        }

        if(valid) {
            saveBtn.disabled = false;
            saveBtn.classList.remove("disabled");
        } else {
            saveBtn.disabled = true;
            saveBtn.classList.add("disabled");
        }

        return valid;
    }

    editBtn.addEventListener('click', () => {
        usernameInput.disabled = false;
        usernameInput.focus();
        editBtn.classList.add('hidden');
        saveBtn.classList.remove('hidden');
        cancelUsernameBtn.classList.remove('hidden');
        originalUsername = usernameInput.value;
        validateUsernameField();
    });

    cancelUsernameBtn.addEventListener('click', () => {
        usernameInput.value = originalUsername;
        usernameInput.disabled = true;
        saveBtn.classList.add('hidden');
        editBtn.classList.remove('hidden');
        cancelUsernameBtn.classList.add('hidden');
        clearFieldError(usernameInput);
    });

    usernameInput.addEventListener('input', validateUsernameField);

    saveBtn.addEventListener('click', () => {
        if(!validateUsernameField()) return;
        const newUsername = usernameInput.value.trim();

        fetch('<?= $baseUrl ?>app/auth/update_username.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'username=' + encodeURIComponent(newUsername)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                usernameInput.value = data.username;
                usernameInput.disabled = true;
                saveBtn.classList.add('hidden');
                editBtn.classList.remove('hidden');
                alert('Username updated successfully');
            } else {
                showError(usernameInput, data.message || 'Error updating username');
            }
        })
        .catch(err => alert('Error: ' + err));
    });

    const editEmailBtn = document.getElementById('editEmailBtn');
    const saveEmailBtn = document.getElementById('saveEmailBtn');
    const cancelEmailBtn = document.getElementById('cancelEmailBtn');
    const emailInput = document.getElementById('emailInput');

    let originalEmail = emailInput.value;

    function validateEmailField() {
        clearFieldError(emailInput);
        const val = emailInput.value.trim();
        let valid = true;

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailPattern.test(val)) {
            showError(emailInput, "Invalid email format.");
            valid = false;
        }

        if(valid) {
            saveEmailBtn.disabled = false;
            saveEmailBtn.classList.remove("disabled");
        } else {
            saveEmailBtn.disabled = true;
            saveEmailBtn.classList.add("disabled");
        }

        return valid;
    }

    editEmailBtn.addEventListener('click', () => {
        emailInput.disabled = false;
        emailInput.focus();
        editEmailBtn.classList.add('hidden');
        saveEmailBtn.classList.remove('hidden');
        cancelEmailBtn.classList.remove('hidden');
        originalEmail = emailInput.value;
        validateEmailField();
    });

    cancelEmailBtn.addEventListener('click', () => {
        emailInput.value = originalEmail;
        emailInput.disabled = true;
        saveEmailBtn.classList.add('hidden');
        editEmailBtn.classList.remove('hidden');
        cancelEmailBtn.classList.add('hidden');
        clearFieldError(emailInput);
    });
        
    emailInput.addEventListener('input', validateEmailField);

    saveEmailBtn.addEventListener('click', () => {
        if(!validateEmailField()) return;

        const newEmail = emailInput.value.trim();

        fetch('<?= $baseUrl ?>app/auth/update_email.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'email=' + encodeURIComponent(newEmail)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                emailInput.value = data.email;
                emailInput.disabled = true;
                saveEmailBtn.classList.add('hidden');
                editEmailBtn.classList.remove('hidden');
                alert('Email updated successfully');
            } else {
                showError(emailInput, data.message || 'Error updating email');
                saveEmailBtn.disabled = true;
                saveEmailBtn.classList.add('disabled');
            }
        })
        .catch(err => alert('Error: ' + err));
    });
});
</script>