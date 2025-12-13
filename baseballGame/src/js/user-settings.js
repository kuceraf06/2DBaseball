let menuOpen = false;
let settingsOpen = false;
let savedWindowModeBeforeModal = null;

let strikeZoneSwitch;
let saveSettingsBtn;
let saveCloseSettingsBtn;
let stopPitchKeyInput;
let stopPitchKey = 'Space';
let newStopPitchKey = null;

let swingKeyInput;
let swingKey = 'Space';
let newSwingKey = null;

let settingsToggleKeyInput;
let settingsToggleKey = 'Escape';
let newSettingsToggleKey = null;

let throwTo1BKeyInput;
let throwTo1BKey = '1';
let newThrowTo1BKey = null;

let throwTo2BKeyInput;
let throwTo2BKey = '2';
let newThrowTo2BKey = null;

let throwTo3BKeyInput;
let throwTo3BKey = '3';
let newThrowTo3BKey = null;

let unsavedChanges = false;

let volumeSlider;
let volumeIcon;
let globalVolume = 1.0;
let isMuted = false;

let lastVolume = globalVolume;
let savedVolumeBeforeModal;
let savedMutedBeforeModal;

let currentExitAction = null;

window.stopPitchKey = stopPitchKey;
window.swingKey = swingKey;
window.settingsToggleKey = settingsToggleKey;
window.throwTo1BKey = throwTo1BKey;
window.throwTo2BKey = throwTo2BKey;
window.throwTo3BKey = throwTo3BKey;

function normalizeKeyName(k) {
  if (k === undefined || k === null) return k;
  if (k === ' ' || k === 'Spacebar') return 'Space';
  if (k.toLowerCase && k.toLowerCase() === 'esc') return 'Escape';
  return k;
}

function setKeyVar(name, value) {
  const v = normalizeKeyName(value);
  switch (name) {
    case 'stopPitchKey':
      stopPitchKey = v;
      window.stopPitchKey = v;
      break;
    case 'swingKey':
      swingKey = v;
      window.swingKey = v;
      break;
    case 'settingsToggleKey':
      settingsToggleKey = v;
      window.settingsToggleKey = v;
      break;
    case 'throwTo1BKey':
      throwTo1BKey = v;
      window.throwTo1BKey = v;
      break;
    case 'throwTo2BKey':
      throwTo2BKey = v;
      window.throwTo2BKey = v;
      break;
    case 'throwTo3BKey':
      throwTo3BKey = v;
      window.throwTo3BKey = v;
      break;
    default:
      window[name] = v;
  }
}

(function loadSavedKeyBindings() {
  try {
    const mapping = {
      stopPitchKey,
      swingKey,
      settingsToggleKey,
      throwTo1BKey,
      throwTo2BKey,
      throwTo3BKey
    };

    ['stopPitchKey','swingKey','settingsToggleKey','throwTo1BKey','throwTo2BKey','throwTo3BKey'].forEach(k => {
      const saved = localStorage.getItem(k);
      const val = saved ? normalizeKeyName(saved) : normalizeKeyName(mapping[k]);
      setKeyVar(k, val);
    });

    const sv = localStorage.getItem('globalVolume');
    const sm = localStorage.getItem('isMuted');
    if (sv !== null) globalVolume = parseFloat(sv);
    if (sm !== null) isMuted = JSON.parse(sm);

    if (typeof applyVolumeSettings === 'function') applyVolumeSettings();

    try {
      const ev = new CustomEvent('controls-updated', {
        detail: {
          stopPitchKey, swingKey, throwTo1BKey, throwTo2BKey, throwTo3BKey, settingsToggleKey
        }
      });
      window.dispatchEvent(ev);
    } catch (err) {}
  } catch (err) {
  }
})();

