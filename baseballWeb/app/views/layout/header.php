<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        require __DIR__ . '/../db/connect.php';

        $token = $_COOKIE['remember_me'];

        $stmt = $db->prepare("
            SELECT id, username FROM users 
            WHERE remember_token = :token
            AND remember_expires > NOW()
        ");
        $stmt->execute([":token" => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        }
    }
}
?>
<header class="main-header">
    <div class="container">
        <a href="<?= $baseUrl ?>" class="logo">
            <img src="<?= $baseUrl ?>public/images/header/logo.png" alt="Logo">
        </a>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="about">About</a></li>
                <li><a href="how-to-play">How&nbsp;to&nbsp;play</a></li>
                <li><a href="rules">Rules</a></li>
                <li><a href="leaderboard">Leaderboard</a></li>
                <li><a href="support">Support</a></li>
                <li><a href="info">Info</a></li>
            </ul>
        </nav>

        <div class="header-right">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login" class="btn-signIn">Sign&nbsp;In</a>

            <?php else: ?>
                <div class="user-menu">
                    <button class="user-btn">
                        <i class='bx bxs-user'></i>
                    </button>
                    <div class="user-dropdown">
                        <a href="<?= $baseUrl ?>account">Account</a>
                        <a href="<?= $baseUrl ?>logout"  id="logoutLink">Logout <i class='bx bx-log-out'></i></a>
                    </div>
                </div>
            <?php endif; ?>
            <a href="download.php" class="btn-download">Download</a>
        </div>
        
        <button class="hamburger" id="hamburgerBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<script>
    const hamburger = document.getElementById("hamburgerBtn");
    const nav = document.getElementById("mainNav");
    const body = document.body;
    const html = document.documentElement;
    const logoutLink = document.getElementById("logoutLink");

    hamburger.addEventListener("click", () => {
        const isActive = nav.classList.toggle("active");
        hamburger.classList.toggle("active");

        if (isActive) {
            html.classList.add("no-scroll");
            body.classList.add("no-scroll");
            window.scrollTo(0, 0);
        } else {
            html.classList.remove("no-scroll");
            body.classList.remove("no-scroll");
        }
    });

    const userMenu = document.querySelector(".user-menu");
    if (userMenu) {
        userMenu.querySelector(".user-btn").addEventListener("click", () => {
            userMenu.classList.toggle("open");
        });

        document.addEventListener("click", e => {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove("open");
            }
        });
    }

    if (logoutLink) {
        logoutLink.addEventListener("click", function(e) {
            const confirmLogout = confirm("Are you sure you want to log out?");
            if (!confirmLogout) {
                e.preventDefault();
            }
        });
    }

</script>

