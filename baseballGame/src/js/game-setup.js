if (typeof applyVolumeSettings === 'function') applyVolumeSettings();

function startAnimation() {
  animationInProgress = true;
  throwButton.disabled = true;
  pitchTypeContainer.style.display = 'none';
  hidePickoffButtons();
  throwButton.classList.add("disabled");
}

function endAnimation() {
  animationInProgress = false;
  setTimeout(() => {
    throwButton.disabled = false;
    throwButton.classList.remove("disabled");

    if (gameState === 'defense') {
      showPickoffButtons();
      pitchTypeContainer.style.display = 'flex';
    } else {
      hidePickoffButtons();
      pitchTypeContainer.style.display = 'none';
    }
  }, 500);
}

function nextBatter() {
  const batter = battersQueue.shift();
  battersQueue.push(batter);

  currentOnDeckBatter = batter;

  return batter;
}

const players = [
  { name: 'Catcher', img: catcherImg, x: centerX - 12, y: homePlateY + 7 },
  { name: 'Nadhazovac', img: nadhazovacImg, x: centerX - 5, y: homePlateY - baseDistance - 5 },

  { name: 'Polar_LeftField', img: polarImg, x: centerX - 230, y: homePlateY - 425 },
  { name: 'Polar_CenterField', img: polarImg, x: centerX - 20,  y: homePlateY - 500 },
  { name: 'Polar_RightField', img: polarImg, x: centerX + 190, y: homePlateY - 425 },

  { name: 'Polar_SecondBase', img: polarImg, x: centerX + baseDistance / 3, y: homePlateY - baseDistance - 160},
  { name: 'Polar_ShortStop', img: polarImg, x: centerX - baseDistance / 2, y: homePlateY - baseDistance * 2 },

  { name: 'Polar_ThirdBase', img: polarImg, x: centerX - baseDistance - 0, y: homePlateY - baseDistance - 70 },
  { name: 'Polar_FirstBase', img: polarImg, x: centerX + baseDistance - 30, y: homePlateY - baseDistance - 70 }
];

const catcher = players.find(p => p.name === 'Catcher');
catcher.homeX = catcher.x;
catcher.homeY = catcher.y;
catcher.moving = false;

function getBatterPositions() {
  return [
    {
      ...battersQueue[0],
      x: centerX - 37,
      y: homePlateY - 15
    },
    {
      ...battersQueue[1],
      x: centerX + 90 - playerSize/2,
      y: homePlateY - 5 - playerSize/2
    }
  ];
}