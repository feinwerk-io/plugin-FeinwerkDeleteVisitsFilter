/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export type JobStatus = 'queued' | 'running' | 'held' | 'done' | 'cancelled' | 'failed';
export type JobSource = 'ui' | 'api' | 'console' | 'rule';

export interface JobMessage {
  key: string;
  params: (string | number)[];
}

/** As returned by Job::toApiArray(). */
export interface Job {
  idjob: number;
  status: JobStatus;
  source: JobSource;
  idrule: number | null;
  idsites: number[];
  segment: string;
  date_from: string;
  date_to: string;
  batch_size: number;
  max_visits: number | null;
  estimated_visits: number | null;
  deleted_visits: number;
  /** PHP sends [] for an empty map – read it through normalizeRows(). */
  deleted_rows: unknown;
  batches: number;
  invalidated_dates: number;
  /** Index (from 0) of the website currently processed. */
  site_index: number;
  sites_total: number;
  messages: JobMessage[];
  error: string | null;
  created_by: string | null;
  created_at: string;
  started_at: string | null;
  finished_at: string | null;
  heartbeat_at: string | null;
}

export interface ProcessResult {
  busy: boolean;
  jobs: Job[];
}

export interface PreviewSite {
  idsite: number;
  name: string;
  count: number;
}

export interface SampleVisit {
  idsite: number;
  idvisit: number;
  visitor_id: string;
  /** Last action in the website's timezone. */
  time: string;
  ip: string;
  referrer_type: number;
  referrer_name: string;
  browser: string;
  os: string;
  device_type: number | null;
  actions: number;
  entry_url: string;
}

export interface Preview {
  total: number;
  action_level: boolean;
  sites: PreviewSite[];
  sample: SampleVisit[];
}

export interface JobTemplate {
  idJob: number;
  idSites: number[];
  segment: string;
  dateFrom: string;
  dateTo: string;
}
