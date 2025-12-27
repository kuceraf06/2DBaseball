const {
  createInitialState,
  applyPitchResult,
  applyRun
} = require('../src/js/scoreLogic');

test('Strikeout increases outs and resets count', () => {
  let state = createInitialState();

  state = applyPitchResult(state, 'STRIKE');
  state = applyPitchResult(state, 'STRIKE');
  state = applyPitchResult(state, 'STRIKE');

  expect(state.outs).toBe(1);
  expect(state.strikes).toBe(0);
  expect(state.balls).toBe(0);
});

test('Ball four resets count', () => {
  let state = createInitialState();

  state = applyPitchResult(state, 'BALL');
  state = applyPitchResult(state, 'BALL');
  state = applyPitchResult(state, 'BALL');
  state = applyPitchResult(state, 'BALL');

  expect(state.strikes).toBe(0);
  expect(state.balls).toBe(0);
});

test('Run increases correct team score', () => {
  let state = createInitialState();

  state = applyRun(state, 'B');

  expect(state.teamBScore).toBe(1);
  expect(state.teamAScore).toBe(0);
});
