/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export const ACTIVE_STATUSES = ['queued', 'running', 'held'];

/** Same limits as the server (Input/BatchSize). */
export const BATCH_SIZE_MIN = 100;
export const BATCH_SIZE_MAX = 10000;

export function isActive(status: string): boolean {
  return ACTIVE_STATUSES.indexOf(status) !== -1;
}

/** PHP encodes an empty associative array as [] – the UI always wants an object. */
export function normalizeRows(rows: unknown): Record<string, number> {
  if (!rows || Array.isArray(rows) || typeof rows !== 'object') {
    return {};
  }
  return rows as Record<string, number>;
}

function parseUtc(datetime: string): number {
  return Date.parse(`${datetime.replace(' ', 'T')}Z`);
}

/** Seconds between two "Y-m-d H:i:s" UTC timestamps (until now while the job runs). */
export function durationSeconds(
  startedAt: string | null,
  finishedAt: string | null,
  nowMs: number,
): number | null {
  if (!startedAt) {
    return null;
  }
  const start = parseUtc(startedAt);
  const end = finishedAt ? parseUtc(finishedAt) : nowMs;
  if (Number.isNaN(start) || Number.isNaN(end)) {
    return null;
  }
  return Math.max(0, Math.round((end - start) / 1000));
}

/**
 * Pause before the next processJobs call: none while this page holds the runner lock, 5 s while
 * another process (console, hourly task, other tab) works on the queue.
 */
export function nextPollDelayMs(busy: boolean): number {
  return busy ? 5000 : 0;
}

/** Empty means "use the default". */
export function isValidBatchSize(value: string): boolean {
  const trimmed = value.trim();
  if (trimmed === '') {
    return true;
  }
  if (!/^\d+$/.test(trimmed)) {
    return false;
  }
  const size = parseInt(trimmed, 10);
  return size >= BATCH_SIZE_MIN && size <= BATCH_SIZE_MAX;
}
