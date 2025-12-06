const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld("api", {
    getUsers: () => ipcRenderer.invoke("get-users"),

    minimize: () => ipcRenderer.send("window-minimize"),
    toggleFullscreen: () => ipcRenderer.send("window-hide"),
    close: () => ipcRenderer.send("window-close"),
    quitApp: () => ipcRenderer.send("app-quit"),

    setWindowMode: (mode) => ipcRenderer.send("set-window-mode", { mode }),
    getWindowMode: () => ipcRenderer.invoke("get-window-mode"),


    openExternal: (url) => ipcRenderer.send("open-external", url)
});
