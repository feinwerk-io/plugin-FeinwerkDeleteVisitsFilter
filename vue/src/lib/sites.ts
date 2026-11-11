/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

export type SiteMode = 'single' | 'multiple' | 'all';

export interface SiteChoice {
  mode: SiteMode;
  single: number | null;
  multiple: number[];
}

/** idsite → name of every website (superusers only see this page). */
export type SiteNames = Record<string, string>;

export interface SiteSummary {
  names: string[];
  more: number;
  all: boolean;
}

export function allSiteIds(names: SiteNames): number[] {
  return Object.keys(names).map(Number).sort((a, b) => a - b);
}

export function selectedSiteIds(choice: SiteChoice, allIds: number[]): number[] {
  if (choice.mode === 'all') {
    return allIds.slice();
  }
  if (choice.mode === 'multiple') {
    return choice.multiple.slice();
  }
  return choice.single === null ? [] : [choice.single];
}

/** Value of the API parameter idSites. */
export function siteParam(choice: SiteChoice, allIds: number[]): string {
  return choice.mode === 'all' ? 'all' : selectedSiteIds(choice, allIds).join(',');
}

export function addSites(current: number[], add: number[]): number[] {
  const result = current.slice();
  add.forEach((id) => {
    if (result.indexOf(id) === -1) {
      result.push(id);
    }
  });
  return result;
}

/** Websites whose name contains the pattern (case-insensitive), in id order. */
export function matchingSiteIds(names: SiteNames, pattern: string): number[] {
  const needle = pattern.trim().toLowerCase();
  if (needle === '') {
    return [];
  }
  return allSiteIds(names).filter((id) => names[id].toLowerCase().indexOf(needle) !== -1);
}

function coversAll(ids: number[], allIds: number[]): boolean {
  const sorted = ids.slice().sort((a, b) => a - b);
  return allIds.length > 1 && sorted.length === allIds.length
    && sorted.every((id, index) => id === allIds[index]);
}

/** Restores the website choice of a job (template from the history). */
export function choiceFromIds(ids: number[], allIds: number[]): SiteChoice {
  if (coversAll(ids, allIds)) {
    return { mode: 'all', single: allIds[0], multiple: [] };
  }
  if (ids.length === 1) {
    return { mode: 'single', single: ids[0], multiple: [] };
  }
  return { mode: 'multiple', single: ids.length ? ids[0] : null, multiple: ids.slice() };
}

export function summarizeSites(
  ids: number[],
  names: SiteNames,
  allIds: number[],
  max = 3,
): SiteSummary {
  return {
    names: ids.slice(0, max).map((id) => names[id] ?? `#${id}`),
    more: Math.max(0, ids.length - max),
    all: coversAll(ids, allIds),
  };
}
