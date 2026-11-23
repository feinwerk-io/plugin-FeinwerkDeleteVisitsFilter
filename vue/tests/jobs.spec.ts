/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import {
  durationSeconds, isActive, isValidBatchSize, nextPollDelayMs, normalizeRows,
} from '../src/lib/jobs';

describe('jobs', () => {
  it('knows the active statuses', () => {
    expect(['queued', 'running', 'held'].every(isActive)).toBe(true);
    expect(['done', 'cancelled', 'failed'].some(isActive)).toBe(false);
  });

  it('normalizes an empty PHP map', () => {
    expect(normalizeRows([])).toEqual({});
    expect(normalizeRows(null)).toEqual({});
    expect(normalizeRows({ log_visit: 3 })).toEqual({ log_visit: 3 });
  });

  it('computes durations from UTC timestamps', () => {
    expect(durationSeconds('2026-10-06 10:00:00', '2026-10-06 10:02:05', 0)).toBe(125);
    expect(durationSeconds('2026-10-06 10:00:00', null, Date.UTC(2026, 9, 6, 10, 0, 30))).toBe(30);
    expect(durationSeconds(null, null, 0)).toBeNull();
  });

  it('polls at once while this page works and every 5 s while another process works', () => {
    expect(nextPollDelayMs(false)).toBe(0);
    expect(nextPollDelayMs(true)).toBe(5000);
  });

  it('accepts an empty batch size (default) or a whole number from 100 to 10000', () => {
    expect(['', ' ', '100', '10000', ' 2500 '].every(isValidBatchSize)).toBe(true);
    expect(['99', '10001', '1e3', '12.5', 'abc', '-500'].some(isValidBatchSize)).toBe(false);
  });
});
