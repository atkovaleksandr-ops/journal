const { app, BrowserWindow, shell, Menu } = require('electron');
const fs = require('fs');
const path = require('path');

const DEFAULT_JOURNAL_URL = 'https://journal-production-858f.up.railway.app';

function resolveJournalUrl() {
  if (process.env.JOURNAL_APP_URL) {
    return process.env.JOURNAL_APP_URL;
  }

  for (const configPath of journalUrlConfigPaths()) {
    try {
      if (!fs.existsSync(configPath)) {
        continue;
      }

      const configuredUrl = fs.readFileSync(configPath, 'utf8').trim();
      if (configuredUrl) {
        return configuredUrl;
      }
    } catch {
      // Ignore broken local config files and fall back to the default URL.
    }
  }

  return DEFAULT_JOURNAL_URL;
}

function journalUrlConfigPaths() {
  const executableDir = path.dirname(app.getPath('exe'));

  return [
    path.join(executableDir, 'journal-url.txt'),
    path.join(app.getPath('userData'), 'journal-url.txt'),
    path.join(__dirname, '..', 'journal-url.txt')
  ];
}

function withAppMarker(rawUrl) {
  try {
    const url = new URL(rawUrl);
    url.searchParams.set('journal_app', 'windows');

    return url.toString();
  } catch {
    return rawUrl;
  }
}

function createWindow() {
  const journalUrl = withAppMarker(resolveJournalUrl());

  const win = new BrowserWindow({
    width: 1180,
    height: 760,
    minWidth: 920,
    minHeight: 620,
    title: 'Journal',
    backgroundColor: '#0f172a',
    autoHideMenuBar: true,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false,
      sandbox: true,
      webSecurity: true
    }
  });

  win.loadURL(journalUrl).catch(() => {
    win.loadFile(path.join(__dirname, 'offline.html'));
  });

  win.webContents.setWindowOpenHandler(({ url }) => {
    if (isAllowedNavigation(url, journalUrl)) {
      return { action: 'allow' };
    }

    shell.openExternal(url);
    return { action: 'deny' };
  });

  win.webContents.on('will-navigate', (event, url) => {
    if (!isAllowedNavigation(url, journalUrl)) {
      event.preventDefault();
      shell.openExternal(url);
    }
  });

  win.webContents.on('did-fail-load', (_event, _errorCode, _errorDescription, validatedUrl, isMainFrame) => {
    if (isMainFrame && !validatedUrl.startsWith('file://')) {
      win.loadFile(path.join(__dirname, 'offline.html'));
    }
  });
}

function isAllowedNavigation(targetUrl, baseUrl) {
  try {
    const target = new URL(targetUrl);
    const base = new URL(baseUrl);

    return target.origin === base.origin;
  } catch {
    return false;
  }
}

app.whenReady().then(() => {
  Menu.setApplicationMenu(null);
  createWindow();

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});
