/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { formatLocalDateTime, parseUtc } from '../src/lib/datetime';

describe('date and time of a job', () => {
  it('reads the database value as UTC', () => {
    expect(parseUtc('2026-11-11 21:57:34')).toBe(Date.UTC(2026, 10, 11, 21, 57, 34));
    expect(Number.isNaN(parseUtc('not a date'))).toBe(true);
  });

  it('shows the local date and time like the date ranges, without seconds', () => {
    expect(formatLocalDateTime(new Date(2026, 10, 11, 22, 57, 34).getTime())).toBe('2026-11-11 22:57');
    expect(formatLocalDateTime(new Date(2026, 0, 5, 7, 3, 0).getTime())).toBe('2026-01-05 07:03');
  });
});
