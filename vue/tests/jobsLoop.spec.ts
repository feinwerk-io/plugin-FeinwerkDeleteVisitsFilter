/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { ERROR_RETRY_MS, JobsLoop, JobsLoopDeps } from '../src/lib/jobsLoop';
import { ProgressSample } from '../src/lib/progress';
import { Job, JobStatus, ProcessResult } from '../src/types';

function job(idjob: number, status: JobStatus, deleted = 0): Job {
  return {
    idjob,
    status,
    source: 'ui',
    idrule: null,
    idsites: [1],
    segment: 'visitIp==192.0.2.10',
    date_from: '2026-03-14',
    date_to: '2026-03-14',
    batch_size: 1000,
    max_visits: null,
    estimated_visits: 100,
    deleted_visits: deleted,
    deleted_rows: [],
    batches: 0,
    invalidated_dates: 0,
    site_index: 0,
    sites_total: 1,
    messages: [],
    error: null,
    created_by: 'peter',
    created_at: '2026-10-06 10:00:00',
    started_at: null,
    finished_at: null,
    heartbeat_at: null,
  };
}

const IDLE: ProcessResult = { busy: false, jobs: [] };

type FinalJobs = Record<number, Job | Job[]>;

function setup(
  responses: (ProcessResult | Error)[],
  finalJobs: FinalJobs = {},
  onGetJob: (idJob: number, loop: JobsLoop) => void = () => undefined,
) {
  const log = {
    process: 0,
    waits: [] as number[],
    errorsWhileWaiting: [] as (string | null)[],
    busyWhileWaiting: [] as boolean[],
    sampleKeysWhileWaiting: [] as string[][],
    samplesWhileWaiting: [] as ProgressSample[][],
    finished: [] as Job[],
  };
  let loop: JobsLoop;
  const deps: JobsLoopDeps = {
    processJobs: () => {
      const response = log.process < responses.length ? responses[log.process] : IDLE;
      log.process += 1;
      return response instanceof Error ? Promise.reject(response) : Promise.resolve(response);
    },
    getJob: (idJob) => {
      onGetJob(idJob, loop);
      const entry = finalJobs[idJob];
      return Promise.resolve(Array.isArray(entry) ? entry.shift() as Job : entry);
    },
    cancelJob: (idJob) => Promise.resolve(job(idJob, 'cancelled', 5)),
    finished: (finishedJob) => {
      log.finished.push(finishedJob);
    },
    describeError: (error) => (error as Error).message,
    wait: (ms) => {
      log.waits.push(ms);
      log.errorsWhileWaiting.push(loop.state.error);
      log.busyWhileWaiting.push(loop.state.busy);
      log.sampleKeysWhileWaiting.push(Object.keys(loop.state.samples));
      log.samplesWhileWaiting.push(JSON.parse(JSON.stringify(loop.state.samples[1] || [])));
      return Promise.resolve();
    },
    now: () => log.process * 1000,
  };
  loop = new JobsLoop(deps);
  return { loop, log };
}

