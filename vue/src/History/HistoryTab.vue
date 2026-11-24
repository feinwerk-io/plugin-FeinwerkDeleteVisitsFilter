<template>
  <div class="fwdvf-history">
    <ContentBlock :content-title="translate('FeinwerkDeleteVisitsFilter_HistoryTitle')">
      <div class="fwdvf-history-filters">
        <Field
          uicontrol="select"
          name="fwdvf-filter-status"
          :title="translate('FeinwerkDeleteVisitsFilter_FilterStatus')"
          :model-value="status"
          :options="statusOptions"
          @update:model-value="status = $event; reload(0)"
        />
        <Field
          uicontrol="select"
          name="fwdvf-filter-source"
          :title="translate('FeinwerkDeleteVisitsFilter_FilterSource')"
          :model-value="source"
          :options="sourceOptions"
          @update:model-value="source = $event; reload(0)"
        />
        <button type="button" class="btn-flat" @click="reload(offset)">
          {{ translate('FeinwerkDeleteVisitsFilter_Refresh') }}
        </button>
      </div>
      <ActivityIndicator :loading="loading" />
      <Alert v-if="error" severity="danger">{{ error }}</Alert>
      <p v-if="!loading && !error && jobs.length === 0 && !filtered">
        {{ translate('FeinwerkDeleteVisitsFilter_HistoryEmpty') }}
      </p>
      <p v-if="!loading && !error && jobs.length === 0 && filtered" class="fwdvf-history-nomatch">
        {{ translate('FeinwerkDeleteVisitsFilter_HistoryNoMatch') }}
        <button type="button" class="btn-flat" @click="showAll()">
          {{ translate('FeinwerkDeleteVisitsFilter_FilterShowAll') }}
        </button>
      </p>
      <div v-if="jobs.length" class="fwdvf-table-scroll">
        <table class="entityTable dataTable fwdvf-table">
          <thead>
            <tr>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnCreated') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnWebsites') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnDateRange') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnStatus') }}</th>
              <th class="fwdvf-num">{{ translate('FeinwerkDeleteVisitsFilter_ColumnDeleted') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnActions') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_ColumnSegment') }}</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="job in jobs" :key="job.idjob">
              <tr>
                <!-- who started it under the time: outcome columns stay in view on narrow cards -->
                <td :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnCreated')">
                  <span class="fwdvf-nowrap">{{ formatUtc(job.created_at) }}</span>
                  <span class="fwdvf-sub" :title="translate('FeinwerkDeleteVisitsFilter_ColumnBy')">
                    {{ byText(job) }}
                  </span>
                </td>
                <td
                  class="fwdvf-sites-cell"
                  :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnWebsites')"
                >{{ sitesText(job.idsites, siteNames, allIds) }}</td>
                <td
                  class="fwdvf-range-cell"
                  :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnDateRange')"
                >
                  <template
                    v-for="(part, index) in dateRangeParts(job.date_from, job.date_to)"
                    :key="index"
                  >
                    <span v-if="part.date" class="fwdvf-nowrap">{{ part.text }}</span>
                    <template v-else>{{ part.text }}</template>
                  </template>
                </td>
                <td :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnStatus')">
                  <span :class="`fwdvf-status fwdvf-status-${job.status}`">
                    {{ statusLabel(job.status) }}
                  </span>
                </td>
                <td
                  class="fwdvf-num"
                  :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnDeleted')"
                >
                  <span class="fwdvf-nowrap">{{ formatCount(job.deleted_visits) }}</span>
                  <span
                    v-if="durationText(job) !== '-'"
                    class="fwdvf-sub fwdvf-nowrap"
                    :title="translate('FeinwerkDeleteVisitsFilter_ColumnDuration')"
                  >{{ durationText(job) }}</span>
                </td>
                <td
                  class="fwdvf-actions"
                  :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnActions')"
                >
                  <!-- eslint-disable-next-line vuejs-accessibility/click-events-have-key-events -->
                  <a
                    href=""
                    :aria-expanded="isExpanded(job.idjob) ? 'true' : 'false'"
                    @click.prevent="toggle(job.idjob)"
                  >
                    {{ translate('FeinwerkDeleteVisitsFilter_Details') }}
                  </a>
                  <!-- eslint-disable-next-line vuejs-accessibility/click-events-have-key-events -->
                  <a href="" @click.prevent="useTemplate(job)">
                    {{ translate('FeinwerkDeleteVisitsFilter_UseAsTemplate') }}
                  </a>
                  <!-- eslint-disable-next-line vuejs-accessibility/click-events-have-key-events -->
                  <a
                    v-if="job.status === 'failed' && retrying.indexOf(job.idjob) === -1"
                    href=""
                    @click.prevent="askRetry(job)"
                  >
                    {{ translate('FeinwerkDeleteVisitsFilter_RetryJob') }}
                  </a>
                </td>
                <td :data-label="translate('FeinwerkDeleteVisitsFilter_ColumnSegment')">
                  <code class="fwdvf-code fwdvf-segment-cell" :title="job.segment">
                    {{ job.segment }}
                  </code>
                </td>
              </tr>
              <tr v-if="expanded.indexOf(job.idjob) !== -1" class="fwdvf-details">
                <td colspan="7">
                  <p v-if="job.segment">
                    <strong>
                      {{ translate('FeinwerkDeleteVisitsFilter_ConfirmSegmentLabel') }}
                    </strong>
                    <code class="fwdvf-code">{{ job.segment }}</code>
                  </p>
                  <p>
                    {{ translate(
                      'FeinwerkDeleteVisitsFilter_DetailsSteps',
                      formatCount(job.batches),
                      formatCount(job.invalidated_dates),
                      formatCount(job.batch_size),
                    ) }}
                  </p>
                  <template v-if="rowsOf(job).length">
                    <strong>{{ translate('FeinwerkDeleteVisitsFilter_DeletedRows') }}</strong>
                    <ul>
                      <li v-for="row in rowsOf(job)" :key="row.table">
                        {{ translate(
                          'FeinwerkDeleteVisitsFilter_DeletedRowsItem',
                          row.table,
                          formatCount(row.count),
                        ) }}
                      </li>
                    </ul>
                  </template>
                  <template v-if="job.messages.length">
                    <strong>{{ translate('FeinwerkDeleteVisitsFilter_DetailsMessages') }}</strong>
                    <ul>
                      <li v-for="(message, index) in job.messages" :key="index">
                        {{ messageText(message) }}
                      </li>
                    </ul>
                  </template>
                  <p v-if="job.error" class="fwdvf-error">
                    <strong>{{ translate('FeinwerkDeleteVisitsFilter_DetailsError') }}</strong>
                    {{ job.error }}
                  </p>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      <div v-if="offset > 0 || hasMore" class="fwdvf-paging">
        <button
          type="button"
          class="btn-flat"
          :disabled="offset === 0"
          @click="reload(offset - limit)"
        >
          {{ translate('FeinwerkDeleteVisitsFilter_Previous') }}
        </button>
        <button type="button" class="btn-flat" :disabled="!hasMore" @click="reload(offset + limit)">
          {{ translate('FeinwerkDeleteVisitsFilter_Next') }}
        </button>
      </div>
    </ContentBlock>
    <MatomoDialog
      v-model="retryOpen"
      :options="{ focusSelector: '.modal.open .modal-footer a:last-child' }"
      @yes="retry()"
    >
      <div class="ui-confirm">
        <h2>
          {{ translate(
            'FeinwerkDeleteVisitsFilter_RetryJobConfirm',
            retryTarget ? retryTarget.idjob : '',
            retryTarget ? formatCount(retryTarget.deleted_visits) : '',
          ) }}
        </h2>
        <!-- The action first, Cancel last and focused (Matomo 5.1: the last footer link). -->
        <!-- role names the MatomoDialog button (yes/no), not an ARIA role. -->
        <!-- eslint-disable vuejs-accessibility/aria-role -->
        <input
          type="button"
          role="yes"
          :value="translate('FeinwerkDeleteVisitsFilter_RetryJobConfirmYes')"
        />
        <input type="button" role="no" :value="translate('FeinwerkDeleteVisitsFilter_Cancel')" />
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
  ActivityIndicator, Alert, ContentBlock, MatomoDialog, translate,
} from 'CoreHome';
import { Field } from 'CorePluginsAdmin';
import { errorMessage, getJobs, retryJob } from '../api';
import {
  dateRangeParts, formatCount, formatUtc, messageText, sitesText, sourceLabel, statusLabel,
} from '../labels';
import { durationSeconds, normalizeRows } from '../lib/jobs';
import { formatDuration } from '../lib/progress';
import { allSiteIds, SiteNames } from '../lib/sites';
import JobsStore from '../stores/Jobs.store';
import { Job, JobStatus, JobTemplate } from '../types';

