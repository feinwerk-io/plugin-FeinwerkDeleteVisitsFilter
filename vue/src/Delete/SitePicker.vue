<template>
  <div class="fwdvf-section fwdvf-sites">
    <h3>{{ translate('FeinwerkDeleteVisitsFilter_SectionWebsites') }}</h3>
    <p class="fwdvf-radios">
      <label v-for="option in modes" :key="option.key">
        <input
          type="radio"
          class="with-gap"
          name="fwdvf-site-mode"
          :value="option.key"
          :checked="modelValue.mode === option.key"
          @change="setMode(option.key)"
        />
        <span>{{ option.label }}</span>
      </label>
    </p>
    <div v-if="modelValue.mode === 'single'" class="sites_autocomplete">
      <SiteSelector
        :model-value="singleRef"
        :show-all-sites-item="false"
        :switch-site-on-select="false"
        :show-selected-site="true"
        @update:model-value="setSingle($event)"
      />
    </div>
    <div v-else-if="modelValue.mode === 'multiple'">
      <div class="sites_autocomplete">
        <SiteSelector
          :model-value="addRef"
          :placeholder="translate('FeinwerkDeleteVisitsFilter_AddWebsite')"
          :show-all-sites-item="false"
          :switch-site-on-select="false"
          :show-selected-site="true"
          @update:model-value="addSite($event)"
        />
      </div>
      <div class="fwdvf-sites-pattern">
        <input
          v-model="pattern"
          type="text"
          class="browser-default"
          :aria-label="translate('FeinwerkDeleteVisitsFilter_WebsitePattern')"
          :placeholder="translate('FeinwerkDeleteVisitsFilter_WebsitePattern')"
          @keydown.enter.prevent="addMatching()"
        />
        <button type="button" class="btn-flat" :disabled="!pattern.trim()" @click="addMatching()">
          {{ translate('FeinwerkDeleteVisitsFilter_AddMatchingWebsites') }}
        </button>
        <span v-if="addedCount !== null" class="fwdvf-hint">
          {{ translate(addedHintKey(addedCount), addedCount) }}
        </span>
      </div>
      <p v-if="!modelValue.multiple.length" class="fwdvf-hint">
        {{ translate('FeinwerkDeleteVisitsFilter_NoWebsitesSelected') }}
      </p>
      <ul v-else class="fwdvf-site-list">
        <li v-for="id in modelValue.multiple" :key="id">
          {{ nameOf(id) }} <span class="fwdvf-muted">({{ id }})</span>
          <button
            type="button"
            class="btn-flat fwdvf-remove"
            :title="translate('FeinwerkDeleteVisitsFilter_RemoveWebsite')"
            @click="remove(id)"
          >×</button>
        </li>
      </ul>
    </div>
    <p v-else class="fwdvf-hint">
      {{ translate('FeinwerkDeleteVisitsFilter_AllWebsitesHint', allIds.length) }}
    </p>
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
import { SiteSelector, translate } from 'CoreHome';
import {
  addedHintKey, addSites, allSiteIds, matchingSiteIds, SiteChoice, SiteMode, SiteNames,
} from '../lib/sites';

export interface SiteRefValue {
  id: string | number;
  name: string;
}

export interface SitePickerData {
  pattern: string;
  addedCount: number | null;
}

export default defineComponent({
  props: {
    modelValue: { type: Object as PropType<SiteChoice>, required: true },
    siteNames: { type: Object as PropType<SiteNames>, required: true },
  },
  emits: ['update:modelValue'],
  components: { SiteSelector },
  data(): SitePickerData {
    return { pattern: '', addedCount: null };
  },
  computed: {
    allIds(): number[] {
      return allSiteIds(this.siteNames);
    },
    modes(): { key: SiteMode; label: string }[] {
      return [
        { key: 'single', label: translate('FeinwerkDeleteVisitsFilter_SiteModeSingle') },
        { key: 'multiple', label: translate('FeinwerkDeleteVisitsFilter_SiteModeMultiple') },
        { key: 'all', label: translate('FeinwerkDeleteVisitsFilter_SiteModeAll') },
      ];
    },
    singleRef(): SiteRefValue | undefined {
      const id = this.modelValue.single;
      return id === null ? undefined : { id, name: this.nameOf(id) };
    },
    addRef(): SiteRefValue {
      // An empty choice shows the placeholder and stops SiteSelector
      // from pre-selecting the current site.
      return { id: '', name: '' };
    },
  },
  methods: {
    addedHintKey,
    nameOf(id: number): string {
      return this.siteNames[id] ?? `#${id}`;
    },
    update(changes: Partial<SiteChoice>): void {
      this.$emit('update:modelValue', { ...this.modelValue, ...changes });
    },
    setMode(mode: SiteMode): void {
      const { single, multiple } = this.modelValue;
      // Start the list with the chosen website so switching modes keeps the selection.
      const prefill = mode === 'multiple' && multiple.length === 0 && single !== null ? [single] : multiple;
      this.update({ mode, multiple: prefill });
    },
    toId(site: SiteRefValue | null | undefined): number | null {
      if (!site || site.id === '' || site.id === 'all') {
        return null;
      }
      const id = Number(site.id);
      return Number.isNaN(id) ? null : id;
    },
    setSingle(site: SiteRefValue): void {
      const id = this.toId(site);
      if (id !== null) {
        this.update({ single: id });
      }
    },
    addSite(site: SiteRefValue): void {
      const id = this.toId(site);
      if (id !== null) {
        this.addedCount = null;
        this.update({ multiple: addSites(this.modelValue.multiple, [id]) });
      }
    },
    addMatching(): void {
      const before = this.modelValue.multiple;
      const after = addSites(before, matchingSiteIds(this.siteNames, this.pattern));
      this.addedCount = after.length - before.length;
      this.update({ multiple: after });
    },
    remove(id: number): void {
      this.update({ multiple: this.modelValue.multiple.filter((other) => other !== id) });
    },
  },
});
</script>
