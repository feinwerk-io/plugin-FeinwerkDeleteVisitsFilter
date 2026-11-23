/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export interface StoredSegment {
  idsegment: string | number;
  name: string;
  definition: string;
}

export interface SelectOption {
  key: string;
  value: string;
}

/**
 * Matomo's segment editor and stored segments encode condition values twice; the API expects them
 * encoded once. Plain text input is returned unchanged.
 */
export function decodeOnce(definition: string): string {
  try {
    return decodeURIComponent(definition);
  } catch (e) {
    return definition;
  }
}

export function storedSegmentOptions(segments: StoredSegment[]): SelectOption[] {
  const seen: Record<string, boolean> = {};
  const options: SelectOption[] = [];
  segments.forEach((segment) => {
    const definition = decodeOnce(segment.definition);
    if (!seen[definition]) {
      seen[definition] = true;
      options.push({ key: definition, value: `${segment.name} (${definition})` });
    }
  });
  return options.sort((a, b) => a.value.localeCompare(b.value));
}

// Longest operators first, so that '<=' is not read as '<'.
const CONDITION = /^(\w+)(==|!=|<=|>=|=@|!@|=\^|=\$|<|>)([\s\S]*)$/;

function conditions(definition: string): string[] {
  return definition.split(/[;,]/);
}

/**
 * True when a condition of the API-encoded definition has no value. Matomo reads "x==" as
 * "is NULL, empty or 0", which the editor produces on its own for a fresh condition.
 */
export function hasEmptyCondition(definition: string): boolean {
  return conditions(definition).some((condition) => {
    const match = CONDITION.exec(condition.trim());
    return match !== null && match[3] === '';
  });
}

/**
 * Inverse of decodeOnce for definitions: the segment editor expects condition values encoded twice.
 */
export function toEditorEncoding(definition: string): string {
  let result = '';
  let position = 0;
  conditions(definition).forEach((condition, index) => {
    if (index > 0) {
      result += definition[position - 1];
    }
    const match = CONDITION.exec(condition);
    result += match
      ? `${match[1]}${match[2]}${encodeURIComponent(match[3])}`
      : condition;
    position += condition.length + 1;
  });
  return result;
}

/** Same limit as the server, which checks strlen() (UTF-8 bytes). */
export const SEGMENT_MAX_LENGTH = 8192;

/** Length of the trimmed definition in UTF-8 bytes, as the server counts it. */
export function segmentLength(definition: string): number {
  const trimmed = definition.trim();
  if (typeof TextEncoder !== 'undefined') {
    return new TextEncoder().encode(trimmed).length;
  }
  return unescape(encodeURIComponent(trimmed)).length;
}

export function isSegmentTooLong(definition: string): boolean {
  return segmentLength(definition) > SEGMENT_MAX_LENGTH;
}
