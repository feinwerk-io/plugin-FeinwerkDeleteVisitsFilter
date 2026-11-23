/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export interface ProgressSample {
  /** Milliseconds (Date.now()) */
  at: number;
  deleted: number;
}

export function percent(deleted: number, estimated: number | null): number {
  if (!estimated || estimated <= 0) {
    return 0;
  }
  return Math.max(0, Math.min(100, Math.round((deleted / estimated) * 100)));
}

/** Deleted visits per second between the oldest and the newest sample. */
export function rate(samples: ProgressSample[]): number | null {
  if (samples.length < 2) {
    return null;
  }
  const first = samples[0];
  const last = samples[samples.length - 1];
  const seconds = (last.at - first.at) / 1000;
  if (seconds <= 0 || last.deleted <= first.deleted) {
    return null;
  }
  return (last.deleted - first.deleted) / seconds;
}

export function etaSeconds(
  deleted: number,
  estimated: number | null,
  visitsPerSecond: number | null,
): number | null {
  if (!estimated || !visitsPerSecond) {
    return null;
  }
  return Math.max(0, Math.round((estimated - deleted) / visitsPerSecond));
}

export function addSample(
  samples: ProgressSample[],
  sample: ProgressSample,
  keep = 10,
): ProgressSample[] {
  return [...samples, sample].slice(-keep);
}

export function formatDuration(seconds: number): string {
  if (seconds < 60) {
    return `${seconds} s`;
  }
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) {
    return `${minutes} min`;
  }
  return `${Math.floor(minutes / 60)} h ${minutes % 60} min`;
}
