<template>
  <div class="fwdvf-manage">
    <ContentBlock :content-title="translate('FeinwerkDeleteVisitsFilter_PageTitle')">
      <p>{{ translate('FeinwerkDeleteVisitsFilter_PageDescription') }}</p>
      <Alert severity="warning">
        <strong>{{ translate('FeinwerkDeleteVisitsFilter_Warning') }}:</strong>
        {{ translate('FeinwerkDeleteVisitsFilter_WarningText') }}
      </Alert>
    </ContentBlock>
    <RunningJobs :site-names="siteNames" :background-budget="backgroundBudget" />
    <div class="fwdvf-tabs">
      <button
        v-for="item in tabs"
        :key="item.id"
        type="button"
        class="fwdvf-tab"
        :class="{ 'fwdvf-tab-active': tab === item.id }"
        @click="setTab(item.id)"
      >
        {{ item.label }}
      </button>
    </div>
    <DeleteTab
      v-show="tab === 'delete'"
      :initial-site-id="initialSiteId"
      :site-names="siteNames"
      :confirm-threshold="confirmThreshold"
      :default-batch-size="defaultBatchSize"
      :template="template"
    />
    <HistoryTab
      v-if="tab === 'history'"
      :site-names="siteNames"
      @use-template="useTemplate($event)"
    />
    <FeedbackBar
      topic="delete_visits"
      :question="translate('FeinwerkDeleteVisitsFilter_FeedbackQuestion_delete_visits')"
      :feedback="feedback"
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
  Alert, ContentBlock, Matomo, MatomoUrl, NotificationsStore, translate,
} from 'CoreHome';
import DeleteTab from '../Delete/DeleteTab.vue';
import FeedbackBar, { FeedbackSettings } from './FeedbackBar.vue';
import HistoryTab from '../History/HistoryTab.vue';
import RunningJobs from '../Running/RunningJobs.vue';
import JobsStore from '../stores/Jobs.store';
import { SiteNames } from '../lib/sites';
import { Job, JobTemplate } from '../types';

type TabId = 'delete' | 'history';

interface ManageData {
  template: JobTemplate | null;
}

export default defineComponent({
  props: {
    initialSiteId: { type: Number, required: true },
    siteNames: { type: Object as PropType<SiteNames>, required: true },
    activeJobs: { type: Array as PropType<Job[]>, required: true },
    confirmThreshold: { type: Number, required: true },
    backgroundBudget: { type: Number, required: true },
    defaultBatchSize: { type: Number, required: true },
    feedback: {
      type: Object as PropType<FeedbackSettings>,
      default: () => ({ enabled: false, supportUrl: '', version: '' }),
    },
  },
  components: {
    Alert, ContentBlock, DeleteTab, FeedbackBar, HistoryTab, RunningJobs,
  },
  data(): ManageData {
    return { template: null };
  },
  computed: {
    tab(): TabId {
      return MatomoUrl.hashParsed.value.tab === 'history' ? 'history' : 'delete';
    },
    tabs(): { id: TabId; label: string }[] {
      return [
        { id: 'delete', label: translate('FeinwerkDeleteVisitsFilter_TabDelete') },
        { id: 'history', label: translate('FeinwerkDeleteVisitsFilter_TabHistory') },
      ];
    },
  },
  created() {
    // After a reload during a deletion the page continues where it was.
    JobsStore.setActive(this.activeJobs);
    if (JobsStore.state.active.length > 0) {
      JobsStore.run();
    }
  },
  methods: {
    setTab(tab: TabId): void {
      MatomoUrl.updateHash({ ...MatomoUrl.hashParsed.value, tab });
    },
    useTemplate(template: JobTemplate): void {
      this.template = template;
      this.setTab('delete');
      NotificationsStore.show({
        message: Matomo.helper.htmlEntities(
          translate('FeinwerkDeleteVisitsFilter_TemplateApplied', template.idJob),
        ),
        context: 'info',
        id: 'fwdvf-template',
        type: 'transient',
      });
    },
  },
});
</script>
