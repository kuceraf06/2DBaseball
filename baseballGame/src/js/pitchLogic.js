function evaluatePitchAtPosition(pos, sliderWidth, rng = Math.random) {
  const gray1 = sliderWidth * 0.6;
  const blue = sliderWidth * 0.25;
  const green = sliderWidth * 0.1;

  if (pos < gray1) return 'BALL';
  if (pos < gray1 + blue) return rng() < 0.5 ? 'STRIKE' : 'BALL';
  if (pos < gray1 + blue + green) return 'STRIKE';
  return 'BALL';
}

module.exports = { evaluatePitchAtPosition };
