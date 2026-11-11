/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { isPreviewFresh, previewInputKey } from '../src/lib/previewGate';

describe('preview gate', () => {
  const base = previewInputKey('1,2', '2026-01-01', '2026-01-31', 'pageUrl@/spam');

  it('gives the same key for the same inputs', () => {
    expect(previewInputKey('1,2', '2026-01-01', '2026-01-31', 'pageUrl@/spam')).toBe(base);
  });

  it.each([
    ['sites', previewInputKey('1', '2026-01-01', '2026-01-31', 'pageUrl@/spam')],
    ['from', previewInputKey('1,2', '2026-01-02', '2026-01-31', 'pageUrl@/spam')],
    ['to', previewInputKey('1,2', '2026-01-01', '2026-02-01', 'pageUrl@/spam')],
    ['segment', previewInputKey('1,2', '2026-01-01', '2026-01-31', 'pageUrl@/other')],
  ])('changes the key when the %s change', (_name, key) => {
    expect(key).not.toBe(base);
  });

  it('ignores whitespace around the segment', () => {
    expect(previewInputKey('1,2', '2026-01-01', '2026-01-31', '  pageUrl@/spam \n')).toBe(base);
  });

  it('does not mix up the fields', () => {
    expect(previewInputKey('1', '2', '3', '4')).not.toBe(previewInputKey('12', '3', '4', ''));
  });

  it('is fresh only for equal, non-empty keys', () => {
    expect(isPreviewFresh(base, base)).toBe(true);
    expect(isPreviewFresh(base, previewInputKey('1', '2026-01-01', '2026-01-31', ''))).toBe(false);
    expect(isPreviewFresh('', '')).toBe(false);
    expect(isPreviewFresh('', base)).toBe(false);
  });
});
