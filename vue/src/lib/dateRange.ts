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
