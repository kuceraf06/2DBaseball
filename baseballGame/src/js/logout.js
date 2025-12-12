function logoutUser() {
    console.log("LOGOUT: Removing token");
    localStorage.removeItem("app_token");
    localStorage.removeItem("remember_me");
    localStorage.removeItem("token_expire");
    window.location.href = "login.html";
}

document.addEventListener("DOMContentLoaded", () => {
    const logoutButtons = [
        document.getElementById("logoutBtn"),
        document.getElementById("startLogoutBtn"),
        document.getElementById("confirmLogoutBtn")
    ];

    logoutButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener("click", logoutUser);
        }
    });
});