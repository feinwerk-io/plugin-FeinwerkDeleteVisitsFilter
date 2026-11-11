/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { AjaxHelper, Matomo, translate } from 'CoreHome';
import { isTransportErrorMessage } from './lib/errors';
import { StoredSegment } from './lib/segment';
import { Job, Preview, ProcessResult } from './types';

// abortable: false – a hash change (tab switch, visitor profile) must not cancel a request: Matomo
// never settles aborted requests. createErrorNotification: false – the page shows errors itself.
const OPTIONS = { abortable: false, createErrorNotification: false };

// All parameters go into the POST body. For `segment` this also stops AjaxHelper from adding the
// page's (empty) segment to the URL, and long segments are not limited by the URL length.
function call<R>(method: string, postParams: QueryParameters = {}): Promise<R> {
  return AjaxHelper.post<R>({ method: `FeinwerkDeleteVisitsFilter.${method}` }, postParams, OPTIONS);
}

// Matomo's API HTML-escapes exception messages; callers escape again where needed (Vue
// interpolation, NotificationsStore), so decode here to avoid double escaping. AjaxHelper's fixed
// English transport failure texts are replaced by a translated one.
export function errorMessage(error: unknown): string {
  if (error instanceof Error && error.message && !isTransportErrorMessage(error.message)) {
    return Matomo.helper.htmlDecode(error.message);
  }
  return translate('FeinwerkDeleteVisitsFilter_RequestFailed');
}

export function previewDeletion(
  idSites: string,
  segment: string,
  dateFrom: string,
  dateTo: string,
): Promise<Preview> {
  return call<Preview>('previewDeletion', {
    idSites, segment, dateFrom, dateTo, sampleSize: 20,
  });
}

export function createJob(
  idSites: string,
  segment: string,
  dateFrom: string,
  dateTo: string,
  batchSize: string,
): Promise<{ idjob: number }> {
  return call<{ idjob: number }>('createJob', {
    idSites, segment, dateFrom, dateTo, batchSize, source: 'ui',
  });
}

export function processJobs(): Promise<ProcessResult> {
  return call<ProcessResult>('processJobs');
}

export function getJob(idJob: number): Promise<Job> {
  return call<Job>('getJob', { idJob });
}

export function getJobs(
  offset: number,
  limit: number,
  status: string,
  source: string,
): Promise<Job[]> {
  return call<Job[]>('getJobs', {
    offset, limit, status, source,
  });
}

export function cancelJob(idJob: number): Promise<Job> {
  return call<Job>('cancelJob', { idJob });
}

export function retryJob(idJob: number): Promise<Job> {
  return call<Job>('retryJob', { idJob });
}

export function storedSegments(idSite: number): Promise<StoredSegment[]> {
  return AjaxHelper.post<StoredSegment[]>({ method: 'SegmentEditor.getAll' }, { idSite }, OPTIONS);
}
