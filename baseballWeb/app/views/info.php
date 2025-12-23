<?php
$pageTitle = "2D Baseball | Info";
$pageDescription = "Updates and known issues in the game.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/info.css?v=' . time() . '">';
?>

<main class="main-content info-page">

    <section class="info-hero">
        <h1>Game <span>Info</span></h1>
        <p>Stay updated with the latest changes, updates, and known issues.</p>
    </section>

    <section class="updates-section">
        <div class="updates-grid" id="updatesGrid">
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>The ready button can be pressed before the pitch is ready, which means that the pitch will not take place, but the AI can steal the base.</p>
            </div>
            <div class="update-card">
                <h3>[Update 17.12.2025]</h3>
                <p>Fixing the disappearance of runners who steal second base on a pitch.</p>
            </div>
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>A steal and a pickoff at the same time in an offense cause the AI to throw a pickoff at the base the runner is stealing.</p>
            </div>
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>When the AI bot hits a single and then steals second base, it teleports to second base and immediately steals third base.</p>
            </div>
            <div class="update-card">
                <h3>[Update 20.11.2025]</h3>
                <p>New graphics when switching between defense and offense.</p>
            </div>
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>When the bat and ball make contact, instead of the name of the batter who hits the ball, the name of the batter on deck is displayed.</p>
            </div>
            <div class="update-card">
                <h3>[Update 16.11.2025]</h3>
                <p>Added new leaderboard sorting by total wins.</p>
            </div>
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>Players are visible twice during steals and ballfours.</p>
            </div>
            <div class="update-card">
                <h3>[Update 10.11.2025]</h3>
                <p>New icon for 2D Baseball on the desktop.</p>
            </div>
            <div class="update-card">
                <h3>[Bug]</h3>
                <p>When changing the settings and pressing the SAVE button, and then making another change and pressing the cross button, the settings will not be saved, nor will the previous settings that should have been saved via SAVE.</p>
            </div>
            <div>
                <button id="showMoreBtn" class="show-more-btn">Show More</button>
            </div>
        </div>
    </section>

    <section class="report-section">
        <div class="report-card">
            <h2>Found a bug or issue?</h2>
            <p>If you notice any bugs, glitches, or issues not listed above, please <a href="<?= $baseUrl ?>support">contact support</a> and let us know. Your feedback helps us improve the game!</p>
        </div>
    </section>

</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const updates = document.querySelectorAll("#updatesGrid .update-card");
    const showMoreBtn = document.getElementById("showMoreBtn");
    const batchSize = 5;
    let visibleCount = 0;

    function showNextBatch() {
        const nextCount = visibleCount + batchSize;
        for (let i = visibleCount; i < nextCount && i < updates.length; i++) {
            updates[i].style.display = "block";
        }
        visibleCount += batchSize;
        if (visibleCount >= updates.length) {
            showMoreBtn.style.display = "none";
        }
    }

    updates.forEach((card, index) => {
        card.style.display = index < batchSize ? "block" : "none";
    });
    visibleCount = batchSize;

    if (updates.length <= batchSize) {
        showMoreBtn.style.display = "none";
    }

    showMoreBtn.addEventListener("click", showNextBatch);
});
</script>
