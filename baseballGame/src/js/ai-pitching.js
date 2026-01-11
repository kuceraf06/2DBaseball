function aiPitch() {
  if (gameState !== 'offense') return;
  if (animationInProgress && !runnersInMotion) return;

  resultEvaluated = false;
  hitRegistered = false;
  swingActive = false;

  if (bases[0] && Math.random() < AI_PICKOFF_CHANCE_1B) {
    startPickoff1B();
    return;
  }
  if (bases[1] && Math.random() < AI_PICKOFF_CHANCE_2B) {
    startPickoff2B();
    return;
  }
  if (bases[2] && Math.random() < AI_PICKOFF_CHANCE_3B) {
    startPickoff3B();
    return;
  }

  const isStrike = Math.random() < AI_PITCH_STRIKE_CHANCE;
  const pitchTypes = ['FB', 'CH', 'SL'];
  const chosenPitch = pitchTypes[Math.floor(Math.random() * pitchTypes.length)];
  selectedPitch = chosenPitch;

  slider.result = isStrike ? "STRIKE" : "BALL";

  const pitcher = players.find(p => p.name === 'Nadhazovac');
  const catcher = players.find(p => p.name === 'Catcher');

  ball.startX = pitcher.x + playerSize / 2;
  ball.startY = pitcher.y + playerSize / 2;
  ball.endX = catcher.x + playerSize / 2 + (slider.result === 'BALL' ? 25 : 0);
  ball.endY = catcher.y + playerSize / 2;

  catcher.targetX = ball.endX - playerSize / 2;
  catcher.targetY = ball.endY - playerSize / 2;
  catcher.moving = true;

  ball.x = ball.startX;
  ball.y = ball.startY;
  ball.progress = 0;
  ball.active = true;

  ball.isSliderFlight = (chosenPitch === 'SL');

  startAnimation();

  if (throwSound) {
    throwSound.currentTime = 0;
    throwSound.play().catch(() => {});
  }


  let speedFactor = PITCH_SPEEDS[chosenPitch] || 0.01;

  animateBall(() => {
    ball.owner = "catcher";
    lastPitch = chosenPitch;
    setTimeout(() => {
      if (!preventReturnToPitcher) {
        returnBallToPitcher();
      }
      preventReturnToPitcher = false;
    }, 800);
  }, speedFactor);
  const waitForBallDone = setInterval(() => {
    if (!ball.active) {
      clearInterval(waitForBallDone);
      evaluateResult();
    }
  }, 50);
}

throwButtonEl.addEventListener('click', () => {
  if (gameState === 'offense' && !animationInProgress) {
    setTimeout(aiPitch, 600 + Math.random() * 750);
  }
});
