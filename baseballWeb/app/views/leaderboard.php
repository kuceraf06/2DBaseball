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
                    users.id as user_id,
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
                    <div class="table-row" data-player-id="' . (int)$row['user_id'] . '" data-player-name="' . htmlspecialchars($row['username']) . '">
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

    allRows.forEach(row => {
        const usernameCell = row.querySelector(".username");
        usernameCell.style.cursor = "pointer";
        usernameCell.addEventListener("click", async () => {
            const playerId = row.getAttribute("data-player-id");
            const playerName = row.getAttribute("data-player-name");
            modalName.textContent = playerName;
            modalList.innerHTML = "";

            try {
                const response = await fetch('https://xeon.spskladno.cz/~kuceraf/2DBaseball/baseballWeb/api/fetch_player_matches.php?user_id=' + playerId);
                const data = await response.json();

                if (data.length === 0) {
                    modalList.innerHTML = "<div>No match history</div>";
                } else {
                    data.forEach(match => {
                        const div = document.createElement("div");
                        div.textContent = match.result + " " + match.team_a_score + "-" + match.team_b_score;
                        modalList.appendChild(div);
                    });
                }

                modal.style.display = "flex";

            } catch (err) {
                console.error(err);
                modalList.innerHTML = "<div>Error loading match history</div>";
                modal.style.display = "flex";
            }
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
