document.getElementById("loginBtn").addEventListener("click", login);

async function login() {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!username || !password) {
        return;
    }

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
            return;
        }

        const data = await res.json();
        
        console.log("Sending token:", data.token);

        localStorage.setItem("app_token", data.token);

        window.api.loadIndex?.();

    } catch (e) {
        console.error(e);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("minimizeBtn").addEventListener("click", () => {
        window.api.minimize();
    });

    document.getElementById("desktopBtn").addEventListener("click", () => {
        window.api.toggleFullscreen();
    });

    document.getElementById("closeBtn").addEventListener("click", () => {
        window.api.close();
    });

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
