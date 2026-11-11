/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { requiresTypedConfirmation, typedCountMatches } from '../src/lib/confirm';

describe('confirmation', () => {
  it('requires typing from the threshold on', () => {
    expect(requiresTypedConfirmation(9999, 10000)).toBe(false);
    expect(requiresTypedConfirmation(10000, 10000)).toBe(true);
  });

  it.each(['12345', '12.345', '12,345', '12 345', ' 12345 ', '12’345'])('accepts %s', (input) => {
    expect(typedCountMatches(input, 12345)).toBe(true);
  });

  it('accepts several separators', () => {
    expect(typedCountMatches('1.234.567', 1234567)).toBe(true);
  });

  it.each(['12344', '', 'abc', '1234.5', '-12345', '12.34.5'])('rejects %s', (input) => {
    expect(typedCountMatches(input, 12345)).toBe(false);
  });
});
