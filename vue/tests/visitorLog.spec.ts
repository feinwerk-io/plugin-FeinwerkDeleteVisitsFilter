/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import { visitorLogUrl } from '../src/lib/visitorLog';

describe('visitorLogUrl', () => {
  it('links to the visitor log with range and encoded segment', () => {
    expect(visitorLogUrl(7, '2026-10-01', '2026-10-06', 'pageUrl=@%2Fspam')).toBe(
      '?module=CoreHome&action=index&idSite=7&period=range&date=2026-10-01,2026-10-06'
      + '#?idSite=7&period=range&date=2026-10-01,2026-10-06'
      + '&category=General_Visitors&subcategory=Live_VisitorLog&segment=pageUrl%3D%40%252Fspam',
    );
  });
});
