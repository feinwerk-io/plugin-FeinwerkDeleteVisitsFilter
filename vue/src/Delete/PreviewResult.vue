<template>
  <div class="fwdvf-preview">
    <h3>{{ translate('FeinwerkDeleteVisitsFilter_PreviewTotal', formatCount(preview.total)) }}</h3>
    <Alert v-if="preview.action_level" severity="info">
      {{ translate('FeinwerkDeleteVisitsFilter_ActionLevelHint') }}
    </Alert>
    <table class="entityTable dataTable fwdvf-table">
      <thead>
        <tr>
          <th>{{ translate('FeinwerkDeleteVisitsFilter_PreviewWebsite') }}</th>
          <th class="fwdvf-num">{{ translate('FeinwerkDeleteVisitsFilter_PreviewVisits') }}</th>
          <th />
        </tr>
      </thead>
      <tbody>
        <tr v-for="site in preview.sites" :key="site.idsite">
          <td>{{ site.name }} <span class="fwdvf-muted">({{ site.idsite }})</span></td>
          <td class="fwdvf-num">{{ formatCount(site.count) }}</td>
          <td>
            <a
              v-if="site.count > 0"
              :href="logUrl(site.idsite)"
              target="_blank"
              rel="noreferrer noopener"
            >
              {{ translate('FeinwerkDeleteVisitsFilter_ViewInVisitorLog') }}
            </a>
          </td>
        </tr>
      </tbody>
      <!-- Cells, not headers: Matomo pads header cells differently, -->
      <!-- so a total in header cells would not line up with the counts. -->
      <tfoot v-if="multipleSites">
        <tr class="fwdvf-total">
          <td>{{ translate('FeinwerkDeleteVisitsFilter_PreviewSum') }}</td>
          <td class="fwdvf-num">{{ formatCount(preview.total) }}</td>
          <td />
        </tr>
      </tfoot>
    </table>
    <template v-if="preview.sample.length">
      <h4>{{ translate('FeinwerkDeleteVisitsFilter_SampleTitle') }}</h4>
      <div class="fwdvf-table-scroll">
        <table class="entityTable dataTable fwdvf-table fwdvf-sample">
          <thead>
            <tr>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_SampleTime') }}</th>
              <th v-if="multipleSites">
                {{ translate('FeinwerkDeleteVisitsFilter_SampleWebsite') }}
              </th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_SampleIp') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_SampleDevice') }}</th>
              <th class="fwdvf-num">{{ translate('FeinwerkDeleteVisitsFilter_SampleActions') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_SampleEntry') }}</th>
              <th>{{ translate('FeinwerkDeleteVisitsFilter_SampleReferrer') }}</th>
              <th v-if="profileEnabled" />
            </tr>
          </thead>
          <tbody>
            <tr v-for="visit in preview.sample" :key="`${visit.idsite}-${visit.idvisit}`">
              <td class="fwdvf-nowrap">{{ visit.time }}</td>
              <td v-if="multipleSites">{{ siteName(visit.idsite) }}</td>
              <td>{{ visit.ip }}</td>
              <td :title="`${visit.browser} / ${visit.os}`">
                {{ visit.browser_name || visit.browser }} / {{ visit.os_name || visit.os }}
              </td>
              <td class="fwdvf-num">{{ visit.actions }}</td>
              <td class="fwdvf-url" :title="visit.entry_url">{{ visit.entry_url }}</td>
              <td>{{ visit.referrer_name || '-' }}</td>
              <td v-if="profileEnabled">
                <!-- eslint-disable-next-line vuejs-accessibility/click-events-have-key-events -->
                <a href="" @click.prevent="showProfile(visit)">
                  {{ translate('FeinwerkDeleteVisitsFilter_SampleProfile') }}
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
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
import { Alert, Matomo } from 'CoreHome';
import { formatCount } from '../labels';
import { visitorLogUrl } from '../lib/visitorLog';
import { Preview, SampleVisit } from '../types';

export default defineComponent({
  props: {
    preview: { type: Object as PropType<Preview>, required: true },
    dateFrom: { type: String, required: true },
    dateTo: { type: String, required: true },
    segment: { type: String, required: true },
  },
  components: { Alert },
  computed: {
    profileEnabled(): boolean {
      return !!Matomo.visitorProfileEnabled;
    },
    multipleSites(): boolean {
      return this.preview.sites.length > 1;
    },
  },
  methods: {
    formatCount,
    logUrl(idSite: number): string {
      return visitorLogUrl(idSite, this.dateFrom, this.dateTo, this.segment);
    },
    siteName(idSite: number): string {
      const site = this.preview.sites.find((candidate) => candidate.idsite === idSite);
      return site ? site.name : `#${idSite}`;
    },
    showProfile(visit: SampleVisit): void {
      Matomo.helper.showVisitorProfilePopup(visit.visitor_id, visit.idsite);
    },
  },
});
</script>
