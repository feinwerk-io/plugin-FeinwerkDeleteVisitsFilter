/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/** Link to Matomo's visitor log for one website, date range and segment (relative to index.php). */
export function visitorLogUrl(idSite: number, from: string, to: string, segment: string): string {
  const context = `idSite=${idSite}&period=range&date=${from},${to}`;
  return `?module=CoreHome&action=index&${context}#?${context}`
    + `&category=General_Visitors&subcategory=Live_VisitorLog&segment=${encodeURIComponent(segment)}`;
}
