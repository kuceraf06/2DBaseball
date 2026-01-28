const { app, BrowserWindow, ipcMain, dialog, shell, screen, Notification } = require('electron');
const path = require('path');
const https = require("https");
const fs = require('fs');

let mainWindow;
let isWindowFullscreen = true;

function resolvePreload(preloadRelativePath = 'preload.js') {
  const devPath = path.join(__dirname, preloadRelativePath);
  if (fs.existsSync(devPath)) return devPath;

  const unpacked = path.join(process.resourcesPath, 'app.asar.unpacked', preloadRelativePath);
  if (fs.existsSync(unpacked)) return unpacked;

  return devPath;
}

function isNewerVersion(remote, local) {
  const r = remote.split('.').map(Number);
  const l = local.split('.').map(Number);

  for (let i = 0; i < Math.max(r.length, l.length); i++) {
    const rv = r[i] || 0;
    const lv = l[i] || 0;

    if (rv > lv) return true;
    if (rv < lv) return false;
  }
  return false;
}

function checkForUpdates() {
  return new Promise((resolve, reject) => {
    const https = require('https');

    https.get('https://raw.githubusercontent.com/kuceraf06/2DBaseball/main/baseballGame/update.json', res => {
      let data = '';
      res.on('data', chunk => { data += chunk; });
      res.on('end', () => {
        try {
          const updateInfo = JSON.parse(data);
          resolve(updateInfo);
        } catch (err) {
          reject(err);
        }
      });
    }).on('error', err => reject(err));
  });
}

const currentVersion = app.getVersion();
let lastNotifiedVersion = null;

async function promptUpdate(showDialog = false) {
  try {
    const updateInfo = await checkForUpdates();
    const currentVersion = app.getVersion();
    const hasUpdate = isNewerVersion(updateInfo.version, currentVersion);

    if (hasUpdate) {
      if (!showDialog && updateInfo.version !== lastNotifiedVersion) {
        lastNotifiedVersion = updateInfo.version;
        
        if (Notification.isSupported()) {
          const notification = new Notification({
            title: 'Update Available',
            body: `A new version (${updateInfo.version}) is available. Click to download.`,
            icon: app.isPackaged
              ? path.join(process.resourcesPath, 'build', 'favicon.png')
              : path.join(__dirname, 'build', 'favicon.png')
          });

          notification.on('click', () => {
            if (mainWindow) {
              mainWindow.show();
              mainWindow.focus();
            }
            shell.openExternal(updateInfo.url);
          });

          notification.show();
        } else if (mainWindow) {
          const choice = dialog.showMessageBoxSync(mainWindow, {
            type: 'info',
            buttons: ['Download', 'Later'],
            defaultId: 0,
            cancelId: 1,
            title: 'Update Available',
            message: `A new version (${updateInfo.version}) is available.\nDo you want to download it?`
          });
          if (choice === 0) shell.openExternal(updateInfo.url);
        }
      }
      
      if (showDialog && mainWindow) {
        const choice = dialog.showMessageBoxSync(mainWindow, {
          type: 'info',
          buttons: ['Download', 'Later'],
          defaultId: 0,
          cancelId: 1,
          title: 'Update Available',
          message: `A new version (${updateInfo.version}) is available.\nDo you want to download it?`
        });
        if (choice === 0) shell.openExternal(updateInfo.url);
      }
    } else if (showDialog && mainWindow) {
      dialog.showMessageBoxSync(mainWindow, {
        type: 'info',
        buttons: ['OK'],
        defaultId: 0,
        title: 'Up to date',
        message: `You already have the latest version (${currentVersion}).`
      });
    }

    return {
      hasUpdate: hasUpdate,
      current: currentVersion,
      latestVersion: updateInfo.version,
      url: updateInfo.url
    };

  } catch (err) {
    if (showDialog && mainWindow) {
      dialog.showMessageBoxSync(mainWindow, {
        type: 'error',
        buttons: ['OK'],
        defaultId: 0,
        title: 'Update check failed',
        message: `Could not check for updates.\nError: ${err.message}`
      });
    }
    return { error: true };
  }
}

