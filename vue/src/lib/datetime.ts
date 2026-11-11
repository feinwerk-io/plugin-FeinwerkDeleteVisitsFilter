/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

const pad = (n: number): string => (n < 10 ? `0${n}` : `${n}`);

/** Milliseconds of a "Y-m-d H:i:s" UTC value from the database, NaN when it cannot be read. */
export function parseUtc(datetime: string): number {
  return Date.parse(`${datetime.trim().replace(' ', 'T')}Z`);
}

/**
 * "2026-11-11 22:57" in the browser's time zone: the date form of the page's date ranges,
 * without seconds, so the column stays narrow.
 */
export function formatLocalDateTime(ms: number): string {
  const d = new Date(ms);
  const date = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  return `${date} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
