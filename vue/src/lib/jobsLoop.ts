/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { reactive } from 'vue';
import { Job, ProcessResult } from '../types';
import { isActive, nextPollDelayMs } from './jobs';
import { addSample, ProgressSample } from './progress';

export const ERROR_RETRY_MS = 10000;

/**
 * A running job whose deleted visits have not changed for this long counts as stalled: a request
 * died while it held the runner lock (a lost database connection, a killed PHP process), and the
 * lock only frees itself after its TTL (MatomoRunnerLock::TTL, 600 s). A normal step deletes one
 * batch in seconds.
 */
export const STALL_MS = 120000;

/**
 * The last time a running job moved, as this page saw it (browser clock); key: see progressKey().
 */
export interface ProgressMark {
  key: string | null;
  at: number;
}

/**
 * What changes while a running job works: deleted visits, steps, the website, and the heartbeat the
 * runner writes after every scanned window (a rare segment scans many windows without deleting).
 * null while the first count runs: it writes nothing until all websites are counted, so a long
 * count never looks stalled.
 */
export function progressKey(job: Job): string | null {
  if (job.estimated_visits === null) {
    return null;
  }
  return [job.deleted_visits, job.batches, job.site_index, job.heartbeat_at].join('|');
}

/** When the job last moved, if that is longer ago than STALL_MS; null while it moves. Pure. */
export function stalledSince(
  mark: ProgressMark | undefined,
  now: number,
  stallMs = STALL_MS,
): number | null {
  if (!mark || now - mark.at < stallMs) {
    return null;
  }
  return mark.at;
}

export interface JobsLoopDeps {
  processJobs(): Promise<ProcessResult>;
  getJob(idJob: number): Promise<Job>;
  cancelJob(idJob: number): Promise<Job>;
  /** Called once for every job that left the active list (done, cancelled or failed). */
  finished(job: Job): void;
  describeError(error: unknown): string;
  wait(ms: number): Promise<void>;
  now(): number;
}

export interface JobsLoopState {
  active: Job[];
  /** Another process holds the runner lock; this page only watches. */
  busy: boolean;
  running: boolean;
  error: string | null;
  samples: Record<number, ProgressSample[]>;
  /** Per running job: its deleted visits and when they last changed (stall detection). */
  progressAt: Record<number, ProgressMark>;
  /** Increases whenever jobs finished – lets the history reload. */
  finishedCount: number;
}

/**
 * Keeps the queue moving while the page is open: calls processJobs until no queued or running job
 * is listed and no started job is still watched (held jobs stay listed but do not keep it going),
 * pausing while another process works or after an error, and reports every job that finished.
 */
export class JobsLoop {
  readonly state = reactive<JobsLoopState>({
    active: [],
    busy: false,
    running: false,
    error: null,
    samples: {},
    progressAt: {},
    finishedCount: 0,
  });

  private watched: number[] = [];

  private reported: number[] = [];

  private readonly deps: JobsLoopDeps;

  constructor(deps: JobsLoopDeps) {
    this.deps = deps;
  }

  /** Jobs known from the page load, e.g. after a reload during a deletion. */
  setActive(jobs: Job[]): void {
    this.state.active = jobs.filter((job) => isActive(job.status));
    this.recordSamples();
  }

  /**
   * Report this job when it finishes, even if it never shows up as active
   * (small deletions, retries).
   */
  watch(idJob: number): void {
    this.reported = this.reported.filter((id) => id !== idJob);
    if (this.watched.indexOf(idJob) === -1) {
      this.watched.push(idJob);
    }
  }

  /**
   * Show a freshly started job at once instead of with the first processJobs response. Never
   * replaces a job that is already listed: the response data is newer.
   */
  show(job: Job): void {
    if (!isActive(job.status)
      || this.reported.indexOf(job.idjob) !== -1
      || this.state.active.some((active) => active.idjob === job.idjob)) {
      return;
    }
    this.state.active = this.state.active.concat([job]);
    this.recordSamples();
  }

  async run(): Promise<void> {
    if (this.state.running) {
      return;
    }
    this.state.running = true;
    try {
      let more = true;
      while (more) {
        // Sequential on purpose: each call works on the queue for up to 15 s.
        // eslint-disable-next-line no-await-in-loop
        more = await this.tick();
      }
    } finally {
      this.state.running = false;
    }
  }

  /** Errors are passed to the caller, which shows them next to the button. */
  async cancel(idJob: number): Promise<void> {
    const job = await this.deps.cancelJob(idJob);
    await this.update(this.state.active.filter((active) => active.idjob !== idJob), [job]);
  }

  /**
   * One processJobs call plus the pause before the next one; false when the response lists no
   * queued or running job and nothing is watched.
   */
  private async tick(): Promise<boolean> {
    let delay: number;
    try {
      const result = await this.deps.processJobs();
      this.state.error = null;
      this.state.busy = result.busy;
      await this.update(result.jobs);
      const working = result.jobs.some((job) => job.status === 'queued' || job.status === 'running');
      if (!working && this.watched.length === 0) {
        return false;
      }
      delay = nextPollDelayMs(result.busy);
    } catch (e) {
      this.state.error = this.deps.describeError(e);
      delay = ERROR_RETRY_MS;
    }
    if (delay > 0) {
      await this.deps.wait(delay);
    }
    return true;
  }

  private async update(jobs: Job[], known: Job[] = []): Promise<void> {
    const active = jobs.filter(
      (job) => isActive(job.status) && this.reported.indexOf(job.idjob) === -1,
    );
    const activeIds = active.map((job) => job.idjob);
    const candidates = this.state.active.map((job) => job.idjob).concat(this.watched);
    const gone = candidates.filter((id, index) => candidates.indexOf(id) === index
      && activeIds.indexOf(id) === -1
      && this.reported.indexOf(id) === -1);

    this.state.active = active;
    this.watched = this.watched.filter((id) => activeIds.indexOf(id) !== -1);
    this.recordSamples();
    if (gone.length === 0) {
      return;
    }

    // Marked before awaiting so that overlapping calls (cancel and tick) report a job only once.
    this.reported = this.reported.concat(gone);
    const finalJobs = await Promise.all(gone.map((id) => {
      const knownJob = known.find((candidate) => candidate.idjob === id);
      return knownJob ? Promise.resolve(knownJob) : this.deps.getJob(id).catch(() => null);
    }));
    let finishedAny = false;
    finalJobs.forEach((job, index) => {
      if (job && isActive(job.status)) {
        // Not finished: the response predates the job (or its start). Keep following it.
        this.reported = this.reported.filter((id) => id !== gone[index]);
        if (this.watched.indexOf(gone[index]) === -1) {
          this.watched.push(gone[index]);
        }
      } else if (job) {
        this.deps.finished(job);
        finishedAny = true;
      }
    });
    if (finishedAny) {
      this.state.finishedCount += 1;
    }
  }

  private recordSamples(): void {
    const samples: Record<number, ProgressSample[]> = {};
    const progressAt: Record<number, ProgressMark> = {};
    const now = this.deps.now();
    this.state.active.filter((job) => job.status === 'running').forEach((job) => {
      samples[job.idjob] = addSample(
        this.state.samples[job.idjob] || [],
        { at: now, deleted: job.deleted_visits },
      );
      const mark = this.state.progressAt[job.idjob];
      const key = progressKey(job);
      progressAt[job.idjob] = mark && key !== null && mark.key === key ? mark : { key, at: now };
    });
    this.state.samples = samples;
    this.state.progressAt = progressAt;
  }
}