function createWindow() {
  const preloadPath = resolvePreload('preload.js');
  console.log('Using preload:', preloadPath, 'exists:', fs.existsSync(preloadPath));
  console.log('__dirname:', __dirname);
  console.log('process.resourcesPath:', process.resourcesPath);
  console.log('userData (for app files):', app.getPath('userData'));

  const iconPath = app.isPackaged
  ? path.join(process.resourcesPath, 'build', 'favicon.png')
  : path.join(__dirname, 'build', 'favicon.png');

  mainWindow = new BrowserWindow({
    width: 1600,
    height: 900,
    fullscreen: true,
    resizable: true,
    icon: iconPath,
    webPreferences: {
      preload: preloadPath,
      contextIsolation: true,
      nodeIntegration: false
    }
  });

  mainWindow.loadFile(path.join(__dirname, 'src', 'index.html'))
    .catch(err => console.error('Nelze načíst index.html:', err));

  mainWindow.on('closed', () => {
    mainWindow = null;
  });

  mainWindow.on('will-resize', (event, newBounds) => {
    if (!mainWindow || mainWindow.isFullScreen() || mainWindow.isMaximized()) return;
    event.preventDefault();

    const oldBounds = mainWindow.getBounds();
    const centerX = oldBounds.x + oldBounds.width / 2;
    const centerY = oldBounds.y + oldBounds.height / 2;

    const minWidth = 400;
    const minHeight = 300;

    const widthRatio = newBounds.width / oldBounds.width;
    const heightRatio = newBounds.height / oldBounds.height;

    let factor;
    const widthChanged = Math.abs(widthRatio - 1) > 1e-3;
    const heightChanged = Math.abs(heightRatio - 1) > 1e-3;

    if (widthChanged && heightChanged) {
      if (widthRatio < 1 && heightRatio < 1) {
        factor = Math.min(widthRatio, heightRatio);
      } else if (widthRatio > 1 && heightRatio > 1) {
        factor = Math.max(widthRatio, heightRatio);
      } else {
        factor = (Math.abs(widthRatio - 1) >= Math.abs(heightRatio - 1)) ? widthRatio : heightRatio;
      }
    } else if (widthChanged) {
      factor = widthRatio;
    } else if (heightChanged) {
      factor = heightRatio;
    } else {
      factor = 1;
    }

    let newWidth = Math.round(oldBounds.width * factor);
    let newHeight = Math.round(oldBounds.height * factor);

    newWidth = Math.max(newWidth, minWidth);
    newHeight = Math.max(newHeight, minHeight);

    const x = Math.round(centerX - newWidth / 2);
    const y = Math.round(centerY - newHeight / 2);

    mainWindow.setBounds({ x, y, width: newWidth, height: newHeight });
  });

  mainWindow.once('ready-to-show', () => {
    mainWindow.show();
    setTimeout(() => {
      promptUpdate(false);
    }, 2000);
  });

}

ipcMain.handle('manual-update-check', async () => {
  return await promptUpdate(true);
});

ipcMain.on('app-quit', () => app.quit());
ipcMain.on('window-minimize', () => { if (mainWindow) mainWindow.minimize(); });
ipcMain.on('window-hide', () => {
  if (!mainWindow) return;

  if (isWindowFullscreen) {
    mainWindow.setFullScreen(false);

    const { width, height } = screen.getPrimaryDisplay().workAreaSize;
    const newWidth = 1600;
    const newHeight = 900;
    const x = Math.floor((width - newWidth) / 2);
    const y = Math.floor((height - newHeight) / 2);
    mainWindow.setBounds({ x, y, width: newWidth, height: newHeight });

    isWindowFullscreen = false;
  } else {
    mainWindow.setFullScreen(true);
    isWindowFullscreen = true;
  }

  notifyWindowMode();
});
ipcMain.on('window-close', () => { if (mainWindow) mainWindow.close(); });
ipcMain.on('set-window-mode', (event, { mode }) => {
  if (!mainWindow) return;

  const isFull = mode === 'fullscreen';
  mainWindow.setFullScreen(isFull);

  if (!isFull) {
    const { width, height } = screen.getPrimaryDisplay().workAreaSize;
    const newWidth = 1600;
    const newHeight = 900;
    const x = Math.floor((width - newWidth) / 2);
    const y = Math.floor((height - newHeight) / 2);
    mainWindow.setBounds({ x, y, width: newWidth, height: newHeight });
  }

  isWindowFullscreen = isFull;
  notifyWindowMode();
});
ipcMain.on('open-external', (event, url) => { shell.openExternal(url); });

function notifyWindowMode() {
  if (mainWindow) {
    mainWindow.webContents.send(
      'window-mode-changed',
      isWindowFullscreen ? 'fullscreen' : 'windowed'
    );
  }
}

function loadLogin() {
  if (!mainWindow) return;
  mainWindow.loadFile(path.join(__dirname, 'src', 'login.html'))
    .catch(err => console.error('Nelze načíst login.html:', err));
}
ipcMain.on('load-login', () => loadLogin());
ipcMain.on('load-index', () => {
  if (!mainWindow) return;
  mainWindow.loadFile(path.join(__dirname, 'src', 'index.html'))
    .catch(err => console.error('Nelze načíst index.html:', err));
});

ipcMain.handle('get-window-mode', () => (isWindowFullscreen ? 'fullscreen' : 'windowed'));

ipcMain.handle('get-app-version', () => {
  return app.getVersion();
});

app.whenReady().then(() => {
  createWindow();
});

app.on('window-all-closed', () => { if (process.platform !== 'darwin') app.quit(); });
app.on('activate', () => { if (BrowserWindow.getAllWindows().length === 0) createWindow(); });