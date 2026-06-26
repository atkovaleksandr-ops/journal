#define MyAppName "Journal"
#define MyAppVersion "1.1.3"
#define MyAppPublisher "Journal Project"
#define MyAppExeName "Journal.exe"
#define MyAppUrl "http://journal.test"

[Setup]
AppId={{8A13CF6D-A29B-4ED1-B642-C613D5B0229D}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppUrl}
AppSupportURL={#MyAppUrl}
AppUpdatesURL={#MyAppUrl}
DefaultDirName={localappdata}\Programs\Journal
DefaultGroupName=Journal
DisableProgramGroupPage=no
DisableDirPage=no
OutputDir=..\..\..\public\downloads
OutputBaseFilename=Journal-Windows-Setup
Compression=lzma2
SolidCompression=yes
WizardStyle=modern
PrivilegesRequired=lowest
ArchitecturesAllowed=x64compatible
ArchitecturesInstallIn64BitMode=x64compatible
CloseApplications=force
UninstallDisplayIcon={app}\{#MyAppExeName}
SetupLogging=yes

[Languages]
Name: "russian"; MessagesFile: "compiler:Languages\Russian.isl"

[Tasks]
Name: "desktopicon"; Description: "Создать ярлык на рабочем столе"; GroupDescription: "Дополнительные действия:"; Flags: checkedonce

[Files]
Source: "..\dist\win-unpacked\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs

[Icons]
Name: "{group}\Journal"; Filename: "{app}\{#MyAppExeName}"; WorkingDir: "{app}"
Name: "{group}\Удалить Journal"; Filename: "{uninstallexe}"
Name: "{autodesktop}\Journal"; Filename: "{app}\{#MyAppExeName}"; WorkingDir: "{app}"; Tasks: desktopicon

[Run]
Filename: "{app}\{#MyAppExeName}"; Description: "Запустить Journal"; Flags: nowait postinstall skipifsilent

[UninstallDelete]
Type: files; Name: "{app}\journal-url.txt"
Type: files; Name: "{app}\journal-version.txt"
Type: files; Name: "{app}\journal-update-url.txt"

[Code]
procedure CurStepChanged(CurStep: TSetupStep);
begin
  if CurStep = ssPostInstall then
  begin
    SaveStringToFile(ExpandConstant('{app}\journal-url.txt'), '{#MyAppUrl}', False);
    SaveStringToFile(ExpandConstant('{app}\journal-version.txt'), '{#MyAppVersion}', False);
    SaveStringToFile(ExpandConstant('{app}\journal-update-url.txt'), '{#MyAppUrl}/download/version', False);
  end;
end;
