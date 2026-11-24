<template>
  <div class="fwdvf-section fwdvf-segment">
    <div class="fwdvf-segment-header">
      <h3>{{ translate('FeinwerkDeleteVisitsFilter_SectionSegment') }}</h3>
      <div
        class="fwdvf-segment-mode"
        role="group"
        :aria-label="translate('FeinwerkDeleteVisitsFilter_SegmentModeLabel')"
      >
        <button
          type="button"
          :aria-pressed="!asText ? 'true' : 'false'"
          :class="{ 'fwdvf-active': !asText }"
          @click="setMode(false)"
        >
          {{ translate('FeinwerkDeleteVisitsFilter_SegmentModeEditor') }}
        </button>
        <button
          type="button"
          :aria-pressed="asText ? 'true' : 'false'"
          :class="{ 'fwdvf-active': asText }"
          @click="setMode(true)"
        >
          {{ translate('FeinwerkDeleteVisitsFilter_SegmentModeText') }}
        </button>
      </div>
    </div>
    <div v-if="storedOptions.length" class="fwdvf-stored-segment">
      <Field
        uicontrol="select"
        name="fwdvf-stored-segment"
        :title="translate('FeinwerkDeleteVisitsFilter_StoredSegment')"
        :model-value="stored"
        :options="options"
        @update:model-value="chooseStored($event)"
      />
    </div>
    <div v-if="!asText" class="fwdvf-segment-editor">
      <SegmentGenerator
        :model-value="editorValue"
        :idsite="generatorSite"
        :add-initial-condition="true"
        @update:model-value="fromEditor($event)"
      />
    </div>
    <div v-else class="fwdvf-segment-text-wrap">
      <textarea
        ref="text"
        class="browser-default fwdvf-segment-text"
        rows="10"
        spellcheck="false"
        autocomplete="off"
        :aria-label="translate('FeinwerkDeleteVisitsFilter_SegmentFilter')"
        :aria-describedby="`${helpId} ${countId}`"
        :aria-invalid="tooLong ? 'true' : 'false'"
        :value="modelValue"
        @input="fromText($event)"
      />
      <div class="fwdvf-segment-text-footer">
        <span :id="helpId" class="fwdvf-segment-help">
          {{ translate('FeinwerkDeleteVisitsFilter_SegmentFilterHelp') }}
        </span>
        <span :id="countId" class="fwdvf-segment-count" :class="{ 'fwdvf-over': tooLong }">
          {{ translate('FeinwerkDeleteVisitsFilter_SegmentLength',
                       formatCount(length), formatCount(maxLength)) }}
        </span>
      </div>
    </div>
    <div
      v-if="!asText && definition"
      class="fwdvf-code fwdvf-segment-definition"
      :title="definition"
    >
      {{ definition }}
    </div>
  </div>
</template>

<script lang="ts">
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { defineComponent, nextTick, PropType } from 'vue';
import { translate } from 'CoreHome';
import { Field } from 'CorePluginsAdmin';
import { SegmentGenerator } from 'SegmentEditor';
import { storedSegments } from '../api';
import { formatCount } from '../labels';
import {
  decodeOnce, hasEmptyCondition, isSegmentTooLong, SEGMENT_MAX_LENGTH, segmentLength, SelectOption,
  storedSegmentOptions, toEditorEncoding,
} from '../lib/segment';

/** Gives every instance its own element ids. */
let instanceCount = 0;

export interface SegmentInputData {
  uid: number;
  asText: boolean;
  /** What the segment editor shows (its own, doubly encoded form). */
  editorValue: string;
  stored: string;
  storedOptions: SelectOption[];
}

