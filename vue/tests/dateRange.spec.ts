/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import {
  DatePreset, EARLIEST_DAY, isValidDay, RANGE_FROM_MARK, RANGE_TO_MARK, rangeForPreset, rangeParts, rangeProblem,
  toIsoDay,
} from '../src/lib/dateRange';

describe('rangeForPreset', () => {
  const today = new Date(2026, 9, 6); // 2026-10-06, local time
  const cases: [DatePreset, string, string][] = [
    ['today', '2026-10-06', '2026-10-06'],
    ['yesterday', '2026-10-05', '2026-10-05'],
    ['last7', '2026-09-30', '2026-10-06'],
    ['last30', '2026-09-07', '2026-10-06'],
    ['lastMonth', '2026-09-01', '2026-09-30'],
    ['thisYear', '2026-01-01', '2026-10-06'],
    ['lastYear', '2025-01-01', '2025-12-31'],
    ['all', EARLIEST_DAY, '2026-10-06'],
  ];

  it.each(cases)('%s', (preset, from, to) => {
    expect(rangeForPreset(preset, today)).toEqual({ from, to });
  });

  it('returns null for custom', () => {
    expect(rangeForPreset('custom', today)).toBeNull();
  });

  it('handles last month in January', () => {
    expect(rangeForPreset('lastMonth', new Date(2026, 0, 15)))
      .toEqual({ from: '2025-12-01', to: '2025-12-31' });
  });

  it('handles February in a leap year', () => {
    expect(rangeForPreset('lastMonth', new Date(2028, 2, 3)))
      .toEqual({ from: '2028-02-01', to: '2028-02-29' });
  });
});

describe('isValidDay / toIsoDay', () => {
  it('accepts real days only', () => {
    expect(isValidDay('2026-02-28')).toBe(true);
    expect(isValidDay('2026-02-30')).toBe(false);
    expect(isValidDay('2026-2-3')).toBe(false);
    expect(isValidDay('')).toBe(false);
  });

  it('pads month and day', () => {
    expect(toIsoDay(new Date(2026, 0, 5))).toBe('2026-01-05');
  });
});

describe('rangeProblem', () => {
  it('names the first invalid day', () => {
    expect(rangeProblem({ from: '2026-13-01', to: '2026-10-06' }))
      .toEqual({ key: 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate', value: '2026-13-01' });
    expect(rangeProblem({ from: '2026-10-01', to: 'tomorrow' }))
      .toEqual({ key: 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate', value: 'tomorrow' });
  });

  it('rejects a reversed range and accepts a valid one', () => {
    expect(rangeProblem({ from: '2026-10-06', to: '2026-10-01' }))
      .toEqual({ key: 'FeinwerkDeleteVisitsFilter_ErrorDateOrder', value: '' });
    expect(rangeProblem({ from: '2026-10-01', to: '2026-10-01' })).toBeNull();
  });
});

describe('rangeParts', () => {
  const F = RANGE_FROM_MARK;
  const T = RANGE_TO_MARK;

  it('keeps each date as one piece between the translated words', () => {
    expect(rangeParts(`${F} to ${T}`, '2026-10-25', '2026-11-23')).toEqual([
      { text: '2026-10-25', date: true },
      { text: ' to ', date: false },
      { text: '2026-11-23', date: true },
    ]);
    expect(rangeParts(`du ${F} au ${T}`, '2026-10-25', '2026-11-23')).toEqual([
      { text: 'du ', date: false },
      { text: '2026-10-25', date: true },
      { text: ' au ', date: false },
      { text: '2026-11-23', date: true },
    ]);
  });

  it('follows a translation that puts the end first', () => {
    expect(rangeParts(`${T} (${F})`, 'a', 'b')).toEqual([
      { text: 'b', date: true },
      { text: ' (', date: false },
      { text: 'a', date: true },
      { text: ')', date: false },
    ]);
  });

  it('shows a single day once, and an unexpected translation as one piece', () => {
    expect(rangeParts(`${F} to ${T}`, '2026-11-23', '2026-11-23')).toEqual([{ text: '2026-11-23', date: true }]);
    expect(rangeParts('range', 'a', 'b')).toEqual([{ text: 'range', date: false }]);
  });
});
