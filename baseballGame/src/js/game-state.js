function drawResultText() {
  if (resultText) {
    ctx.font = 'bold 38px sans-serif';
    ctx.textAlign = 'center';

    ctx.lineWidth = 3;
    ctx.strokeStyle = 'black';
    ctx.strokeText(resultText, centerX, homePlateY - 250);

    ctx.fillStyle = resultTextColor;
    ctx.fillText(resultText, centerX, homePlateY - 250);
  }
}

function showResultText(text, color = 'yellow', duration = 1500) {
  resultText = text;
  resultTextColor = color;

  clearTimeout(resultTextTimeout);
  resultTextTimeout = setTimeout(() => {
    resultText = '';
  }, duration);
}

function addOut() {
  if (gameOver) return;

  outs++;

  if (outs >= 3) {
    setTimeout(() => {
      if (gameState === 'offense') {
        showGameOver();
      } else {
        switchSides();
      }
    }, 800);
  }
}

function clearStateTransTimers() {
  clearTimeout(_stateTransTimers.hold);
  clearTimeout(_stateTransTimers.out);
  clearTimeout(_stateTransTimers.reset);
  _stateTransTimers.hold = _stateTransTimers.out = _stateTransTimers.reset = null;
}

function showStateTransition(text, holdMs = 1200) {
  const overlay = document.getElementById('stateTransitionOverlay');
  const textEl = document.getElementById('stateTransitionText');
  if (!overlay || !textEl) return;

  clearStateTransTimers();
  overlay.classList.remove('in', 'out');

  void overlay.offsetWidth;

  textEl.textContent = text;

  overlay.classList.add('in');

  _stateTransTimers.hold = setTimeout(() => {
    overlay.classList.remove('in');
    overlay.classList.add('out');

    _stateTransTimers.out = setTimeout(() => {
      overlay.classList.remove('out');
      overlay.style.transform = 'translate(120vw, 120vh)';
      overlay.style.opacity = '0';
      _stateTransTimers.reset = setTimeout(() => {
        overlay.style.transform = '';
        overlay.style.opacity = '';
      }, 20);
    }, 600);
  }, holdMs + 300);
}

function resetOverlay() {
  const overlay = document.getElementById('stateTransitionOverlay');
  const textEl = document.getElementById('stateTransitionText');
  const promptEl = document.getElementById('gameOverPrompt');

  if (!overlay || !textEl || !promptEl) return;

  overlay.classList.remove('in', 'out');
  overlay.style.opacity = '0';
  overlay.style.transform = 'translate(120vw, 120vh)';
  promptEl.style.display = 'none';
  textEl.textContent = '';
}

function showGameOver() {
  if (gameOver) return;
  gameOver = true; 

  if (!matchInProgress || matchFinished) return;

  matchFinished = true;
  matchInProgress = false;

  function getMatchResult() {
    if (teamBScore > teamAScore) return "WIN";
    if (teamBScore < teamAScore) return "LOSE";
    return "TIED";
  }

  console.log("MATCH FINISHED – VALID");

  const result = getMatchResult();
  const token = localStorage.getItem("app_token");
  if (!token) return;

  fetch("https://xeon.spskladno.cz/~kuceraf/2DBaseball/baseballWeb/api/app_add_match.php", {
      method: "POST",
      headers: {
          "Content-Type": "application/json",
          "X-App-Token": token
      }
  });

  fetch("https://xeon.spskladno.cz/~kuceraf/2DBaseball/baseballWeb/api/app_add_match_result.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-App-Token": token
    },
      body: JSON.stringify({
        result: result,
        team_a_score: teamAScore,
        team_b_score: teamBScore
      })
  });

  const overlay = document.getElementById('stateTransitionOverlay');
  const textEl = document.getElementById('stateTransitionText');
  const promptEl = document.getElementById('gameOverPrompt');

  if (!overlay || !textEl || !promptEl) return;

  clearStateTransTimers();
  overlay.classList.remove('in', 'out');

  void overlay.offsetWidth;

  textEl.textContent = "GAME OVER";
  overlay.style.opacity = "1";
  overlay.style.transform = "translate(0,0)";
  overlay.classList.add('in');

  promptEl.style.display = "block";
  let visible = true;
  const blinkInterval = setInterval(() => {
    if (!gameOver) { clearInterval(blinkInterval); return; }
    visible = !visible;
    promptEl.style.visibility = visible ? 'visible' : 'hidden';
  }, 500);

  function goToMenuListener(e) {
    if (e.key === 'Enter') {
      gameOver = false;
      promptEl.style.display = "none";
      overlay.classList.remove('in');

      if (gameWrapper && startScreen) {
        resetOverlay();
        gameWrapper.style.display = 'none';
        startScreen.style.display = 'flex';
      }

      window.removeEventListener('keydown', goToMenuListener);
      clearInterval(blinkInterval);
    }
  }

  window.addEventListener('keydown', goToMenuListener);
}

function switchSides() {
  const nextState = gameState === 'defense' ? 'offense' : 'defense';

  if (nextState === 'offense') {
    showStateTransition("NOW YOU GO HIT!");
  } else {
    showStateTransition("NOW YOU GO PITCH!");
  }

  outs = 0;
  runnerOnFirstBase = null;
  runnerOnSecondBase = null;
  runnerOnThirdBase = null;
  bases = [null, null, null];
  gameState = nextState;
  aiBattingEnabled = gameState === 'defense';

  resetCount();
  draw();

  if (typeof updateHitZoneButton === 'function') {
    updateHitZoneButton();
  }
}

if (toggleStateBtn) {
  toggleStateBtn.addEventListener('click', () => {
    switchSides();
  });
}

function resetGame() {
  runnerOnFirstBase = null;
  runnerOnSecondBase = null;
  runnerOnThirdBase = null;
  bases = [null, null, null];

  dugoutRunners = [];
  batterRunningToDugout = false;
  batterRunObj = null;

  ballCountInProgress = false;
  strikeCount = 0;
  ballCount = 0;
  outs = 0;

  gameState = 'defense';
  aiBattingEnabled = true;
  gameOver = false;
  lastPlayType = null;

  hitRegistered = false;
  swingAllowed = false;
  swingActive = false;
  strikeoutInProgress = false;
  atBatOver = false;
  showHitZone = true;
  hitZone = null;

  teamBScore = 0;
  teamAScore = 0;
  runScoredText = '';
  clearTimeout(runScoredTimeout);

  selectedPitch = 'FB';
  lastPitch = null;

  slider.active = false;
  slider.stopped = false;
  slider.result = null;

  pitchTypeContainer.style.display = 'block';

  document.querySelectorAll('.pitchTypeBtn').forEach(b =>
    b.classList.remove('activePitch')
  );
  document.querySelector('.pitchTypeBtn[data-pitch="FB"]')
    ?.classList.add('activePitch');

  resultText = '';
  resultTextColor = 'black';
  clearTimeout(resultTextTimeout);

  resetOverlay();

  battersQueue = [
    { name: 'Turner', img: palkarImg },
    { name: 'Betts', img: palkarImg },
    { name: 'Ohtani', img: palkarImg },
    { name: 'Guerrero Jr.', img: palkarImg },
    { name: 'Trout', img: palkarImg },
    { name: 'Judge', img: palkarImg },
    { name: 'Rodriguez', img: palkarImg },
    { name: 'Jeter', img: palkarImg },
    { name: 'Acuña Jr.', img: palkarImg }
  ];
  currentOnDeckBatter = battersQueue[1];

  draw();
  resetCount();
}

