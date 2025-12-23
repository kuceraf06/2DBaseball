const { app, BrowserWindow, ipcMain, shell, screen } = require('electron');
const path = require('path');
const fs = require('fs');

let mainWindow;
let isWindowFullscreen = true;

// resolve preload - hledá v dev i v app.asar.unpacked
function resolvePreload(preloadRelativePath = 'preload.js') {
  const devPath = path.join(__dirname, preloadRelativePath);
  if (fs.existsSync(devPath)) return devPath;

  const unpacked = path.join(process.resourcesPath, 'app.asar.unpacked', preloadRelativePath);
  if (fs.existsSync(unpacked)) return unpacked;

  return devPath;
}

function createWindow() {
  const preloadPath = resolvePreload('preload.js');
  console.log('Using preload:', preloadPath, 'exists:', fs.existsSync(preloadPath));
  console.log('__dirname:', __dirname);
  console.log('process.resourcesPath:', process.resourcesPath);
  console.log('userData (for app files):', app.getPath('userData'));

  const devIcon = path.join(__dirname, 'src', 'images', 'favicon.ico'); // vývojová ikona
  const prodIcon = path.join(process.resourcesPath || '', 'build', 'icon.ico'); // po zabalení electron-builder vloží sem
  let iconPath;
  if (fs.existsSync(devIcon)) {
    iconPath = devIcon;
  } else if (fs.existsSync(prodIcon)) {
    iconPath = prodIcon;
  } else {
    iconPath = undefined; // fallback - Electron použije defaultní icon
  }

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
}

// --- window / ui ipc ---
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
});
ipcMain.on('open-external', (event, url) => { shell.openExternal(url); });

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

// jednoduché IPC handlery, bez lokální DB
ipcMain.handle('get-window-mode', () => (isWindowFullscreen ? 'fullscreen' : 'windowed'));

// start app
app.whenReady().then(() => {
  createWindow();
});

app.on('window-all-closed', () => { if (process.platform !== 'darwin') app.quit(); });
app.on('activate', () => { if (BrowserWindow.getAllWindows().length === 0) createWindow(); });