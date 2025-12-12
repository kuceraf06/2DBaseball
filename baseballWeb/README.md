<h1>2D Baseball — Web Platform</h1>

The web companion to the 2D Baseball Simulator project, providing players with game information, tutorials, gameplay rules, account management, match history, leaderboards, downloads, and community support.

This web module serves as the public-facing hub of the project, complementing the desktop game.</br>
</br>
🌐 Overview

The 2D Baseball Web Platform delivers all non-gameplay user services, including:

Game guides, rules, and instructions

Player account system

Match history & profile statistics

Global leaderboard based on number of games played

News and information about the project

Support & contact options

Download portal for the desktop (Electron) version

Accessible, responsive, and designed for fast navigation and clear presentation.

</br>
</br>
📌 Website Structure

This project lives in:

2DBaseball/
└── baseballWeb/


The web contains multiple sections:

About

Information about the project, its goals, features, updates, and developer notes.

How to Play

A beginner-friendly guide explaining gameplay mechanics, controls, and strategies.

Rules

Official rules and logic used in the baseball simulator, including:

Pitching / hitting system

Base running and stealing

AI behavior basics

Leaderboard

A global ranking sorted by:

Total number of games played

All records are dynamically loaded from the backend database.

Match History

Each registered user can view:

Completed game results

Scores

</br>
</br>
<b>User System</b>

Includes:

🔐 Registration

🔑 Login

⚙️ Account management (email, password, profile details)

🗃️ Player statistics and profile info

</br>
</br>
<b>Support</b>

A section for:

Reporting issues

Requesting help

Contacting the developer

</br>
</br>
<b>Download</b>

Direct download page for the Electron desktop version, including:

Windows executable (.exe)

</br>
</br>
🛠️ Technologies

HTML5 / CSS3 / JavaScript

PHP / SQLite

AJAX / Fetch API

Responsive CSS

Server-side authentication system

</br>
</br>
🔐 User Authentication

The platform supports:

Secure login with encrypted credentials

New account registration

Session-based authentication

Account modification options

Recovery pathways (if implemented)

User statistics sync seamlessly with match history and leaderboards.

</br>
</br>
🏆 Leaderboard System

The leaderboard automatically updates based on:

Number of games played per user

Data is stored in the database and displayed dynamically on the leaderboard page.

</br>
</br>
📬 Contact / Author

2D Baseball - Filip Kučera

[mail](2dbaseball25@gmail.com) / [kuceraf](https://github.com/kuceraf06) / [website](https://xeon.spskladno.cz/~kuceraf/2DBaseball/baseballWeb/)