function applyVolumeSettings() {
  if (typeof allSounds === 'undefined') return;
  allSounds.forEach(sound => {
    sound.volume = isMuted ? 0 : globalVolume;
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const menuLogo = document.querySelector('.menuLogo');
  const menuModal = document.getElementById('menuModal');
  const closeMenuBtn = document.getElementById('closeMenuBtn');
  const closeMenuCross = document.getElementById('closeMenuCross');
  const menuSettingsBtn = document.querySelector('#menuModal .menuBtn:nth-child(3)');
  const exitAppBtn = document.getElementById('exitAppBtn');
  const settingsLogo = document.querySelector('.settingsLogo');
  const settingsModal = document.getElementById('settingsModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const userLogo = document.querySelector('.accountLogo');
  const userModal = document.getElementById('userModal');
  const closeUserModalBtn = document.getElementById('closeUserModal');
  const logoutBtn = document.getElementById('logoutBtn');

  const backToMenuBtn = document.getElementById('backToMenuBtn');
  const resetSettingsBtn = document.getElementById('resetSettings');

  const confirmCloseModal = document.getElementById('confirmCloseModal');
  const cancelCloseBtn = document.getElementById('cancelCloseBtn');
  const confirmCloseBtn = document.getElementById('confirmCloseBtn');

  const confirmExitModal = document.getElementById('confirmExitModal');
  const cancelExitBtn = document.getElementById('cancelExitBtn');
  const confirmExitBtn = document.getElementById('confirmExitBtn');
  const confirmExitText = confirmExitModal.querySelector('p');

  strikeZoneSwitch = document.getElementById('strikeZoneSwitch');
  saveSettingsBtn = document.getElementById('saveSettingsBtn');
  saveCloseSettingsBtn = document.getElementById('saveCloseSettingsBtn');
  stopPitchKeyInput = document.getElementById('stopPitchKeyInput');
  swingKeyInput = document.getElementById('swingKeyInput');
  settingsToggleKeyInput = document.getElementById('settingsToggleKeyInput');

  throwTo1BKeyInput = document.getElementById('throwTo1BKeyInput');
  throwTo2BKeyInput = document.getElementById('throwTo2BKeyInput');
  throwTo3BKeyInput = document.getElementById('throwTo3BKeyInput');

  volumeSlider = document.getElementById('volumeSlider');
  volumeIcon = document.getElementById('volumeIcon');

  function showConfirmExitModal(message, onConfirm) {
    confirmExitText.innerHTML = message;
    currentExitAction = onConfirm;
    confirmExitModal.style.display = 'flex';
  }

  cancelExitBtn.onclick = () => {
    confirmExitModal.style.display = 'none';
    currentExitAction = null;
  };

  confirmExitBtn.onclick = () => {
    confirmExitModal.style.display = 'none';
    if (typeof currentExitAction === 'function') currentExitAction();
    currentExitAction = null;
  };

  function openMenu() {
    menuModal.style.display = 'flex';
    menuOpen = true;
  }

  function closeMenu() {
    menuModal.style.display = 'none';
    menuOpen = false;
  }

  menuModal.addEventListener('click', e => {
    if (e.target === menuModal) closeMenu();
  });
  
  function toggleMenu() {
    if (menuOpen) closeMenu();
    else openMenu();
  }

  if (menuLogo) menuLogo.addEventListener('click', openMenu);
  if (closeMenuBtn) closeMenuBtn.addEventListener('click', closeMenu);
  if (closeMenuCross) closeMenuCross.addEventListener('click', closeMenu);
  if (settingsLogo) {
    settingsLogo.addEventListener('click', () => {
      if (menuOpen) closeMenu();
      openSettings();
    });
  }

  if (backToMenuBtn) {
    backToMenuBtn.addEventListener('click', () => {
      settingsModal.style.display = 'none';
      settingsOpen = false;
      openMenu();
    });
  }

  if (menuSettingsBtn) {
    menuSettingsBtn.addEventListener('click', () => {
      closeMenu();
      openSettings();
    });
  }

  function openUserModal() {
    if (menuOpen) closeMenu();
    if (settingsOpen) closeSettings();
  
    userModal.style.display = 'flex';
  }
  
  function closeUserModal() {
    userModal.style.display = 'none';
  }
  
  if (userLogo) userLogo.addEventListener('click', openUserModal);
  if (closeUserModalBtn) closeUserModalBtn.addEventListener('click', closeUserModal);
  
  userModal.addEventListener('click', e => {
    if (e.target === userModal) closeUserModal();
  });
  
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      closeUserModal();
      showConfirmExitModal(
        'Do you really want to logout? <br>Game progress will be lost.',
        () => {
          logoutUser();

          if (window.api?.quitApp) {
            window.api.quitApp();
          } else {
            window.location.reload();
          }

        }
      );
    });
  }

  if (exitAppBtn) {
    exitAppBtn.addEventListener('click', () => {
      showConfirmExitModal(
        'The game is in progress. Exit now? <br>Game progress will be lost.',
        () => {
          if (window.api?.quitApp) {
            window.api.quitApp();
          } else {
            window.location.reload();
          }
        }
      );
    });
  }

  if (cancelExitBtn) {
    cancelExitBtn.addEventListener('click', () => {
      confirmExitModal.style.display = 'none';
    });
  }

  if (confirmExitBtn) {
    confirmExitBtn.addEventListener('click', () => {
      confirmExitModal.style.display = 'none';
      if (window.api?.quitApp) {
        window.api.quitApp();
      }
    });
  }

  const minimizeBtn = document.getElementById('minimizeBtn');
  const desktopBtn = document.getElementById('desktopBtn');
  const closeBtn = document.getElementById('closeBtn');

  if (window.api) {

    if (minimizeBtn) {
      minimizeBtn.addEventListener('click', () => {
        window.api.minimize();
      });
    }

    if (desktopBtn) {
      const desktopIcon = desktopBtn.querySelector('i');
      let isFullscreen = true;

      function updateFullscreenIcon() {
        desktopIcon.className = isFullscreen
          ? 'bx bx-exit-fullscreen'
          : 'bx bx-fullscreen';
      }

      desktopBtn.addEventListener('click', () => {
        window.api.toggleFullscreen();
        isFullscreen = !isFullscreen;
        updateFullscreenIcon();
      });

      updateFullscreenIcon();
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        window.api.close();
      });
    }

  } else {
    console.warn('Window controls disabled – not running in Electron');
  }

  document.addEventListener('keydown', e => {
    const active = document.activeElement;
    if (
      active === stopPitchKeyInput ||
      active === swingKeyInput ||
      active === settingsToggleKeyInput ||
      active === throwTo1BKeyInput ||
      active === throwTo2BKeyInput ||
      active === throwTo3BKeyInput
    ) return;

    const confirmCloseVisible = document.getElementById('confirmCloseModal')?.style.display === 'flex';
    const confirmResetVisible = document.getElementById('confirmResetModal')?.style.display === 'flex';
    const confirmExitVisible = document.getElementById('confirmExitModal')?.style.display === 'flex';
    if (confirmCloseVisible || confirmResetVisible || confirmExitVisible) return;

    const keyName = normalizeKeyName(e.key);

    if (keyName === settingsToggleKey) {
      if (userModal && userModal.style.display === 'flex') {
        closeUserModal();
        return;
      }

      if (settingsOpen) {
        closeSettings();
        return;
      }

      toggleMenu();
    }
  });
  
  if (resetSettingsBtn) {
    const confirmResetModal = document.getElementById('confirmResetModal');
    const cancelResetBtn = document.getElementById('cancelResetBtn');
    const confirmResetBtn = document.getElementById('confirmResetBtn');
  
    resetSettingsBtn.addEventListener('click', () => {
      confirmResetModal.style.display = 'flex';
    });
  
    cancelResetBtn.addEventListener('click', () => {
      confirmResetModal.style.display = 'none';
    });
  
    confirmResetBtn.addEventListener('click', () => {
      confirmResetModal.style.display = 'none';

      const defaults = {
        stopPitchKey: 'Space',
        swingKey: 'Space',
        throwTo1BKey: '1',
        throwTo2BKey: '2',
        throwTo3BKey: '3',
        settingsToggleKey: 'Escape'
      };

      newStopPitchKey = defaults.stopPitchKey;
      newSwingKey = defaults.swingKey;
      newThrowTo1BKey = defaults.throwTo1BKey;
      newThrowTo2BKey = defaults.throwTo2BKey;
      newThrowTo3BKey = defaults.throwTo3BKey;
      newSettingsToggleKey = defaults.settingsToggleKey;

      if (stopPitchKeyInput) stopPitchKeyInput.value = newStopPitchKey;
      if (swingKeyInput) swingKeyInput.value = newSwingKey;
      if (throwTo1BKeyInput) throwTo1BKeyInput.value = newThrowTo1BKey;
      if (throwTo2BKeyInput) throwTo2BKeyInput.value = newThrowTo2BKey;
      if (throwTo3BKeyInput) throwTo3BKeyInput.value = newThrowTo3BKey;
      if (settingsToggleKeyInput) settingsToggleKeyInput.value = newSettingsToggleKey;

      unsavedChanges = true;
    });
  }  

  function setupKeyInput(input, savedKeyName, defaultKey, onChange) {
    const saved = localStorage.getItem(savedKeyName);
    let keyVar = normalizeKeyName(saved || defaultKey);

    setKeyVar(savedKeyName, keyVar);

    function displayKey(k) {
      if (!input) return;
      input.value = (k === ' ' ? 'Space' : (k === 'Space' ? 'Space' : k));
    }
    displayKey(keyVar);

    input.addEventListener('focus', () => { if (input) input.value = 'Press key'; });
    input.addEventListener('keydown', e => {
      e.preventDefault();
      const rawKey = e.key;
      const newKey = normalizeKeyName(rawKey);
      onChange(newKey);
      displayKey(newKey);
      unsavedChanges = true;
      input.blur();
    });
    input.addEventListener('blur', () => {
      const fromOnChange = onChange();
      displayKey(fromOnChange || window[savedKeyName] || keyVar);
    });

    return { get: () => keyVar, set: k => { keyVar = normalizeKeyName(k); setKeyVar(savedKeyName, keyVar); } };
  }

  if (stopPitchKeyInput) {
    setupKeyInput(stopPitchKeyInput, 'stopPitchKey', stopPitchKey, (v) => {
      if (v) newStopPitchKey = v;
      return newStopPitchKey ?? stopPitchKey;
    });
  }

  if (swingKeyInput) {
    setupKeyInput(swingKeyInput, 'swingKey', swingKey, (v) => {
      if (v) newSwingKey = v;
      return newSwingKey ?? swingKey;
    });
  }

  if (throwTo1BKeyInput) {
    setupKeyInput(throwTo1BKeyInput, 'throwTo1BKey', throwTo1BKey, (v) => {
      if (v) newThrowTo1BKey = v;
      return newThrowTo1BKey ?? throwTo1BKey;
    });
  }

  if (throwTo2BKeyInput) {
    setupKeyInput(throwTo2BKeyInput, 'throwTo2BKey', throwTo2BKey, (v) => {
      if (v) newThrowTo2BKey = v;
      return newThrowTo2BKey ?? throwTo2BKey;
    });
  }

  if (throwTo3BKeyInput) {
    setupKeyInput(throwTo3BKeyInput, 'throwTo3BKey', throwTo3BKey, (v) => {
      if (v) newThrowTo3BKey = v;
      return newThrowTo3BKey ?? throwTo3BKey;
    });
  }

  if (settingsToggleKeyInput) {
    setupKeyInput(settingsToggleKeyInput, 'settingsToggleKey', settingsToggleKey, (v) => {
      if (v) newSettingsToggleKey = v;
      return newSettingsToggleKey ?? settingsToggleKey;
    });
  }

  const savedVolume = localStorage.getItem('globalVolume');
  const savedMuted = localStorage.getItem('isMuted');
  if (savedVolume !== null) globalVolume = parseFloat(savedVolume);
  if (savedMuted !== null) isMuted = JSON.parse(savedMuted);

  if (volumeSlider) {
    volumeSlider.value = globalVolume;
    volumeSlider.addEventListener('input', e => {
      globalVolume = parseFloat(e.target.value);
      if (globalVolume > 0) isMuted = false;
      applyVolumeSettings();
      updateVolumeIcon();
      unsavedChanges = true;
    });
  }

  if (volumeIcon) {
    updateVolumeIcon();
    volumeIcon.addEventListener('click', () => {
      isMuted = !isMuted;
      if (isMuted) {
        lastVolume = globalVolume > 0 ? globalVolume : lastVolume;
        globalVolume = 0;
        volumeSlider.value = 0;
      } else {
        globalVolume = lastVolume;
        volumeSlider.value = lastVolume;
      }
      applyVolumeSettings();
      updateVolumeIcon();
      unsavedChanges = true;
    });
  }

  function updateVolumeIcon() {
    if (!volumeIcon) return;
    if (isMuted || globalVolume === 0) {
      volumeIcon.classList.remove('bx-volume-full');
      volumeIcon.classList.add('bx-volume-mute');
    } else {
      volumeIcon.classList.remove('bx-volume-mute');
      volumeIcon.classList.add('bx-volume-full');
    }
  }

  async function openSettings() {
    settingsModal.style.display = 'flex';
    settingsOpen = true;
    savedVolumeBeforeModal = globalVolume;
    savedMutedBeforeModal = isMuted;

    if (window.api?.getWindowMode) {
      savedWindowModeBeforeModal = await window.api.getWindowMode();

      const windowModeSelect = document.getElementById('windowModeSelect');
      if (windowModeSelect && savedWindowModeBeforeModal) {
        windowModeSelect.value = savedWindowModeBeforeModal;
        windowModeSelect.addEventListener('change', () => {
          unsavedChanges = true;
        });
      }
    } else {
      savedWindowModeBeforeModal = null;
    }

    if (typeof showHitZone !== 'undefined' && strikeZoneSwitch) {
      strikeZoneSwitch.checked = !!showHitZone;
    }

    unsavedChanges = false;
  }

  function closeSettings() {
    if (unsavedChanges) {
      confirmCloseModal.style.display = 'flex';
      return;
    }
    settingsModal.style.display = 'none';
    settingsOpen = false;
    newStopPitchKey = null;
    newSwingKey = null;
    newThrowTo1BKey = null;
    newThrowTo2BKey = null;
    newThrowTo3BKey = null;
    newSettingsToggleKey = null;
    unsavedChanges = false;
  }

  closeModalBtn.addEventListener('click', closeSettings);
  settingsModal.addEventListener('click', e => { if (e.target === settingsModal) closeSettings(); });
  if (strikeZoneSwitch) strikeZoneSwitch.addEventListener('change', () => unsavedChanges = true);

  if (cancelCloseBtn) cancelCloseBtn.addEventListener('click', () => confirmCloseModal.style.display = 'none');
  if (confirmCloseBtn) confirmCloseBtn.addEventListener('click', () => {
    setKeyVar('stopPitchKey', localStorage.getItem('stopPitchKey') || 'Space');
    setKeyVar('swingKey', localStorage.getItem('swingKey') || 'Space');
    setKeyVar('throwTo1BKey', localStorage.getItem('throwTo1BKey') || '1');
    setKeyVar('throwTo2BKey', localStorage.getItem('throwTo2BKey') || '2');
    setKeyVar('throwTo3BKey', localStorage.getItem('throwTo3BKey') || '3');
    setKeyVar('settingsToggleKey', localStorage.getItem('settingsToggleKey') || 'Escape');
  
    if (stopPitchKeyInput) stopPitchKeyInput.value = stopPitchKey;
    if (swingKeyInput) swingKeyInput.value = swingKey;
    if (throwTo1BKeyInput) throwTo1BKeyInput.value = throwTo1BKey;
    if (throwTo2BKeyInput) throwTo2BKeyInput.value = throwTo2BKey;
    if (throwTo3BKeyInput) throwTo3BKeyInput.value = throwTo3BKey;
    if (settingsToggleKeyInput) settingsToggleKeyInput.value = settingsToggleKey;

    if (savedWindowModeBeforeModal && window.api?.setWindowMode) {
      window.api.setWindowMode(savedWindowModeBeforeModal);
    }

    newStopPitchKey = null;
    newSwingKey = null;
    newThrowTo1BKey = null;
    newThrowTo2BKey = null;
    newThrowTo3BKey = null;
    newSettingsToggleKey = null;
  
    globalVolume = savedVolumeBeforeModal;
    isMuted = savedMutedBeforeModal;
    if (volumeSlider) volumeSlider.value = globalVolume;
    applyVolumeSettings();
    updateVolumeIcon();
    settingsModal.style.display = 'none';
    confirmCloseModal.style.display = 'none';
    settingsOpen = false;
    unsavedChanges = false;
  });

  function saveSettings() {
    if (typeof strikeZoneSwitch !== 'undefined') {
        showHitZone = !!strikeZoneSwitch.checked;
        localStorage.setItem('showHitZone', JSON.stringify(showHitZone));
    }
    if (newStopPitchKey !== null) { setKeyVar('stopPitchKey', newStopPitchKey); localStorage.setItem('stopPitchKey', stopPitchKey); newStopPitchKey = null; }
    if (newSwingKey !== null) { setKeyVar('swingKey', newSwingKey); localStorage.setItem('swingKey', swingKey); newSwingKey = null; }
    if (newThrowTo1BKey !== null) { setKeyVar('throwTo1BKey', newThrowTo1BKey); localStorage.setItem('throwTo1BKey', throwTo1BKey); newThrowTo1BKey = null; }
    if (newThrowTo2BKey !== null) { setKeyVar('throwTo2BKey', newThrowTo2BKey); localStorage.setItem('throwTo2BKey', throwTo2BKey); newThrowTo2BKey = null; }
    if (newThrowTo3BKey !== null) { setKeyVar('throwTo3BKey', newThrowTo3BKey); localStorage.setItem('throwTo3BKey', throwTo3BKey); newThrowTo3BKey = null; }
    if (newSettingsToggleKey !== null) { setKeyVar('settingsToggleKey', newSettingsToggleKey); localStorage.setItem('settingsToggleKey', settingsToggleKey); newSettingsToggleKey = null; }

    localStorage.setItem('stopPitchKey', stopPitchKey);
    localStorage.setItem('swingKey', swingKey);
    localStorage.setItem('throwTo1BKey', throwTo1BKey);
    localStorage.setItem('throwTo2BKey', throwTo2BKey);
    localStorage.setItem('throwTo3BKey', throwTo3BKey);
    localStorage.setItem('settingsToggleKey', settingsToggleKey);

    localStorage.setItem('globalVolume', globalVolume);
    localStorage.setItem('isMuted', JSON.stringify(isMuted));
    applyVolumeSettings();

    const windowModeSelect = document.getElementById('windowModeSelect');
    if (windowModeSelect && window.api?.setWindowMode) {
      window.api.setWindowMode(windowModeSelect.value);
    }

    try {
      const ev = new CustomEvent('controls-updated', {
        detail: {
          stopPitchKey, swingKey, throwTo1BKey, throwTo2BKey, throwTo3BKey, settingsToggleKey
        }
      });
      window.dispatchEvent(ev);
    } catch (err) {}

    unsavedChanges = false;
  }

  if (saveSettingsBtn) saveSettingsBtn.addEventListener('click', saveSettings);
  if (saveCloseSettingsBtn) saveCloseSettingsBtn.addEventListener('click', () => {
    saveSettings();
    settingsModal.style.display = 'none';
    settingsOpen = false;
  });

  const savedHitZone = localStorage.getItem('showHitZone');
  if (savedHitZone !== null) {
    try { showHitZone = JSON.parse(savedHitZone); } catch (err) {}
  }

  document.addEventListener('keydown', e => {
    const active = document.activeElement;
    if (active === stopPitchKeyInput ||
        active === swingKeyInput ||
        active === settingsToggleKeyInput ||
        active === throwTo1BKeyInput ||
        active === throwTo2BKeyInput ||
        active === throwTo3BKeyInput) return;

    const keyName = normalizeKeyName(e.key);

    if (keyName === swingKey && !pickoffInProgress && gameState === 'offense' && ball.active) {
      if (typeof triggerSwing === 'function') triggerSwing();
    }

    if (keyName === stopPitchKey) {
      if (typeof stopPitch === 'function') stopPitch();
    }

    if (keyName === throwTo1BKey) {
      if (typeof throwToBase === 'function') throwToBase(1);
      if (typeof pickoffTo1B === 'function') pickoffTo1B();
    }
    if (keyName === throwTo2BKey) {
      if (typeof throwToBase === 'function') throwToBase(2);
      if (typeof pickoffTo2B === 'function') pickoffTo2B();
    }
    if (keyName === throwTo3BKey) {
      if (typeof throwToBase === 'function') throwToBase(3);
      if (typeof pickoffTo3B === 'function') pickoffTo3B();
    }
  });

  applyVolumeSettings();
});