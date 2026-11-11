/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Identifies the inputs a preview was made for. Deleting is only offered while the current inputs
 * still have the key of the last preview.
 */
export function previewInputKey(
  idSites: string,
  from: string,
  to: string,
  segment: string,
): string {
  return JSON.stringify([idSites, from, to, segment.trim()]);
}

export function isPreviewFresh(previewKey: string, currentKey: string): boolean {
  return previewKey !== '' && previewKey === currentKey;
}