export default defineComponent({
  props: {
    /** Segment in the API encoding (values encoded once). */
    modelValue: { type: String, required: true },
    idSite: { type: Number as PropType<number | null>, default: null },
    allSites: { type: Boolean, default: false },
  },
  emits: ['update:modelValue'],
  components: { Field, SegmentGenerator },
  data(): SegmentInputData {
    instanceCount += 1;
    return {
      uid: instanceCount,
      asText: false,
      editorValue: toEditorEncoding(this.modelValue),
      stored: '',
      storedOptions: [],
    };
  },
  computed: {
    // The definition under the editor; hidden while a condition has no value
    // (an untouched row reads "actions==").
    definition(): string {
      const value = this.modelValue.trim();
      return value !== '' && !hasEmptyCondition(value) ? value : '';
    },
    helpId(): string {
      return `fwdvf-segment-help-${this.uid}`;
    },
    countId(): string {
      return `fwdvf-segment-count-${this.uid}`;
    },
    length(): number {
      return segmentLength(this.modelValue);
    },
    maxLength(): number {
      return SEGMENT_MAX_LENGTH;
    },
    tooLong(): boolean {
      return isSegmentTooLong(this.modelValue);
    },
    generatorSite(): number | string {
      return this.allSites || this.idSite === null ? 'all' : this.idSite;
    },
    options(): SelectOption[] {
      return [
        { key: '', value: translate('FeinwerkDeleteVisitsFilter_StoredSegmentNone') },
        ...this.storedOptions,
      ];
    },
  },
  watch: {
    idSite: {
      immediate: true,
      handler(idSite: number | null): void {
        this.loadStored(idSite);
      },
    },
    modelValue(value: string): void {
      // Changed from outside (template from the history): show it in the editor as well.
      if (value !== decodeOnce(this.editorValue)) {
        this.editorValue = toEditorEncoding(value);
      }
      if (value !== this.stored) {
        this.stored = '';
      }
      // Typed or set from outside: fit the text field once the new value is rendered.
      if (this.asText) {
        nextTick(() => this.fitHeight());
      }
    },
  },
  mounted(): void {
    window.addEventListener('resize', this.onResize);
  },
  beforeUnmount(): void {
    window.removeEventListener('resize', this.onResize);
  },
  methods: {
    formatCount,
    async loadStored(idSite: number | null): Promise<void> {
      if (idSite === null) {
        this.storedOptions = [];
        return;
      }
      try {
        const options = storedSegmentOptions(await storedSegments(idSite));
        if (this.idSite === idSite) {
          this.storedOptions = options;
        }
      } catch (e) {
        // Saved segments are only a shortcut;
        // the editor and the text field still work without them.
        this.storedOptions = [];
      }
    },
    chooseStored(definition: string): void {
      this.stored = definition;
      if (definition) {
        this.editorValue = toEditorEncoding(definition);
        this.$emit('update:modelValue', definition);
      }
    },
    fromEditor(value: string): void {
      this.editorValue = value;
      this.$emit('update:modelValue', decodeOnce(value));
    },
    fromText(event: Event): void {
      // The modelValue watcher fits the height.
      this.$emit('update:modelValue', (event.target as HTMLTextAreaElement).value || '');
    },
    onResize(): void {
      if (this.asText) {
        this.fitHeight();
      }
    },
    /** Grows the text field with its content; CSS min-height and max-height clamp it. */
    fitHeight(): void {
      const el = this.$refs.text as HTMLTextAreaElement | undefined;
      if (!el) {
        return;
      }
      // The page gets shorter for a moment while the height is 'auto'; keep it from scrolling.
      const scroller = document.scrollingElement;
      const top = scroller ? scroller.scrollTop : 0;
      el.style.height = 'auto';
      el.style.height = `${el.scrollHeight + 2}px`;
      if (scroller) {
        scroller.scrollTop = top;
      }
    },
    setMode(text: boolean): void {
      if (text === this.asText) {
        return;
      }
      if (this.asText) {
        this.editorValue = toEditorEncoding(this.modelValue);
      }
      this.asText = text;
      if (text) {
        nextTick(() => this.fitHeight());
      }
    },
  },
});
</script>
