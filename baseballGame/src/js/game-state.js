let runnerOnFirstBase = null;
let ballCountInProgress = false;
let runnerOnSecondBase = null;
let runnerOnThirdBase = null;
let bases = [null, null, null];

let hideOnDeckDuringAnimation = false;
let currentOnDeckBatter = battersQueue[1];

let teamBScore = 0;
let teamAScore = 0;
let runScoredText = '';
let runScoredTimeout = null;
let hideBatterDuringOnDeckAnimation = false;
let runnersInMotion = false;
let runnersInStealing = false;

let dugoutLeftPos = null;
let dugoutRightPos = null;

let dugoutRunners = [];

let batterRunningToDugout = false;
let batterRunObj = null;

let pickoffInProgress = false;
let preventReturnToPitcher = false;

let gameState = 'defense';
let aiBattingEnabled = (gameState === 'defense'); 
let outs = 0;
let strikeCount = 0;
let ballCount = 0;

let hitRegistered = false;
let swingAllowed = false;
let strikeoutInProgress = false;
let swingActive = false;
let atBatOver = false;
let showHitZone = true;
let hitZone = null;
let lastPlayType = null; 

let selectedPitch = 'FB';
let lastPitch = null;
const pitchNames = {
  'FB': 'Fastball',
  'SL': 'Slider',
  'CH': 'Changeup'
};

let resultText = '';
let resultTextColor = 'black';
let resultTextTimeout;

let resultEvaluated = false;

let gameOver = false;

const stateIndicatorEl = document.getElementById('stateIndicator');
const outsDisplayEl = document.getElementById('outsDisplay');
const toggleStateBtn = document.getElementById('toggleStateBtn');
const pitchTypeContainerEl = document.getElementById('pitchTypeContainer');
const pickoffButtons = document.querySelectorAll('.pickoffBtn');
const throwButtonEl = document.getElementById('throwButton');

function drawResultText() {
  if (resultText) {
    ctx.font = 'bold 38px sans-serif';
    ctx.fillStyle = resultTextColor;
    ctx.textAlign = 'center';
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

const _stateTransTimers = { hold: null, out: null, reset: null };

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
  gameOver = true;

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

