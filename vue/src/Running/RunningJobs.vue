<template>
  <div class="fwdvf-running">
    <ContentBlock
      v-if="jobs.length"
      :content-title="translate('FeinwerkDeleteVisitsFilter_RunningTitle')"
    >
      <div v-for="job in jobs" :key="job.idjob" class="fwdvf-running-job">
        <h3 class="fwdvf-running-heading">
          {{ translate('FeinwerkDeleteVisitsFilter_RunningJob', job.idjob) }}
          <span :class="`fwdvf-status fwdvf-status-${job.status}`">
            {{ statusLabel(job.status) }}
          </span>
        </h3>
        <p class="fwdvf-muted">
          {{ sitesText(job.idsites, siteNames, allIds) }} ·
          {{ dateRangeText(job.date_from, job.date_to) }}
        </p>
        <p v-if="job.segment" class="fwdvf-muted">
          <code class="fwdvf-code fwdvf-segment-cell" :title="job.segment">{{ job.segment }}</code>
        </p>
        <Progressbar :progress="progressOf(job)" :label="labelOf(job)" />
        <p v-if="detailsOf(job)" class="fwdvf-muted">{{ detailsOf(job) }}</p>
        <Alert v-if="job.status === 'held'" severity="warning">
          {{ translate('FeinwerkDeleteVisitsFilter_HeldHint') }}
        </Alert>
        <Alert v-if="stalledText(job)" severity="warning">{{ stalledText(job) }}</Alert>
        <button
          type="button"
          class="btn-flat fwdvf-cancel"
          :disabled="cancelling.indexOf(job.idjob) !== -1"
          @click="askCancel(job.idjob)"
        >
          {{ translate('FeinwerkDeleteVisitsFilter_CancelJob') }}
        </button>
      </div>
      <Alert v-if="state.error" severity="danger">{{ connectionErrorText }}</Alert>
      <p v-if="state.busy && !anyStalled" class="fwdvf-hint">
        {{ translate('FeinwerkDeleteVisitsFilter_BusyHint') }}
      </p>
      <p class="fwdvf-hint">{{ backgroundHint }}</p>
    </ContentBlock>
    <MatomoDialog
      v-model="confirmOpen"
      :options="{ focusSelector: '.modal.open .modal-footer a:last-child' }"
      @yes="cancel()"
    >
      <div class="ui-confirm">
        <h2>{{ translate('FeinwerkDeleteVisitsFilter_CancelJobConfirm') }}</h2>
        <!-- As in the delete and retry dialogs: the action first, the safe choice last -->
        <!-- and focused (Matomo 5.1 focuses the last footer link, later versions the -->
        <!-- focusSelector). -->
        <!-- role names the MatomoDialog button (yes/no), not an ARIA role. -->
        <!-- eslint-disable vuejs-accessibility/aria-role -->
        <input
          type="button"
          role="yes"
          :value="translate('FeinwerkDeleteVisitsFilter_ConfirmCancelYes')"
        />
        <input
          type="button"
          role="no"
          :value="translate('FeinwerkDeleteVisitsFilter_ConfirmCancelNo')"
        />
        <!-- eslint-enable vuejs-accessibility/aria-role -->
      </div>
    </MatomoDialog>
  </div>
</template>

<script lang="ts">
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { defineComponent, PropType } from 'vue';
import {
  Alert, ContentBlock, Matomo, MatomoDialog, NotificationsStore, Progressbar, translate,
} from 'CoreHome';
import { errorMessage } from '../api';
import {
  dateRangeText, formatCount, formatRate, sitesText, statusLabel,
} from '../labels';
import { formatLocalDateTime } from '../lib/datetime';
import { JobsLoopState, stalledSince } from '../lib/jobsLoop';
import {
  etaSeconds, formatDuration, percent, rate,
} from '../lib/progress';
import { allSiteIds, SiteNames } from '../lib/sites';
import JobsStore from '../stores/Jobs.store';
import { Job } from '../types';

export interface RunningJobsData {
  confirmOpen: boolean;
  cancelTarget: number | null;
  cancelling: number[];
}

