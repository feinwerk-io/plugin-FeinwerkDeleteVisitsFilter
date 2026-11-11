<template>
  <div class="fwdvf-delete">
    <ContentBlock :content-title="translate('FeinwerkDeleteVisitsFilter_DeleteTitle')">
      <SitePicker v-model="sites" :site-names="siteNames" />
      <DateRangeSelect v-model="dates" />
      <SegmentInput v-model="segment" :id-site="segmentSiteId" :all-sites="sites.mode === 'all'" />
      <details class="fwdvf-advanced">
        <summary>{{ translate('FeinwerkDeleteVisitsFilter_Advanced') }}</summary>
        <Field
          v-model="batchSize"
          uicontrol="text"
          name="fwdvf-batch-size"
          :title="translate('FeinwerkDeleteVisitsFilter_BatchSize')"
          :inline-help="batchSizeHelp"
        />
      </details>
      <p v-if="attempted && inputError" class="fwdvf-error">{{ inputError }}</p>
      <SaveButton
        class="fwdvf-check"
        :value="translate('FeinwerkDeleteVisitsFilter_CheckMatches')"
        :saving="previewing"
        @confirm="checkMatches()"
      />
      <Alert v-if="error" severity="danger">{{ error }}</Alert>
      <template v-if="currentPreview">
        <PreviewResult
          :preview="currentPreview"
          :date-from="dates.from"
          :date-to="dates.to"
          :segment="segment.trim()"
        />
        <Alert v-if="currentPreview.total === 0" severity="info">
          {{ translate('FeinwerkDeleteVisitsFilter_NoVisitsFound') }}
        </Alert>
        <SaveButton
          v-else
          class="fwdvf-delete-button"
          :value="translate(
            'FeinwerkDeleteVisitsFilter_DeleteMatches',
            formatCount(currentPreview.total),
          )"
          :saving="creating"
          @confirm="confirmOpen = true"
        />
      </template>
    </ContentBlock>
    <ConfirmDeleteDialog
      v-model="confirmOpen"
      :total="currentPreview ? currentPreview.total : 0"
      :threshold="confirmThreshold"
      :sites-text="selectedSitesText"
      :date-range-text="dateRangeText(dates.from, dates.to)"
      :segment="segment.trim()"
      @confirm="startDeletion()"
    />
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
  Alert, ContentBlock, Matomo, NotificationsStore, translate,
} from 'CoreHome';
import { Field, SaveButton } from 'CorePluginsAdmin';
import {
  createJob, errorMessage, getJob, previewDeletion,
} from '../api';
import { dateRangeText, formatCount, sitesText } from '../labels';
import { DateSelection, rangeForPreset, rangeProblem } from '../lib/dateRange';
import { isValidBatchSize } from '../lib/jobs';
import { isPreviewFresh, previewInputKey } from '../lib/previewGate';
import { hasEmptyCondition, isSegmentTooLong, SEGMENT_MAX_LENGTH } from '../lib/segment';
import {
  allSiteIds, choiceFromIds, selectedSiteIds, SiteChoice, SiteNames, siteParam,
} from '../lib/sites';
import JobsStore from '../stores/Jobs.store';
import { JobTemplate, Preview } from '../types';
import ConfirmDeleteDialog from './ConfirmDeleteDialog.vue';
import DateRangeSelect from './DateRangeSelect.vue';
import PreviewResult from './PreviewResult.vue';
import SegmentInput from './SegmentInput.vue';
import SitePicker from './SitePicker.vue';

export interface DeleteTabData {
  sites: SiteChoice;
  dates: DateSelection;
  segment: string;
  batchSize: string;
  attempted: boolean;
  previewing: boolean;
  creating: boolean;
  confirmOpen: boolean;
  preview: Preview | null;
  /** Inputs the preview was made for; a preview for other inputs is not shown. */
  previewKey: string;
  error: string | null;
}