describe('JobsLoop', () => {
  it('continues the jobs known from the page load and reports them when they finish', async () => {
    const { loop, log } = setup([{ busy: false, jobs: [job(1, 'running', 50)] }], { 1: job(1, 'done', 100) });
    loop.setActive([job(1, 'running', 10)]);

    await loop.run();

    expect(log.process).toBe(2);
    expect(log.waits).toEqual([]);
    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[1, 'done']]);
    expect(loop.state.active).toEqual([]);
    expect(loop.state.finishedCount).toBe(1);
    expect(loop.state.running).toBe(false);
  });

  it('only watches every 5 s while another process works on the queue', async () => {
    const working: ProcessResult = { busy: true, jobs: [job(1, 'running')] };
    const { loop, log } = setup([working, working], { 1: job(1, 'done') });

    await loop.run();

    expect(log.waits).toEqual([5000, 5000]);
    expect(log.busyWhileWaiting).toEqual([true, true]);
    expect(loop.state.busy).toBe(false);
    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[1, 'done']]);
  });

  it('reports a watched job that finished within a single call', async () => {
    const { loop, log } = setup([], { 7: job(7, 'done', 3) });
    loop.watch(7);

    await loop.run();

    expect(log.finished.map((j) => j.idjob)).toEqual([7]);
  });

  it('shows an error and tries again after 10 s', async () => {
    const { loop, log } = setup([new Error('Gateway timeout')]);

    await loop.run();

    expect(log.waits).toEqual([ERROR_RETRY_MS]);
    expect(log.errorsWhileWaiting).toEqual(['Gateway timeout']);
    expect(loop.state.error).toBeNull();
  });

  it('runs a single loop even if started twice', async () => {
    const { loop, log } = setup([]);

    await Promise.all([loop.run(), loop.run()]);

    expect(log.process).toBe(1);
  });

  it('reports a cancelled job once, even if a late response still lists it as running', async () => {
    const { loop, log } = setup([{ busy: false, jobs: [job(2, 'running')] }]);
    loop.setActive([job(2, 'running')]);

    await loop.cancel(2);
    await loop.run();

    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[2, 'cancelled']]);
    expect(loop.state.active).toEqual([]);
  });

  it('reports a retried job again', async () => {
    const { loop, log } = setup([], { 2: job(2, 'done', 9) });
    loop.setActive([job(2, 'running')]);
    await loop.cancel(2);

    loop.watch(2);
    await loop.run();

    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[2, 'cancelled'], [2, 'done']]);
  });

  it('collects progress samples for the rate while a job is active', async () => {
    const { loop, log } = setup([
      { busy: true, jobs: [job(1, 'running', 40)] },
      { busy: true, jobs: [job(1, 'running', 80)] },
    ], { 1: job(1, 'done', 100) });
    loop.setActive([job(1, 'running', 0)]);

    await loop.run();

    expect(log.samplesWhileWaiting).toEqual([
      [{ at: 0, deleted: 0 }, { at: 1000, deleted: 40 }],
      [{ at: 0, deleted: 0 }, { at: 1000, deleted: 40 }, { at: 2000, deleted: 80 }],
    ]);
    expect(loop.state.samples).toEqual({});
  });

  it('picks up a job watched while the last call was finishing', async () => {
    const { loop, log } = setup(
      [IDLE, { busy: false, jobs: [job(9, 'running')] }],
      { 1: job(1, 'done', 5), 9: job(9, 'done', 7) },
      (idJob, l) => {
        if (idJob === 1) {
          l.watch(9);
          l.run();
        }
      },
    );
    loop.setActive([job(1, 'running')]);

    await loop.run();

    expect(log.process).toBe(3);
    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[1, 'done'], [9, 'done']]);
  });

  it('does not report a watched job that is still queued', async () => {
    const { loop, log } = setup(
      [IDLE, { busy: false, jobs: [job(9, 'running')] }],
      { 9: [job(9, 'queued'), job(9, 'done', 4)] },
    );
    loop.watch(9);

    await loop.run();

    expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[9, 'done']]);
    expect(loop.state.active).toEqual([]);
  });

  it('stops when only held jobs are left and nothing is watched', async () => {
    const { loop, log } = setup([{ busy: false, jobs: [job(4, 'held')] }]);

    await loop.run();

    expect(log.process).toBe(1);
    expect(log.waits).toEqual([]);
    expect(loop.state.active.map((j) => j.idjob)).toEqual([4]);
    expect(loop.state.running).toBe(false);
  });

  it('records no progress samples for queued jobs', async () => {
    const { loop, log } = setup(
      [{ busy: true, jobs: [job(3, 'queued')] }],
      { 3: job(3, 'done') },
    );

    await loop.run();

    expect(log.sampleKeysWhileWaiting).toEqual([[]]);
  });

  describe('show', () => {
    it('adds a queued job to the active list', () => {
      const { loop } = setup([]);

      loop.show(job(5, 'queued'));

      expect(loop.state.active.map((j) => [j.idjob, j.status])).toEqual([[5, 'queued']]);
      expect(Object.keys(loop.state.samples)).toEqual([]);
    });

    it('records samples for a shown running job', () => {
      const { loop } = setup([]);

      loop.show(job(5, 'running', 7));

      expect(loop.state.samples[5].map((sample) => sample.deleted)).toEqual([7]);
    });

    it('does not replace a job that is already active', () => {
      const { loop } = setup([]);
      loop.setActive([job(5, 'running', 40)]);

      loop.show(job(5, 'queued', 0));

      expect(loop.state.active.map((j) => [j.idjob, j.status, j.deleted_visits]))
        .toEqual([[5, 'running', 40]]);
    });

    it('ignores finished jobs and jobs that were already reported', async () => {
      const { loop } = setup([], { 6: job(6, 'done', 3) });
      loop.watch(6);
      await loop.run();

      loop.show(job(6, 'queued'));
      loop.show(job(8, 'done', 3));
      loop.show(job(9, 'failed'));

      expect(loop.state.active).toEqual([]);
    });

    it('reports a shown job once when it later leaves the list', async () => {
      const { loop, log } = setup(
        [{ busy: false, jobs: [job(5, 'running', 20)] }],
        { 5: job(5, 'done', 100) },
      );
      loop.watch(5);
      loop.show(job(5, 'queued'));

      await loop.run();

      expect(log.finished.map((j) => [j.idjob, j.status])).toEqual([[5, 'done']]);
      expect(loop.state.finishedCount).toBe(1);
      expect(loop.state.active).toEqual([]);
    });
  });
});
