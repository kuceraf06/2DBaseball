document.getElementById("loginBtn").addEventListener("click", login);

async function login() {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const errorBox = document.getElementById("error");

    if (!username || !password) {
        errorBox.textContent = "Vyplň všechna pole.";
        return;
    }

    errorBox.textContent = "";

    try {
        const res = await fetch("http://localhost/PHP/2Dbaseball/baseballWeb/api/app_login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                login: username,
                password: password
            })
        });

        if (!res.ok) {
            errorBox.textContent = "Špatné přihlášení.";
            return;
        }

        const data = await res.json();

        localStorage.setItem("app_token", data.token);

        window.location.href = "index.html";

    } catch (e) {
        console.error(e);
        errorBox.textContent = "Chyba serveru.";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.api && window.api.openExternal) {
                window.api.openExternal(link.href);
            } else {
                console.error("Electron API není dostupné!");
            }
        });
    });
});
