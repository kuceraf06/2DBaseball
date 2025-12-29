<h1>2D Baseball Simulator — Desktop Version</h1>

A full 2D interactive baseball simulator built using Electron, allowing players to pitch, hit, steal bases, perform pickoffs, and compete against a dynamic AI opponent — all in a polished desktop application.</br>
</br>
📌 Repository Structure</br>

This part of the project lives in the directory:

2DBaseball/</br>
└── baseballGame/


Inside this folder you will find all assets, source files, UI components, and Electron configuration required to run the desktop version of the game.

The project is structured as:

<b>baseballGame/</b>

The full Electron-powered desktop 2D baseball simulator, including:

⚾ Pitching mechanics (fastball, changeup, slider — depending on your implementation)

🏏 Realistic hitting with physics-driven trajectories

👟 Base stealing and timing-based interactions

🎯 Pickoff mechanics for pitchers

🤖 Adaptive AI reacting to game situations

🎮 Desktop UI, animations, scoreboard, assets, and input controls

</br>
</br>
🚀 Game Features</br>

Complete 2D baseball experience

Pitching → hitting gameplay loop

Smart AI responding dynamically to pitch types, runners, and score

Ball physics (speed, drag, angles, collisions)

Clean UI with scoreboard, animations, and feedback

Desktop runtime powered by Electron

</br>
</br>
🛠️ Technologies</br>

Desktop Game (Electron-based)

JavaScript / HTML5 / CSS

Electron 28+ (or your version)

SQLite for login or save stats

Canvas-based rendering or DOM-based animations

</br>
</br>
⚙️ How to Run the Desktop Version (Electron)</br>

Follow these steps to launch the game from the baseballGame/ folder.

1. Install Node.js

Download from:</br>
https://nodejs.org/

(Required for Electron.)

2. Install Electron globally or locally

Option A — Local installation (recommended):

npm install</br>
npm install electron


Option B — Global installation:

npm install -g electron

3. Start the Game

Inside the baseballGame/ folder, run:

npx electron . or npm start


or if installed globally:

electron .


This will launch the 2D Baseball desktop app.

</br>
📦 How to Build a Desktop EXE (Windows)</br>

To create a standalone .exe file:

1. Install Electron Packager</br>
npm install electron-packager -D

2. Run Packager Command

Example (you may adjust app name and icon):

npx electron-packager . "2DBaseball" --platform=win32 --arch=x64 --out=dist --overwrite


After building:

Your .exe will appear in the /dist/2DBaseball-win32-x64/ folder.

This folder contains a fully portable desktop version of the game.

Optional: Build for other platforms</br>
--platform=linux</br>
--platform=darwin   (Mac)


</br>
</br> 
🧪 Automated Testing (Jest)</br>

The project includes automated tests to verify core game logic and functionality.

Testing is handled using Jest, a JavaScript testing framework.

</br> 
⚙️ How Testing Works</br>

Tests are focused on validating:

Game logic (rules, states, calculations)

Core mechanics (pitching, hitting logic, AI decisions)

Utility functions and helpers

The tests run in a Node.js environment and do not require Electron to be launched.

</br> 
🚀 How to Run Tests</br>

Install Jest (if not already installed)

Inside the baseballGame/ folder run:

npm install --save-dev jest


Run Automated Tests

From the same folder, execute:

npm test


Jest will automatically:

Locate all test files</br>
Execute test cases</br>
Display pass/fail results directly in the terminal

</br>
</br>
🤖 AI Opponent Overview</br>

The in-game AI evaluates:

Pitch types

Base runner positions

Inning and score

Randomness vs. strategy

Steal attempts

Swing timing and decision-making

</br>
</br>
📬 Contact / Authors</br>

2D Baseball - Filip Kučera

[mail](2dbaseball25@gmail.com) / [kuceraf](https://github.com/kuceraf06) / [website](https://xeon.spskladno.cz/~kuceraf/2DBaseball/baseballWeb/)
