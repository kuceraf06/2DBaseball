function logoutUser() {
    console.log("LOGOUT: Removing token");

    localStorage.removeItem("app_token");
    localStorage.removeItem("remember_me");
    localStorage.removeItem("token_expire");
    sessionStorage.removeItem("app_token");

    window.location.href = "login.html";
}

document.addEventListener("DOMContentLoaded", () => {
    openLogoutBtns.forEach(btn => {
        if (!btn) return;
        btn.addEventListener("click", () => {
            confirmModal.classList.add("active");
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            confirmModal.classList.remove("active");
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener("click", logoutUser);
    }
});