export default defineComponent({
  props: {
    siteNames: { type: Object as PropType<SiteNames>, required: true },
    backgroundBudget: { type: Number, required: true },
  },
  components: {
    Alert, ContentBlock, MatomoDialog, Progressbar,
  },
  data(): RunningJobsData {
    return { confirmOpen: false, cancelTarget: null, cancelling: [] };
  },
  computed: {
    state(): JobsLoopState {
      return JobsStore.state;
    },
    jobs(): Job[] {
      return JobsStore.state.active;
    },
    allIds(): number[] {
      return allSiteIds(this.siteNames);
    },
    /** The generic transport text needs no "(...)" inside the retry sentence. */
    connectionErrorText(): string {
      const error = JobsStore.state.error || '';
      return error === translate('FeinwerkDeleteVisitsFilter_RequestFailed')
        ? translate('FeinwerkDeleteVisitsFilter_ConnectionErrorGeneric')
        : translate('FeinwerkDeleteVisitsFilter_ConnectionError', error);
    },
    /**
     * A stalled job replaces the "another process is working" hint, which would not be true then.
     */
    anyStalled(): boolean {
      return this.jobs.some((job) => this.stalledText(job) !== '');
    },
    backgroundHint(): string {
      return this.backgroundBudget > 0
        ? translate('FeinwerkDeleteVisitsFilter_BackgroundHint')
        : translate('FeinwerkDeleteVisitsFilter_BackgroundOffHint', './console feinwerkdeletevisitsfilter:work');
    },
  },
  methods: {
    statusLabel,
    sitesText,
    dateRangeText,
    /**
     * No progress for a while: a step died while it held the runner lock, which frees itself after
     * 10 minutes. Says since when, that the deletion goes on by itself and what the user can do.
     * The state's samples change with every processJobs response, so this is evaluated again on
     * each poll.
     */
    stalledText(job: Job): string {
      if (job.status !== 'running') {
        return '';
      }
      const since = stalledSince(JobsStore.state.progressAt[job.idjob], Date.now());
      return since === null
        ? ''
        : translate('FeinwerkDeleteVisitsFilter_StalledHint', formatLocalDateTime(since));
    },
    progressOf(job: Job): number {
      return percent(job.deleted_visits, job.estimated_visits);
    },
    labelOf(job: Job): string {
      if (job.status === 'queued') {
        return translate('FeinwerkDeleteVisitsFilter_StatusQueued');
      }
      if (job.estimated_visits === null) {
        return job.deleted_visits > 0
          ? translate('FeinwerkDeleteVisitsFilter_ProgressLabelNoEstimate', formatCount(job.deleted_visits))
          : translate('FeinwerkDeleteVisitsFilter_CountingVisits');
      }
      return translate(
        'FeinwerkDeleteVisitsFilter_ProgressLabel',
        formatCount(job.deleted_visits),
        formatCount(job.estimated_visits),
      );
    },
    detailsOf(job: Job): string {
      const parts: string[] = [];
      if (job.sites_total > 1) {
        const index = Math.min(job.site_index, job.sites_total - 1);
        const idSite = job.idsites[index];
        parts.push(translate(
          'FeinwerkDeleteVisitsFilter_ProgressSiteName',
          index + 1,
          job.sites_total,
          this.siteNames[idSite] ?? `#${idSite}`,
        ));
      }
      const perSecond = rate(JobsStore.state.samples[job.idjob] || []);
      if (perSecond !== null) {
        parts.push(translate('FeinwerkDeleteVisitsFilter_ProgressRate', formatRate(perSecond)));
        const eta = etaSeconds(job.deleted_visits, job.estimated_visits, perSecond);
        if (eta !== null) {
          parts.push(translate('FeinwerkDeleteVisitsFilter_ProgressRemaining', formatDuration(eta)));
        }
      }
      return parts.join(' · ');
    },
    askCancel(idJob: number): void {
      this.cancelTarget = idJob;
      this.confirmOpen = true;
    },
    async cancel(): Promise<void> {
      const idJob = this.cancelTarget;
      if (idJob === null) {
        return;
      }
      this.cancelling.push(idJob);
      try {
        await JobsStore.cancel(idJob);
      } catch (e) {
        NotificationsStore.show({
          message: Matomo.helper.htmlEntities(errorMessage(e)),
          context: 'error',
          id: 'fwdvf-cancel',
          type: 'transient',
        });
      } finally {
        this.cancelling = this.cancelling.filter((id) => id !== idJob);
      }
    },
  },
});
</script>
