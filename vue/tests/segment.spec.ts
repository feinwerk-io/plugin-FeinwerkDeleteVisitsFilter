/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

import {
  decodeOnce, hasEmptyCondition, isSegmentTooLong, SEGMENT_MAX_LENGTH, segmentLength,
  storedSegmentOptions, toEditorEncoding,
} from '../src/lib/segment';

describe('decodeOnce', () => {
  it('turns the editor encoding into the API encoding', () => {
    expect(decodeOnce('pageUrl=@%252Fspam;visitIp==1.2.3.4')).toBe('pageUrl=@%2Fspam;visitIp==1.2.3.4');
  });

  it('keeps plain text', () => {
    expect(decodeOnce('visitIp==192.0.2.10')).toBe('visitIp==192.0.2.10');
  });

  it('returns the input when it is not valid percent-encoding', () => {
    expect(decodeOnce('pageTitle=@100%')).toBe('pageTitle=@100%');
  });

  it('handles long segments', () => {
    const conditions = Array.from({ length: 600 }, (_, i) => `visitIp==10.0.${Math.floor(i / 250)}.${i % 250}`);
    const encoded = conditions.join(',').replace(/=/g, '%3D');
    expect(encoded.length).toBeGreaterThan(8192);
    expect(decodeOnce(encoded)).toBe(conditions.join(','));
  });
});

describe('storedSegmentOptions', () => {
  it('decodes, de-duplicates and sorts by label', () => {
    const options = storedSegmentOptions([
      { idsegment: 2, name: 'Bots', definition: 'browserCode%3D%3DXX' },
      { idsegment: 1, name: 'Agency', definition: 'visitIp==10.0.0.1' },
      { idsegment: 3, name: 'Bots copy', definition: 'browserCode%3D%3DXX' },
    ]);
    expect(options).toEqual([
      { key: 'visitIp==10.0.0.1', value: 'Agency (visitIp==10.0.0.1)' },
      { key: 'browserCode==XX', value: 'Bots (browserCode==XX)' },
    ]);
  });
});

describe('hasEmptyCondition', () => {
  it('detects an empty value', () => {
    expect(hasEmptyCondition('visitId==')).toBe(true);
    expect(hasEmptyCondition('visitIp==1.2.3.4;pageUrl=@')).toBe(true);
    expect(hasEmptyCondition('visitIp==1.2.3.4,browserCode==')).toBe(true);
  });

  it('accepts filled conditions', () => {
    expect(hasEmptyCondition('visitIp==1.2.3.4,browserCode==FF')).toBe(false);
    expect(hasEmptyCondition('pageUrl=@%3B')).toBe(false);
    expect(hasEmptyCondition('visitCount>=2')).toBe(false);
  });

  it('accepts an empty segment', () => {
    expect(hasEmptyCondition('')).toBe(false);
  });
});

describe('toEditorEncoding', () => {
  it('encodes values once more', () => {
    expect(toEditorEncoding('pageUrl=@%2Fspam')).toBe('pageUrl=@%252Fspam');
  });

  it('is the inverse of decodeOnce', () => {
    ['pageUrl=@%2Fspam;visitIp==1.2.3.4', 'pageUrl=@%C3%BC', 'pageTitle=@100%25'].forEach((x) => {
      expect(decodeOnce(toEditorEncoding(x))).toBe(x);
    });
  });
});

describe('segment length', () => {
  it('uses the same limit as the server', () => {
    expect(SEGMENT_MAX_LENGTH).toBe(8192);
  });

  it('counts ASCII characters', () => {
    expect(segmentLength('visitIp==1.2.3.4')).toBe(16);
  });

  it('counts UTF-8 bytes of multi-byte characters', () => {
    expect(segmentLength('ü')).toBe(2);
  });

  it('ignores surrounding whitespace', () => {
    expect(segmentLength('  abc \n')).toBe(3);
  });

  it('accepts exactly the maximum length', () => {
    expect(isSegmentTooLong('a'.repeat(8192))).toBe(false);
  });

  it('rejects one byte more', () => {
    expect(isSegmentTooLong('a'.repeat(8193))).toBe(true);
  });

  it('counts bytes, not characters, for the limit', () => {
    expect(isSegmentTooLong('ü'.repeat(4096))).toBe(false);
    expect(isSegmentTooLong('ü'.repeat(4097))).toBe(true);
  });
});
