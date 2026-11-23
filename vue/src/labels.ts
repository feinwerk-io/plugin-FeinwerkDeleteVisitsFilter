/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { NumberFormatter, translate } from 'CoreHome';
import { formatLocalDateTime, parseUtc } from './lib/datetime';
import { SiteNames, summarizeSites } from './lib/sites';
import { JobMessage, JobSource, JobStatus } from './types';

const STATUS_KEYS: Record<JobStatus, string> = {
  queued: 'FeinwerkDeleteVisitsFilter_StatusQueued',
  running: 'FeinwerkDeleteVisitsFilter_StatusRunning',
  held: 'FeinwerkDeleteVisitsFilter_StatusHeld',
  done: 'FeinwerkDeleteVisitsFilter_StatusDone',
  cancelled: 'FeinwerkDeleteVisitsFilter_StatusCancelled',
  failed: 'FeinwerkDeleteVisitsFilter_StatusFailed',
};

const SOURCE_KEYS: Record<JobSource, string> = {
  ui: 'FeinwerkDeleteVisitsFilter_SourceUi',
  api: 'FeinwerkDeleteVisitsFilter_SourceApi',
  console: 'FeinwerkDeleteVisitsFilter_SourceConsole',
  rule: 'FeinwerkDeleteVisitsFilter_SourceRule',
};

export function statusLabel(status: JobStatus): string {
  return STATUS_KEYS[status] ? translate(STATUS_KEYS[status]) : status;
}

export function sourceLabel(source: JobSource): string {
  return SOURCE_KEYS[source] ? translate(SOURCE_KEYS[source]) : source;
}

export function formatCount(value: number): string {
  return NumberFormatter.formatNumber(value, 0);
}

export function formatRate(perSecond: number): string {
  return NumberFormatter.formatNumber(perSecond, perSecond < 10 ? 1 : 0);
}

export function sitesText(ids: number[], names: SiteNames, allIds: number[]): string {
  const summary = summarizeSites(ids, names, allIds);
  if (summary.all) {
    return translate('FeinwerkDeleteVisitsFilter_SiteModeAll');
  }
  const text = summary.names.join(', ');
  return summary.more > 0
    ? `${text} ${translate('FeinwerkDeleteVisitsFilter_MoreWebsites', summary.more)}`
    : text;
}

export function dateRangeText(from: string, to: string): string {
  return from === to ? from : translate('FeinwerkDeleteVisitsFilter_DateRangeValue', from, to);
}

/** "Y-m-d H:i:s" in UTC → local date and time of the browser ("2026-11-11 22:57"). */
export function formatUtc(datetime: string | null): string {
  if (!datetime) {
    return '-';
  }
  const ms = parseUtc(datetime);
  return Number.isNaN(ms) ? datetime : formatLocalDateTime(ms);
}

export function messageText(message: JobMessage): string {
  return translate(message.key, ...message.params.map((param) => `${param}`));
}
