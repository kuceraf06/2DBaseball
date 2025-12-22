<?php
$pageTitle = "2D Baseball | Leaderboard";
$pageDescription = "Top players ranked by total games played.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/leaderboard.css?v=' . time() . '">';

require_once __DIR__ . '/../db/connect.php';
?>

<main class="main-content leaderboard-page">

    <section class="leaderboard-hero">
        <h1>Players <span>Leaderboard</span></h1>
        <p>The top players ranked by total games played.</p>
    </section>

    <section class="leaderboard-summary">
        <div class="summary-card">
            <h3>Total Players</h3>
            <p class="summary-number">
                <?php
                $stmt = $db->query("SELECT COUNT(*) as total FROM users"); // tabulka s hráči
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo $row['total'];
                ?>
            </p>
        </div>
    </section>

    <section class="leaderboard-table-section">
        <h2>Top Players</h2>

        <div class="leaderboard-table">

            <div class="table-header">
                <div class="col rank">#</div>
                <div class="col username">Player</div>

                <div class="col games sortable" id="sort-games">
                    Games Played 
                    <i class='bx bxs-chevron-down sort-icon'></i>
                </div>
            </div>

            <?php
            $stmt = $db->prepare("
                SELECT 
                    users.username,
                    user_stats.matches_played
                FROM user_stats
                JOIN users ON users.id = user_stats.user_id
                ORDER BY user_stats.matches_played DESC
            ");
            $stmt->execute();

            $rank = 1;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                    <div class="table-row">
                        <div class="col rank">' . $rank . '</div>
                        <div class="col username">' . htmlspecialchars($row['username']) . '</div>
                        <div class="col games">' . (int)$row['matches_played'] . '</div>
                    </div>
                ';
                $rank++;
            }
            ?>

        </div>

        <button id="showMoreBtn" class="show-more-btn">Show more</button>

    </section>

</main>

<div id="gameHistoryModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2 id="modalPlayerName"></h2>

        <div id="modalHistoryList">
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const header = document.getElementById("sort-games");
    const icon = header.querySelector(".sort-icon");

    const table = document.querySelector(".leaderboard-table");
    let allRows = Array.from(document.querySelectorAll(".table-row"));

    let asc = false;
    let visibleCount = 5;

    function renderRows() {
        allRows.forEach((row, i) => {
            row.style.display = i < visibleCount ? "grid" : "none";
        });

        const btn = document.getElementById("showMoreBtn");
        btn.style.display = visibleCount >= allRows.length ? "none" : "block";
    }

    function updateRanks() {
        const total = allRows.length;

        allRows.forEach((row, i) => {
            const rankNum = asc ? (total - i) : (i + 1);
            row.querySelector(".rank").textContent = rankNum;
        });
    }

    renderRows();
    updateRanks();

    document.getElementById("showMoreBtn").addEventListener("click", () => {
        visibleCount += 5;
        renderRows();
    });

    header.addEventListener("click", () => {
        asc = !asc;

        allRows.sort((a, b) => {
            const numA = parseInt(a.querySelector(".games").textContent.trim());
            const numB = parseInt(b.querySelector(".games").textContent.trim());
            return asc ? numA - numB : numB - numA;
        });

        allRows.forEach(row => table.appendChild(row));

        updateRanks();
        renderRows();

        icon.classList.toggle("rotated", asc);
    });

    const modal = document.getElementById("gameHistoryModal");
    const closeModal = document.querySelector(".close-modal");
    const modalName = document.getElementById("modalPlayerName");
    const modalList = document.getElementById("modalHistoryList");

    const fakeHistory = {
        "Filip": ["Win 5-2", "Loss 1-3", "Win 7-6", "Win 5-2", "Loss 1-3", "Win 7-6", "Win 5-2", "Loss 1-3", "Win 7-6"],
        "Martin": ["Loss 0-9", "Win 3-1"],
        "Sarah": ["Win 4-0", "Win 6-2", "Loss 2-5"],
        "Player123": ["Win 1-0"],
        "John": ["Loss 0-3"],
        "Mike": ["Win 8-4"],
        "Lucas": ["Loss 2-7"],
        "Anna": ["Win 3-2"],
        "David": ["Loss 1-8"],
        "Tom": ["Win 2-1"]
    };

    document.querySelectorAll(".table-row .username").forEach(cell => {
        cell.style.cursor = "pointer";
        cell.addEventListener("click", () => {

            const playerName = cell.textContent.trim();
            modalName.textContent = playerName;

            modalList.innerHTML = "";

            const games = fakeHistory[playerName] || ["No match history"];

            games.forEach(entry => {
                const div = document.createElement("div");
                div.textContent = entry;
                modalList.appendChild(div);
            });

            modal.style.display = "flex";
        });
    });

    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", e => {
        if (e.target === modal) modal.style.display = "none";
    });
});
</script>
