const canvas = document.getElementById('field');
const ctx = canvas.getContext('2d');
const throwButton = document.getElementById('throwButton');

const centerX = canvas.width / 2;
const homePlateY = canvas.height - 50;
const baseDistance = 160;
const playerSize = 25;

const catcherImg = new Image();
const nadhazovacImg = new Image();
const palkarImg = new Image();
const polarImg = new Image();
const bezecImg = new Image();
const ballImg = new Image();
const batterDugoutImg = new Image();
const benchPlayerImg = new Image();
const benchPlayerBImg = new Image();
const slideImg = new Image();
const actionImg = new Image();
const logoImg = new Image();

catcherImg.src = 'images/catcher.png';
nadhazovacImg.src = 'images/nadhazovac.png';
palkarImg.src = 'images/palkar.png';
polarImg.src = 'images/polar.png';
bezecImg.src = 'images/bezec.png';
ballImg.src = 'images/baseball.png';
benchPlayerImg.src = 'images/benchPlayer.png';
benchPlayerBImg.src = 'images/benchPlayerB.png';
slideImg.src = 'images/slide.png';
actionImg.src = 'images/akce.png';
logoImg.src = 'images/logo.png';
batterDugoutImg.src = 'images/batterDugout.png';

const throwSound = new Audio('audio/throw.mp3');
const slideSound = new Audio('audio/slide.wav');
slideSound.volume = 0.5;
const walkSound = new Audio('audio/running.mp3');
walkSound.loop = true;
walkSound.playbackRate = 0.9;
const runningSound = new Audio('audio/running.mp3');
runningSound.loop = true;
runningSound.playbackRate = 1.5;
const hitSound = new Audio('audio/hit.mp3');
hitSound.playbackRate = 2;
const swingSound = new Audio('audio/swing.mp3');
swingSound.playbackRate = 6;
const strikeSound = new Audio('audio/strike.mp3');
const ballSound = new Audio('audio/ball.mp3');
const swingAndMissSound = new Audio('audio/swingandmiss.mp3');
const ballfourSound = new Audio('audio/ballfour.mp3');
const strikeoutSound = new Audio('audio/strikeout.mp3');
const safeSound = new Audio('audio/safe.mp3');
const outSound = new Audio('audio/out.mp3');
const singleSound = new Audio('audio/single.mp3');
const doubleSound = new Audio('audio/double.mp3');
const tripleSound = new Audio('audio/triple.mp3');
const homerunSound = new Audio('audio/homerun.mp3');

const allSounds = [
  throwSound,
  slideSound,
  walkSound,
  runningSound,
  hitSound,
  swingSound,
  ballSound,
  ballfourSound,
  strikeSound,
  strikeoutSound,
  safeSound,
  outSound,
  singleSound,
  doubleSound,
  tripleSound,
  homerunSound,
  swingAndMissSound
];

const AI_PITCH_STRIKE_CHANCE = 0.65;
const AI_PICKOFF_CHANCE_1B = 0.20;
const AI_PICKOFF_CHANCE_2B = 0.15;
const AI_PICKOFF_CHANCE_3B = 0.10;

const PITCH_SPEEDS = {
    'FB': 0.011,
    'CH': 0.005,
    'SL': 0.008
};

const AI_STEAL_CHANCE_DOUBLE = 10;
const AI_STEAL_CHANCE_2B = 30;
const AI_STEAL_CHANCE_3B = 20;

const POS = {
  FIRST: { x: centerX + baseDistance - playerSize / 2, y: homePlateY - baseDistance - playerSize / 2 },
  SECOND: { x: centerX - playerSize / 2, y: homePlateY - baseDistance * 2 - playerSize / 2 },
  THIRD: { x: centerX - baseDistance - playerSize / 2, y: homePlateY - baseDistance - playerSize / 2 },
  HOME: { x: centerX, y: homePlateY },
};

const startScreen = document.getElementById('startScreen');
const gameWrapper = document.querySelector('.gameWrapper');
const playBtn = startScreen.querySelector('#playBtn');
const aboutBtn = startScreen.querySelector('a > .startBtn');
const startLogoutBtn = document.getElementById('startLogoutBtn');
const logoutModal = document.getElementById('confirmLogoutModal');
const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
const startExitBtn = startScreen.querySelector('.exitBtn');
const windowModeSelect = document.getElementById('windowModeSelect');

const stateIndicatorEl = document.getElementById('stateIndicator');
const outsDisplayEl = document.getElementById('outsDisplay');
const toggleStateBtn = document.getElementById('toggleStateBtn');
const pitchTypeContainerEl = document.getElementById('pitchTypeContainer');
const pickoffButtons = document.querySelectorAll('.pickoffBtn');
const throwButtonEl = document.getElementById('throwButton');
const _stateTransTimers = { hold: null, out: null, reset: null };

const loginForm = document.getElementById("loginForm");

const confirmModal = document.getElementById("confirmLogoutModal");

const openLogoutBtns = [
    document.getElementById("logoutBtn"),
    document.getElementById("startLogoutBtn")
];

const confirmBtn = document.getElementById("confirmLogoutBtn");
const cancelBtn = document.getElementById("cancelLogoutBtn");

const pickoffBtn1B = document.getElementById("pickoffButton");
const pickoffBtn2B = document.getElementById("pickoffButton2B");
const pickoffBtn3B = document.getElementById("pickoffButton3B");

const pitchTypeContainer = document.getElementById('pitchTypeContainer');

const tutorialImages = [
    "images/tutorial/1-step.png",
    "images/tutorial/2-step.png",
    "images/tutorial/3-step.png",
    "images/tutorial/4-step.png",
    "images/tutorial/5-step.png",
    "images/tutorial/6-step.png",
    "images/tutorial/7-step.png",
    "images/tutorial/8-step.png",
    "images/tutorial/9-step.png",
    "images/tutorial/10-step.png"
];

const tutorialModal = document.getElementById("tutorialModal");
const tutorialImage = document.getElementById("tutorialImage");
const tutorialCounter = document.getElementById("tutorialCounter");

const openBtn = document.getElementById("tutorial");
const closeBtn = document.getElementById("closeTutorial");
const nextBtn = document.getElementById("tutorialNext");
const prevBtn = document.getElementById("tutorialPrev");


