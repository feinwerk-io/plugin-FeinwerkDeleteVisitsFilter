/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import {
  addSample, etaSeconds, formatDuration, percent, ProgressSample, rate,
} from '../src/lib/progress';

describe('progress', () => {
  it('computes a bounded percentage', () => {
    expect(percent(50, 200)).toBe(25);
    expect(percent(300, 200)).toBe(100);
    expect(percent(10, null)).toBe(0);
    expect(percent(10, 0)).toBe(0);
  });

  it('computes the rate from the oldest and newest sample', () => {
    expect(rate([{ at: 0, deleted: 0 }])).toBeNull();
    expect(rate([{ at: 0, deleted: 0 }, { at: 10000, deleted: 500 }])).toBe(50);
    expect(rate([{ at: 0, deleted: 100 }, { at: 10000, deleted: 100 }])).toBeNull();
  });

  it('estimates the remaining time', () => {
    expect(etaSeconds(500, 1500, 50)).toBe(20);
    expect(etaSeconds(500, null, 50)).toBeNull();
    expect(etaSeconds(500, 1500, null)).toBeNull();
    expect(etaSeconds(2000, 1500, 50)).toBe(0);
  });

  it('keeps the most recent samples', () => {
    let samples: ProgressSample[] = [];
    for (let i = 0; i < 15; i += 1) {
      samples = addSample(samples, { at: i, deleted: i }, 10);
    }
    expect(samples.length).toBe(10);
    expect(samples[0].at).toBe(5);
  });

  it('formats durations', () => {
    expect(formatDuration(42)).toBe('42 s');
    expect(formatDuration(125)).toBe('2 min');
    expect(formatDuration(3725)).toBe('1 h 2 min');
  });
});
