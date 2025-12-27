const { evaluatePitchAtPosition } = require('../src/js/pitchLogic');

describe('evaluatePitchAtPosition', () => {

  test('Green zone always results in STRIKE', () => {
    const sliderWidth = 1000;
    const greenMiddle = 900;

    const result = evaluatePitchAtPosition(greenMiddle, sliderWidth);

    expect(result).toBe('STRIKE');
  });

  test('Blue zone uses 50% chance logic', () => {
    const sliderWidth = 1000;
    const blueMiddle = 725;

    const alwaysStrike = () => 0.1;
    const alwaysBall = () => 0.9;

    expect(
      evaluatePitchAtPosition(blueMiddle, sliderWidth, alwaysStrike)
    ).toBe('STRIKE');

    expect(
      evaluatePitchAtPosition(blueMiddle, sliderWidth, alwaysBall)
    ).toBe('BALL');
  });

});
