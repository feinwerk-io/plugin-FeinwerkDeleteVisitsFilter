/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { isTransportErrorMessage } from '../src/lib/errors';

describe('transport error messages', () => {
  it.each([
    'Something went wrong',
    'Request was possibly aborted',
    'Rate Limit was exceed',
  ])('recognises %s', (message) => {
    expect(isTransportErrorMessage(message)).toBe(true);
  });

  it.each([
    'Please enter a valid segment',
    'Something went wrong while deleting',
    '',
  ])('keeps the server message %s', (message) => {
    expect(isTransportErrorMessage(message)).toBe(false);
  });
});
