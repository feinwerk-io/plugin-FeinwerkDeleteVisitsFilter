<template>
  <div class="fwdvf-section fwdvf-daterange">
    <h3>{{ translate('FeinwerkDeleteVisitsFilter_SectionDateRange') }}</h3>
    <Field
      uicontrol="select"
      name="fwdvf-date-preset"
      :ui-control-attributes="presetAttributes"
      :model-value="modelValue.preset"
      :options="presetOptions"
      @update:model-value="selectPreset($event)"
    />
    <div v-if="modelValue.preset === 'custom'" class="fwdvf-daterange-picker">
      <DateRangePicker
        :start-date="modelValue.from"
        :end-date="modelValue.to"
        @range-change="changeRange($event)"
      />
    </div>
    <p class="fwdvf-muted fwdvf-daterange-value">
      {{ translate('FeinwerkDeleteVisitsFilter_DateRangeValue', modelValue.from, modelValue.to) }}.
      {{ translate('FeinwerkDeleteVisitsFilter_DateRangeTimezoneHint') }}
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
import { DateRangePicker, translate } from 'CoreHome';
import { Field } from 'CorePluginsAdmin';
import {
  DATE_PRESETS, DatePreset, DateSelection, rangeForPreset,
} from '../lib/dateRange';

const PRESET_KEYS: Record<DatePreset, string> = {
  today: 'FeinwerkDeleteVisitsFilter_PresetToday',
  yesterday: 'FeinwerkDeleteVisitsFilter_PresetYesterday',
  last7: 'FeinwerkDeleteVisitsFilter_PresetLast7',
  last30: 'FeinwerkDeleteVisitsFilter_PresetLast30',
  lastMonth: 'FeinwerkDeleteVisitsFilter_PresetLastMonth',
  thisYear: 'FeinwerkDeleteVisitsFilter_PresetThisYear',
  lastYear: 'FeinwerkDeleteVisitsFilter_PresetLastYear',
  all: 'FeinwerkDeleteVisitsFilter_PresetAll',
  custom: 'FeinwerkDeleteVisitsFilter_PresetCustom',
};

export default defineComponent({
  props: {
    modelValue: { type: Object as PropType<DateSelection>, required: true },
  },
  emits: ['update:modelValue'],
  components: { DateRangePicker, Field },
  computed: {
    // The preset select has no visible label of its own: the heading above names it.
    presetAttributes(): Record<string, string> {
      return { 'aria-label': translate('FeinwerkDeleteVisitsFilter_SectionDateRange') };
    },
    presetOptions(): { key: string; value: string }[] {
      return DATE_PRESETS.map((preset) => ({ key: preset, value: translate(PRESET_KEYS[preset]) }));
    },
  },
  methods: {
    selectPreset(preset: DatePreset): void {
      const range = rangeForPreset(preset, new Date());
      // 'custom' keeps the current days as the starting point for the calendar.
      this.$emit('update:modelValue', range ? { preset, ...range } : { ...this.modelValue, preset });
    },
    changeRange(range: { start: string | null; end: string | null }): void {
      this.$emit('update:modelValue', {
        preset: 'custom',
        from: range.start || this.modelValue.from,
        to: range.end || this.modelValue.to,
      });
    },
  },
});
</script>
