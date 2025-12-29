const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld("api", {
    minimize: () => ipcRenderer.send("window-minimize"),
    toggleFullscreen: () => ipcRenderer.send("window-hide"),
    close: () => ipcRenderer.send("window-close"),
    quitApp: () => ipcRenderer.send("app-quit"),

    setWindowMode: (mode) => ipcRenderer.send("set-window-mode", { mode }),
    getWindowMode: () => ipcRenderer.invoke("get-window-mode"),


    openExternal: (url) => ipcRenderer.send("open-external", url),

    loadIndex: () => ipcRenderer.send("load-index"),
    loadLogin: () => ipcRenderer.send("load-login")
});

window.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('check-update-btn');
  if (btn) {
    btn.addEventListener('click', () => {
      ipcRenderer.invoke('manual-update-check');
    });
  }
});
