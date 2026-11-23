/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

// Fixed English texts with which Matomo's AjaxHelper rejects a request that never got an API
// answer (network error, gateway timeout, rate limit). They are not worth showing to the user.
const TRANSPORT_ERROR_MESSAGES = [
  'Something went wrong',
  'Request was possibly aborted',
  'Rate Limit was exceed',
];

export function isTransportErrorMessage(message: string): boolean {
  return TRANSPORT_ERROR_MESSAGES.indexOf(message) !== -1;
}