export default defineComponent({
  props: {
    initialSiteId: { type: Number, required: true },
    siteNames: { type: Object as PropType<SiteNames>, required: true },
    confirmThreshold: { type: Number, required: true },
    defaultBatchSize: { type: Number, required: true },
    template: { type: Object as PropType<JobTemplate | null>, default: null },
  },
  components: {
    Alert,
    ConfirmDeleteDialog,
    ContentBlock,
    DateRangeSelect,
    Field,
    PreviewResult,
    SaveButton,
    SegmentInput,
    SitePicker,
  },
  data(): DeleteTabData {
    return {
      sites: { mode: 'single', single: this.initialSiteId || null, multiple: [] },
      dates: { preset: 'last7', ...rangeForPreset('last7', new Date())! },
      segment: '',
      batchSize: '',
      attempted: false,
      previewing: false,
      creating: false,
      confirmOpen: false,
      preview: null,
      previewKey: '',
      error: null,
    };
  },
  computed: {
    allIds(): number[] {
      return allSiteIds(this.siteNames);
    },
    selectedIds(): number[] {
      return selectedSiteIds(this.sites, this.allIds);
    },
    idSitesParam(): string {
      return siteParam(this.sites, this.allIds);
    },
    segmentSiteId(): number | null {
      if (this.selectedIds.length) {
        return this.selectedIds[0];
      }
      return this.allIds.length ? this.allIds[0] : null;
    },
    selectedSitesText(): string {
      return sitesText(this.selectedIds, this.siteNames, this.allIds);
    },
    inputKey(): string {
      return previewInputKey(this.idSitesParam, this.dates.from, this.dates.to, this.segment);
    },
    currentPreview(): Preview | null {
      return this.preview && isPreviewFresh(this.previewKey, this.inputKey) ? this.preview : null;
    },
    batchSizeHelp(): string {
      return `${translate('FeinwerkDeleteVisitsFilter_BatchSizeHelp')} ${
        translate('FeinwerkDeleteVisitsFilter_BatchSizeDefault', this.defaultBatchSize)}`;
    },
    inputError(): string | null {
      if (!this.selectedIds.length) {
        return translate('FeinwerkDeleteVisitsFilter_ErrorNoSite');
      }
      const problem = rangeProblem(this.dates);
      if (problem) {
        return translate(problem.key, problem.value);
      }
      if (!this.segment.trim()) {
        return translate('FeinwerkDeleteVisitsFilter_ErrorNoSegment');
      }
      if (isSegmentTooLong(this.segment)) {
        return translate(
          'FeinwerkDeleteVisitsFilter_ErrorSegmentTooLong',
          formatCount(SEGMENT_MAX_LENGTH),
        );
      }
      if (hasEmptyCondition(this.segment.trim())) {
        return translate('FeinwerkDeleteVisitsFilter_ErrorSegmentEmptyValue');
      }
      if (!isValidBatchSize(this.batchSize)) {
        return translate('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeInvalid');
      }
      return null;
    },
  },
  watch: {
    template(template: JobTemplate | null): void {
      if (template) {
        this.applyTemplate(template);
      }
    },
  },
  methods: {
    formatCount,
    dateRangeText,
    applyTemplate(template: JobTemplate): void {
      this.sites = choiceFromIds(template.idSites, this.allIds);
      this.dates = { preset: 'custom', from: template.dateFrom, to: template.dateTo };
      this.segment = template.segment;
      this.preview = null;
      this.error = null;
      this.attempted = false;
    },
    async checkMatches(): Promise<void> {
      this.attempted = true;
      if (this.inputError) {
        return;
      }
      const key = this.inputKey;
      this.error = null;
      this.previewing = true;
      try {
        this.preview = await previewDeletion(
          this.idSitesParam,
          this.segment.trim(),
          this.dates.from,
          this.dates.to,
        );
        this.previewKey = key;
      } catch (e) {
        // A failure for inputs that were changed in the meantime is stale.
        if (key === this.inputKey) {
          this.error = errorMessage(e);
        }
      } finally {
        this.previewing = false;
      }
    },
    async startDeletion(): Promise<void> {
      if (this.creating || !this.currentPreview) {
        return;
      }
      // The batch size is not part of the preview key and may have changed since.
      if (this.inputError) {
        this.attempted = true;
        return;
      }
      this.creating = true;
      this.error = null;
      try {
        const { idjob } = await createJob(
          this.idSitesParam,
          this.segment.trim(),
          this.dates.from,
          this.dates.to,
          this.batchSize.trim(),
        );
        JobsStore.watch(idjob);
        JobsStore.run();
        // Show the job right away. Errors are ignored: the loop's next response shows it anyway.
        getJob(idjob).then((job) => JobsStore.show(job)).catch(() => undefined);
        this.preview = null;
        NotificationsStore.show({
          message: Matomo.helper.htmlEntities(translate('FeinwerkDeleteVisitsFilter_JobCreated', idjob)),
          context: 'success',
          id: 'fwdvf-created',
          type: 'transient',
        });
      } catch (e) {
        this.error = errorMessage(e);
      } finally {
        this.creating = false;
      }
    },
  },
});
</script>
