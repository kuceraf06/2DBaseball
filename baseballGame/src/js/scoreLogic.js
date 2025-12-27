function createInitialState() {
  return {
    strikes: 0,
    balls: 0,
    outs: 0,
    teamAScore: 0,
    teamBScore: 0
  };
}

function applyPitchResult(state, result) {
  const s = { ...state };

  if (result === 'STRIKE') {
    s.strikes++;
    if (s.strikes >= 3) {
      s.strikes = 0;
      s.balls = 0;
      s.outs++;
    }
  }

  if (result === 'BALL') {
    s.balls++;
    if (s.balls >= 4) {
      s.strikes = 0;
      s.balls = 0;
    }
  }

  return s;
}

function applyRun(state, battingTeam) {
  const s = { ...state };
  if (battingTeam === 'A') s.teamAScore++;
  else s.teamBScore++;
  return s;
}

module.exports = {
  createInitialState,
  applyPitchResult,
  applyRun
};
