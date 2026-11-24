<template>
  <div class="fwdvf-manage">
    <ContentBlock :content-title="translate('FeinwerkDeleteVisitsFilter_PageTitle')">
      <p>{{ translate('FeinwerkDeleteVisitsFilter_PageDescription') }}</p>
      <Alert severity="warning">
        <strong>{{ translate('FeinwerkDeleteVisitsFilter_Warning') }}</strong>
        {{ translate('FeinwerkDeleteVisitsFilter_WarningText') }}
      </Alert>
    </ContentBlock>
    <RunningJobs :site-names="siteNames" :background-budget="backgroundBudget" />
    <div class="fwdvf-tabs" role="tablist">
      <button
        v-for="(item, index) in tabs"
        :id="`fwdvf-tab-${item.id}`"
        :key="item.id"
        type="button"
        role="tab"
        class="fwdvf-tab"
        :class="{ 'fwdvf-tab-active': tab === item.id }"
        :aria-selected="tab === item.id ? 'true' : 'false'"
        :aria-controls="panelId(item.id)"
        :tabindex="tab === item.id ? 0 : -1"
        @click="setTab(item.id)"
        @keydown.left.prevent="moveTab(index, -1)"
        @keydown.right.prevent="moveTab(index, 1)"
      >
        {{ item.label }}
      </button>
    </div>
    <DeleteTab
      v-show="tab === 'delete'"
      id="fwdvf-panel-delete"
      role="tabpanel"
      aria-labelledby="fwdvf-tab-delete"
      :initial-site-id="initialSiteId"
      :site-names="siteNames"
      :confirm-threshold="confirmThreshold"
      :default-batch-size="defaultBatchSize"
      :template="template"
    />
    <HistoryTab
      v-if="tab === 'history'"
      id="fwdvf-panel-history"
      role="tabpanel"
      aria-labelledby="fwdvf-tab-history"
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
    // The History panel exists only while it is shown; no reference to a missing element.
    panelId(id: TabId): string | undefined {
      return id === 'delete' || this.tab === id ? `fwdvf-panel-${id}` : undefined;
    },
    // Arrow keys move between the tabs (WAI-ARIA tabs pattern): the next tab opens, gets the focus.
    moveTab(index: number, step: number): void {
      const next = this.tabs[(index + step + this.tabs.length) % this.tabs.length];
      this.setTab(next.id);
      const button = document.getElementById(`fwdvf-tab-${next.id}`);
      if (button) {
        button.focus();
      }
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
