/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import {
  addSites, allSiteIds, choiceFromIds, matchingSiteIds, selectedSiteIds, siteParam, summarizeSites,
} from '../src/lib/sites';

const names = { 3: 'shop AT', 1: 'Shop DE', 2: 'Blog' };

describe('sites', () => {
  it('lists all ids in order', () => {
    expect(allSiteIds(names)).toEqual([1, 2, 3]);
  });

  it('turns a choice into ids and the API parameter', () => {
    const all = [1, 2, 3];
    expect(selectedSiteIds({ mode: 'single', single: 2, multiple: [] }, all)).toEqual([2]);
    expect(selectedSiteIds({ mode: 'single', single: null, multiple: [] }, all)).toEqual([]);
    expect(selectedSiteIds({ mode: 'multiple', single: 2, multiple: [3, 1] }, all)).toEqual([3, 1]);
    expect(selectedSiteIds({ mode: 'all', single: 2, multiple: [] }, all)).toEqual([1, 2, 3]);
    expect(siteParam({ mode: 'multiple', single: 2, multiple: [3, 1] }, all)).toBe('3,1');
    expect(siteParam({ mode: 'all', single: 2, multiple: [] }, all)).toBe('all');
  });

  it('adds without duplicates and keeps the order', () => {
    expect(addSites([3, 1], [1, 2, 2])).toEqual([3, 1, 2]);
  });

  it('finds websites by name, ignoring case', () => {
    expect(matchingSiteIds(names, ' shop ')).toEqual([1, 3]);
    expect(matchingSiteIds(names, '')).toEqual([]);
  });

  it('restores a choice from job websites', () => {
    const all = [1, 2, 3];
    expect(choiceFromIds([3, 2, 1], all)).toEqual({ mode: 'all', single: 1, multiple: [] });
    expect(choiceFromIds([2], all)).toEqual({ mode: 'single', single: 2, multiple: [] });
    expect(choiceFromIds([3, 1], all)).toEqual({ mode: 'multiple', single: 3, multiple: [3, 1] });
    expect(choiceFromIds([1], [1])).toEqual({ mode: 'single', single: 1, multiple: [] });
  });

  it('summarizes long lists and unknown ids', () => {
    expect(summarizeSites([1, 2, 3, 9], names, [1, 2, 3, 4]))
      .toEqual({ names: ['Shop DE', 'Blog', 'shop AT'], more: 1, all: false });
    expect(summarizeSites([9], names, [1, 2, 3]))
      .toEqual({ names: ['#9'], more: 0, all: false });
    expect(summarizeSites([3, 1, 2], names, [1, 2, 3]).all).toBe(true);
  });
});
