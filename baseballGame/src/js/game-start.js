if (window.api && typeof window.api.getUsers === "function") {
    window.api.getUsers().then(users => {
        if (users.length > 0) {
            document.getElementById("userName").innerText = users[0].username;
            document.getElementById("userID").innerText = users[0].id;
        }
    });
}

const startScreen = document.getElementById('startScreen');
const gameWrapper = document.querySelector('.gameWrapper');

const playBtn = startScreen.querySelector('#playBtn');
const aboutBtn = startScreen.querySelector('a > .startBtn');
const startLogoutBtn = document.getElementById('startLogoutBtn');
const logoutModal = document.getElementById('confirmLogoutModal');
const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

playBtn.addEventListener('click', () => {
  startScreen.style.display = 'none';
  gameWrapper.style.display = 'block';

  resetGame();

  matchInProgress = true;
  matchFinished = false;

  console.log("MATCH STARTED");
});

startLogoutBtn.addEventListener('click', () => {
  logoutModal.style.display = 'flex';
});

cancelLogoutBtn.addEventListener('click', () => {
  logoutModal.style.display = 'none';
});

confirmLogoutBtn.addEventListener('click', () => {
  logoutModal.style.display = 'none';
  gameWrapper.style.display = 'none';
  startScreen.style.display = 'flex';
});

document.getElementById('minimizeBtn').addEventListener('click', () => {
  window.api.minimize();
});

document.getElementById('desktopBtn').addEventListener('click', () => {
  window.api.toggleFullscreen();
});

document.getElementById('closeBtn').addEventListener('click', () => {
  window.api.close();
});

document.getElementById('exitAppBtn').addEventListener('click', () => {
  window.api.quitApp();
});

const startExitBtn = startScreen.querySelector('.exitBtn');
if (startExitBtn) {
  startExitBtn.addEventListener('click', () => {
    window.api.quitApp();
  });
}

const windowModeSelect = document.getElementById('windowModeSelect');
if (windowModeSelect) {
  windowModeSelect.addEventListener('change', (e) => {
    window.api.setWindowMode(e.target.value);
  });

  window.api.getWindowMode().then(mode => {
    windowModeSelect.value = mode;
  });
}