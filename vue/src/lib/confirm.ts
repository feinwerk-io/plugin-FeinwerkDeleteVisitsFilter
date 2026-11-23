/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export const DEFAULT_CONFIRM_THRESHOLD = 10000;

export function requiresTypedConfirmation(total: number, threshold: number): boolean {
  return total >= threshold;
}

/** Accepts numbers with thousands separators: dot, comma, space, or typographic apostrophe. */
export function typedCountMatches(input: string, total: number): boolean {
  const digits = input.trim().replace(/(\d)[\s.',’](?=\d{3}(\D|$))/g, '$1');
  return /^\d+$/.test(digits) && parseInt(digits, 10) === total;
}
