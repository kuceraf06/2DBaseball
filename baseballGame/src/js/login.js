document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const submitBtn = document.getElementById("loginBtn");
    const resultBox = document.getElementById("login-result");
    const rememberCheckbox = document.getElementById("remember-me");

    const resultIcon = resultBox?.querySelector(".result-icon");
    const resultMsg  = resultBox?.querySelector(".result-message");

    autoLoginCheck();

    document.querySelectorAll(".toggle-pass").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = btn.parentElement.querySelector("input");
            input.type = btn.dataset.state === "closed" ? "text" : "password";
            btn.dataset.state = btn.dataset.state === "closed" ? "open" : "closed";
        });
    });

    [usernameInput, passwordInput].forEach(input => {
        input.addEventListener("input", () => {
            validateField(input);
            checkFormValidity();
        });
        input.addEventListener("blur", () => validateField(input));
    });

    function validateField(input) {
        clearFieldError(input);
        if (!input.value.trim()) showError(input, "Toto pole je povinné");
    }

    function checkFormValidity() {
        submitBtn.disabled = !usernameInput.value.trim() || !passwordInput.value.trim();
    }

    function showError(input, msg) {
        const group = input.closest(".form-group");
        if (!group) return;
        const errorMsg = group.querySelector(".error-msg");
        input.classList.add("error");
        if (errorMsg) {
            errorMsg.textContent = msg;
            errorMsg.style.opacity = 1;
        }
    }

    function clearFieldError(input) {
        const group = input.closest(".form-group");
        if (!group) return;
        const errorMsg = group.querySelector(".error-msg");
        input.classList.remove("error");
        if (errorMsg) {
            errorMsg.textContent = "";
            errorMsg.style.opacity = 0;
        }
    }

    function showResult(message, success = true) {
        if (!resultBox) return;
        resultBox.style.display = "block";
        resultBox.classList.remove("error", "success");
        resultBox.classList.add(success ? "success" : "error");
        if (resultIcon) resultIcon.textContent = success ? "✔️" : "❌";
        if (resultMsg) resultMsg.textContent = message;
    }

    function clearResultBox() {
        if (!resultBox) return;
        resultBox.style.display = "none";
        resultBox.classList.remove("error", "success");
        if (resultIcon) resultIcon.textContent = "";
        if (resultMsg) resultMsg.textContent = "";
    }

    loginForm.addEventListener("submit", e => {
        e.preventDefault();
        login();
    });

    async function login() {
        clearResultBox();
        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        validateField(usernameInput);
        validateField(passwordInput);

        if (!username || !password) return;

        try {
            const res = await fetch("http://localhost/PHP/2Dbaseball/baseballWeb/api/app_login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ login: username, password: password })
            });

            const data = await res.json();

            if (!res.ok || !data.token) {
                showResult(data.message || "Login failed.", false);
                return;
            }

            const token = data.token;

            if (rememberCheckbox.checked) {
                localStorage.setItem("app_token", token);
                localStorage.setItem("remember_me", "1");

                const expire = Date.now() + 30 * 24 * 60 * 60 * 1000;
                localStorage.setItem("token_expire", expire.toString());
            } else {
                localStorage.setItem("app_token", token);
                localStorage.removeItem("remember_me");
                localStorage.removeItem("token_expire");
            }

            window.api?.loadIndex?.();
            if (!window.api) window.location.href = "index.html";

        } catch (err) {
            console.error(err);
            showResult("Chyba serveru.", false);
        }
    }

    async function autoLoginCheck() {
        const sessionToken = sessionStorage.getItem("app_token");
        if (sessionToken) {
            try {
                const res = await fetch("http://localhost/PHP/2Dbaseball/baseballWeb/api/me.php", {
                    method: "GET",
                    headers: { "X-App-Token": sessionToken }
                });

                if (res.ok) {
                    window.api?.loadIndex?.();
                    if (!window.api) window.location.href = "index.html";
                    return;
                } else {
                    sessionStorage.removeItem("app_token");
                }
            } catch (err) {
                console.error("AutoLogin SESSION error:", err);
            }
        }

        const token = localStorage.getItem("app_token");
        const remember = localStorage.getItem("remember_me");
        const expire = localStorage.getItem("token_expire");

        if (!token || remember !== "1") return;

        if (!expire || Date.now() > Number(expire)) {
            localStorage.removeItem("app_token");
            localStorage.removeItem("remember_me");
            localStorage.removeItem("token_expire");
            return;
        }

        try {
            const res = await fetch("http://localhost/PHP/2Dbaseball/baseballWeb/api/me.php", {
                method: "GET",
                headers: { "X-App-Token": token }
            });

            if (res.ok) {
                window.api?.loadIndex?.();
                if (!window.api) window.location.href = "index.html";
            } else {
                localStorage.removeItem("app_token");
                localStorage.removeItem("remember_me");
                localStorage.removeItem("token_expire");
            }

        } catch (err) {
            console.error("AutoLogin REMEMBER error:", err);
        }
    }

    document.getElementById("minimizeBtn")?.addEventListener("click", () => window.api?.minimize());
    document.getElementById("desktopBtn")?.addEventListener("click", () => window.api?.toggleFullscreen());
    document.getElementById("closeBtn")?.addEventListener("click", () => window.api?.close());

    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            window.api?.openExternal?.(link.href);
        });
    });

    loginForm.addEventListener("keydown", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            if (!submitBtn.disabled) loginForm.dispatchEvent(new Event("submit"));
        }
    });
});
