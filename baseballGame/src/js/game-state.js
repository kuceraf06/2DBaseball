let runnerOnFirstBase = null;
let ballCountInProgress = false;
let runnerOnSecondBase = null;
let runnerOnThirdBase = null;
let bases = [null, null, null];

let hideOnDeckDuringAnimation = false;
let currentOnDeckBatter = battersQueue[1];

let teamBScore = 0;
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
  outs++;

  if (outs >= 3) {
    setTimeout(() => {
      switchSides();
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

