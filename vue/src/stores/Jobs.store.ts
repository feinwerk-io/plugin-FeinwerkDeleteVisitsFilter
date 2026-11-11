/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { Matomo, NotificationsStore, translate } from 'CoreHome';
import {
  cancelJob, errorMessage, getJob, processJobs,
} from '../api';
import { formatCount } from '../labels';
import { JobsLoop } from '../lib/jobsLoop';
import { Job } from '../types';

function notifyFinished(job: Job): void {
  let message: string;
  let context: 'success' | 'error' | 'info';
  if (job.status === 'done') {
    message = translate('FeinwerkDeleteVisitsFilter_JobFinished', job.idjob, formatCount(job.deleted_visits));
    context = 'success';
  } else if (job.status === 'failed') {
    message = translate(
      'FeinwerkDeleteVisitsFilter_ErrorJobFailed',
      job.idjob,
      Matomo.helper.htmlEntities(job.error || ''),
    );
    context = 'error';
  } else {
    message = translate('FeinwerkDeleteVisitsFilter_ErrorJobCancelled', job.idjob);
    context = 'info';
  }
  NotificationsStore.show({
    message, context, id: `fwdvf-job-${job.idjob}`, type: 'transient',
  });
}

export default new JobsLoop({
  processJobs,
  getJob,
  cancelJob,
  finished: notifyFinished,
  describeError: errorMessage,
  wait: (ms) => new Promise((resolve) => {
    setTimeout(resolve, ms);
  }),
  now: () => Date.now(),
});
