if (window.api && typeof window.api.getUsers === "function") {
    window.api.getUsers().then(users => {
        if (users.length > 0) {
            document.getElementById("userName").innerText = users[0].username;
            document.getElementById("userID").innerText = users[0].id;
        }
    });
}

playBtn.addEventListener('click', () => {
  startScreen.style.display = 'none';
  gameWrapper.style.display = 'block';

  resetGame();

  matchInProgress = true;
  matchFinished = false;

  if (gameState === 'offense') {
    showStateTransition("NOW YOU GO HIT!");
  } else {
    showStateTransition("NOW YOU GO PITCH!");
  }

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

// Window controls are handled in user-settings.js to avoid duplicate handlers

document.getElementById('exitAppBtn').addEventListener('click', () => {
  window.api.quitApp();
});

if (startExitBtn) {
  startExitBtn.addEventListener('click', () => {
    window.api.quitApp();
  });
}

if (windowModeSelect) {
  windowModeSelect.addEventListener('change', (e) => {
    window.api.setWindowMode(e.target.value);
  });

  window.api.getWindowMode().then(mode => {
    windowModeSelect.value = mode;
  });
}