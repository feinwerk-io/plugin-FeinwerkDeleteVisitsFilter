<template>
  <MatomoDialog
    :model-value="modelValue"
    :options="dialogOptions"
    @update:model-value="$emit('update:modelValue', $event)"
    @validation="confirm()"
  >
    <div class="ui-confirm fwdvf-confirm">
      <h2>{{ translate('FeinwerkDeleteVisitsFilter_ConfirmTitle') }}</h2>
      <ul>
        <li>{{ translate('FeinwerkDeleteVisitsFilter_ConfirmVisits', formattedTotal) }}</li>
        <li>{{ translate('FeinwerkDeleteVisitsFilter_ConfirmWebsites', sitesText) }}</li>
        <li>{{ translate('FeinwerkDeleteVisitsFilter_ConfirmDateRange', dateRangeText) }}</li>
        <li>
          {{ translate('FeinwerkDeleteVisitsFilter_ConfirmSegmentLabel') }}
          <code class="fwdvf-code fwdvf-confirm-segment">{{ segment }}</code>
        </li>
      </ul>
      <p>{{ translate('FeinwerkDeleteVisitsFilter_ConfirmIrreversible') }}</p>
      <div v-if="needsTyping" class="fwdvf-confirm-typing">
        <label for="fwdvf-confirm-count">
          {{ translate('FeinwerkDeleteVisitsFilter_ConfirmTypeCount', formattedTotal) }}
        </label>
        <input
          id="fwdvf-confirm-count"
          v-model="typed"
          type="text"
          class="browser-default"
          autocomplete="off"
        />
        <p v-if="mismatch" class="fwdvf-error">
          {{ translate('FeinwerkDeleteVisitsFilter_ConfirmTypeMismatch') }}
        </p>
      </div>
      <!-- Cancel first: the dialog focuses the first button, so a held Enter cannot confirm. -->
      <!-- role names the MatomoDialog button (yes/no/validation), not an ARIA role. -->
      <!-- eslint-disable vuejs-accessibility/aria-role -->
      <input type="button" role="no" :value="translate('FeinwerkDeleteVisitsFilter_Cancel')" />
      <input
        type="button"
        role="validation"
        :value="translate('FeinwerkDeleteVisitsFilter_DeleteNow')"
      />
      <!-- eslint-enable vuejs-accessibility/aria-role -->
    </div>
  </MatomoDialog>
</template>

<script lang="ts">
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { defineComponent } from 'vue';
import { MatomoDialog } from 'CoreHome';
import { formatCount } from '../labels';
import { requiresTypedConfirmation, typedCountMatches } from '../lib/confirm';

export interface ConfirmData {
  typed: string;
  mismatch: boolean;
}

export default defineComponent({
  props: {
    modelValue: { type: Boolean, required: true },
    total: { type: Number, required: true },
    threshold: { type: Number, required: true },
    sitesText: { type: String, required: true },
    dateRangeText: { type: String, required: true },
    segment: { type: String, required: true },
  },
  emits: ['update:modelValue', 'confirm'],
  components: { MatomoDialog },
  data(): ConfirmData {
    return { typed: '', mismatch: false };
  },
  computed: {
    needsTyping(): boolean {
      return requiresTypedConfirmation(this.total, this.threshold);
    },
    // Read when the dialog opens: focus the typed-count input, else the cancel link (never the
    // destructive one, which Matomo would otherwise focus because it is the last footer link).
    dialogOptions(): { focusSelector: string } {
      return {
        focusSelector: this.needsTyping
          ? '#fwdvf-confirm-count'
          : '.modal.open .modal-footer a:first-child',
      };
    },
    formattedTotal(): string {
      return formatCount(this.total);
    },
  },
  watch: {
    modelValue(open: boolean): void {
      if (open) {
        this.typed = '';
        this.mismatch = false;
      }
    },
  },
  methods: {
    // role="validation" does not close the dialog by itself:
    // it stays open until the number matches.
    confirm(): void {
      if (!this.modelValue) {
        return;
      }
      if (this.needsTyping && !typedCountMatches(this.typed, this.total)) {
        this.mismatch = true;
        return;
      }
      this.$emit('update:modelValue', false);
      this.$emit('confirm');
    },
  },
});
</script>
