/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export type DatePreset = 'today' | 'yesterday' | 'last7' | 'last30' | 'lastMonth'
  | 'thisYear' | 'lastYear' | 'all' | 'custom';

export const DATE_PRESETS: DatePreset[] = [
  'today', 'yesterday', 'last7', 'last30', 'lastMonth', 'thisYear', 'lastYear', 'all', 'custom',
];

/** Matches the server's earliest allowed day (Input/DateRange). */
export const EARLIEST_DAY = '1992-01-01';

export interface DayRange {
  from: string;
  to: string;
}

export interface DateSelection extends DayRange {
  preset: DatePreset;
}

export interface RangeProblem {
  key: string;
  value: string;
}

function pad(value: number): string {
  return value < 10 ? `0${value}` : `${value}`;
}

export function toIsoDay(date: Date): string {
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

function shift(date: Date, days: number): Date {
  return new Date(date.getFullYear(), date.getMonth(), date.getDate() + days);
}

/**
 * Concrete calendar days for a preset, based on the browser's "today".
 * Returns null for 'custom' – the caller keeps its own range.
 */
export function rangeForPreset(preset: DatePreset, today: Date): DayRange | null {
  const year = today.getFullYear();
  switch (preset) {
    case 'today':
      return { from: toIsoDay(today), to: toIsoDay(today) };
    case 'yesterday': {
      const day = toIsoDay(shift(today, -1));
      return { from: day, to: day };
    }
    case 'last7':
      return { from: toIsoDay(shift(today, -6)), to: toIsoDay(today) };
    case 'last30':
      return { from: toIsoDay(shift(today, -29)), to: toIsoDay(today) };
    case 'lastMonth':
      return {
        from: toIsoDay(new Date(year, today.getMonth() - 1, 1)),
        to: toIsoDay(new Date(year, today.getMonth(), 0)),
      };
    case 'thisYear':
      return { from: `${year}-01-01`, to: toIsoDay(today) };
    case 'lastYear':
      return { from: `${year - 1}-01-01`, to: `${year - 1}-12-31` };
    case 'all':
      return { from: EARLIEST_DAY, to: toIsoDay(today) };
    default:
      return null;
  }
}

export function isValidDay(value: string): boolean {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
    return false;
  }
  const [year, month, day] = value.split('-').map(Number);
  const date = new Date(year, month - 1, day);
  return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day;
}

/** First problem of a range as a translation key (+ value for the placeholder), or null. */
export function rangeProblem(range: DayRange): RangeProblem | null {
  const invalid = [range.from, range.to].filter((day) => !isValidDay(day));
  if (invalid.length > 0) {
    return { key: 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate', value: invalid[0] };
  }
  if (range.from > range.to) {
    return { key: 'FeinwerkDeleteVisitsFilter_ErrorDateOrder', value: '' };
  }
  return null;
}

/**
 * One piece of a translated date range: a date (kept on one line) or the words between the dates.
 */
export interface RangePart {
  text: string;
  date: boolean;
}

export const RANGE_FROM_MARK = '\u0001';
export const RANGE_TO_MARK = '\u0002';

/**
 * Splits a date range translated with the two marks instead of the dates ("\u0001 to \u0002", "du
 * \u0001 au \u0002") into its pieces, so each date can stay on one line: a table cell would
 * otherwise break inside a date at its hyphen. Without the marks (an unexpected translation) the
 * whole text stays one piece.
 */
export function rangeParts(marked: string, from: string, to: string): RangePart[] {
  if (from === to) {
    return [{ text: from, date: true }];
  }
  const parts: RangePart[] = [];
  let rest = marked;
  while (rest !== '') {
    const fromAt = rest.indexOf(RANGE_FROM_MARK);
    const toAt = rest.indexOf(RANGE_TO_MARK);
    const at = [fromAt, toAt].filter((i) => i !== -1).sort((a, b) => a - b)[0];
    if (at === undefined) {
      parts.push({ text: rest, date: false });
      break;
    }
    if (at > 0) {
      parts.push({ text: rest.slice(0, at), date: false });
    }
    parts.push({ text: at === fromAt ? from : to, date: true });
    rest = rest.slice(at + 1);
  }

  return parts;
}
