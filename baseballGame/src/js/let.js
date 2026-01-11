let runnerOnFirstBase = null;
let ballCountInProgress = false;
let runnerOnSecondBase = null;
let runnerOnThirdBase = null;
let bases = [null, null, null];

let battersQueue = [
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

let animationInProgress = false;

let hideOnDeckDuringAnimation = false;
let currentOnDeckBatter = battersQueue[1];

let teamBScore = 0;
let teamAScore = 0;
let runScoredText = '';
let runScoredTimeout = null;
let hideBatterDuringOnDeckAnimation = false;
let runnersInMotion = false;
let runnersInStealing = false;
let stealAttempt2B = false;
let stealAttempt3B = false;

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

let matchInProgress = false;
let matchFinished = false;

let gameOver = false;

let clickedStop = false;
let clickedSwing = false;
let canSwingEffect = true;
let hoverStop = false;
let hoverSwing = false;
let clickedBase = null;
let controllerInitialized = false;
let hasThrownDuringSteal = false;

let pickoffAttempt1B = false;
let pickoffAttempt2B = false;
let pickoffAttempt3B = false;

let aiSwingPlanned = false;

let aiStealEnabled = true;
let canStealThisPitch = false;
let isNewPitch = false;

let swingAnimation = {
  active: false,
  progress: 0,
  speed: 10,
  maxAngle: Math.PI/2,
  lastTime: 0
};

let draggingPlayer = null;
let dragOffsetX = 0;
let dragOffsetY = 0;

let globalLastTime = performance.now();

let lastSliderTime = null;

let stopPitchKey = 'Space';
let swingKey = 'Space';
let throwTo1BKey = '1';
let throwTo2BKey = '2';
let throwTo3BKey = '3';
let settingsToggleKey = 'Escape';
let globalVolume = 1.0;
let isMuted = false;