export interface HistoryData {
  jobs: Job[];
  offset: number;
  hasMore: boolean;
  status: string;
  source: string;
  loading: boolean;
  error: string | null;
  expanded: number[];
  request: number;
  pendingOffset: number;
  retrying: number[];
  retryOpen: boolean;
  retryTarget: Job | null;
}

const STATUSES: JobStatus[] = ['queued', 'running', 'held', 'done', 'cancelled', 'failed'];

export default defineComponent({
  props: {
    siteNames: { type: Object as PropType<SiteNames>, required: true },
  },
  emits: ['use-template'],
  components: {
    ActivityIndicator, Alert, ContentBlock, Field, MatomoDialog,
  },
  data(): HistoryData {
    return {
      jobs: [],
      offset: 0,
      hasMore: false,
      status: '',
      source: '',
      loading: false,
      error: null,
      expanded: [],
      request: 0,
      pendingOffset: 0,
      retrying: [],
      retryOpen: false,
      retryTarget: null,
    };
  },
  computed: {
    limit(): number {
      return 20;
    },
    allIds(): number[] {
      return allSiteIds(this.siteNames);
    },
    statusOptions(): { key: string; value: string }[] {
      return [{ key: '', value: translate('FeinwerkDeleteVisitsFilter_FilterAll') }]
        .concat(STATUSES.map((status) => ({ key: status, value: statusLabel(status) })));
    },
    sourceOptions(): { key: string; value: string }[] {
      return [
        { key: '', value: translate('FeinwerkDeleteVisitsFilter_FilterAll') },
        { key: 'ui', value: sourceLabel('ui') },
        { key: 'console', value: sourceLabel('console') },
        { key: 'api', value: sourceLabel('api') },
      ];
    },
    filtered(): boolean {
      return this.status !== '' || this.source !== '';
    },
    /** Changes when a deletion starts or finishes, so the list follows the running jobs. */
    queueVersion(): string {
      const active = JobsStore.state.active.map((job) => `${job.idjob}:${job.status}`).join(',');
      return `${JobsStore.state.finishedCount}|${active}`;
    },
  },
  watch: {
    queueVersion(): void {
      this.reload(this.pendingOffset);
    },
  },
  created() {
    this.reload(0);
  },
  methods: {
    formatCount,
    formatUtc,
    dateRangeParts,
    showAll(): void {
      this.status = '';
      this.source = '';
      this.reload(0);
    },
    isExpanded(idJob: number): boolean {
      return this.expanded.indexOf(idJob) !== -1;
    },
    messageText,
    sitesText,
    statusLabel,
    async reload(offset: number): Promise<void> {
      this.request += 1;
      this.pendingOffset = Math.max(0, offset);
      const { request } = this;
      this.loading = true;
      this.error = null;
      try {
        // One more than shown tells whether there is a next page.
        const rows = await getJobs(Math.max(0, offset), this.limit + 1, this.status, this.source);
        if (request !== this.request) {
          return;
        }
        this.offset = Math.max(0, offset);
        this.hasMore = rows.length > this.limit;
        this.jobs = rows.slice(0, this.limit);
      } catch (e) {
        if (request === this.request) {
          this.error = errorMessage(e);
        }
      } finally {
        if (request === this.request) {
          this.loading = false;
        }
      }
    },
    byText(job: Job): string {
      const source = sourceLabel(job.source);
      if (!job.created_by) {
        return source;
      }
      return job.source === 'ui' ? job.created_by : `${source} (${job.created_by})`;
    },
    durationText(job: Job): string {
      const seconds = durationSeconds(job.started_at, job.finished_at, Date.now());
      return seconds === null ? '-' : formatDuration(seconds);
    },
    rowsOf(job: Job): { table: string; count: number }[] {
      const rows = normalizeRows(job.deleted_rows);
      return Object.keys(rows).sort().map((table) => ({ table, count: rows[table] }));
    },
    toggle(idJob: number): void {
      this.expanded = this.expanded.indexOf(idJob) === -1
        ? this.expanded.concat([idJob])
        : this.expanded.filter((id) => id !== idJob);
    },
    useTemplate(job: Job): void {
      const template: JobTemplate = {
        idJob: job.idjob,
        idSites: job.idsites.slice(),
        segment: job.segment,
        dateFrom: job.date_from,
        dateTo: job.date_to,
      };
      this.$emit('use-template', template);
    },
    askRetry(job: Job): void {
      if (this.retrying.indexOf(job.idjob) !== -1) {
        return;
      }
      this.retryTarget = job;
      this.retryOpen = true;
    },
    async retry(): Promise<void> {
      const job = this.retryTarget;
      if (job === null || this.retrying.indexOf(job.idjob) !== -1) {
        return;
      }
      this.retrying = this.retrying.concat([job.idjob]);
      this.error = null;
      try {
        const retried = await retryJob(job.idjob);
        JobsStore.watch(job.idjob);
        JobsStore.show(retried);
        JobsStore.run();
        await this.reload(this.pendingOffset);
      } catch (e) {
        this.error = errorMessage(e);
      } finally {
        this.retrying = this.retrying.filter((id) => id !== job.idjob);
      }
    },
  },
});
</script>
