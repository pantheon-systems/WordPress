'use strict';

/**
 * MurmurHash is a non-cryptographic hash function suitable for general hash-based lookup
 *
 * @see https://en.wikipedia.org/wiki/MurmurHash
 * @see https://github.com/petertodd/python-bitcoinlib/blob/master/bitcoin/bloom.py
 * @see https://github.com/bitcoinj/bitcoinj/blob/master/core/src/main/java/org/bitcoinj/core/BloomFilter.java#L170
 * @see https://github.com/bitcoin/bitcoin/blob/master/src/hash.cpp
 * @see https://github.com/indutny/bcoin/blob/master/lib/bcoin/bloom.js
 * @see https://github.com/garycourt/murmurhash-js
 *
 * @param {Buffer} data to be hashed
 * @param {Number} seed Positive integer only
 * @return {Number} a 32-bit positive integer hash
*/
function MurmurHash3(seed, data) {
  /* jshint maxstatements: 32, maxcomplexity: 10 */

  var c1 = 0xcc9e2d51;
  var c2 = 0x1b873593;
  var r1 = 15;
  var r2 = 13;
  var m = 5;
  var n = 0x6b64e654;

  var hash = seed;

  function mul32(a, b) {
    return (a & 0xffff) * b + (((a >>> 16) * b & 0xffff) << 16) & 0xffffffff;
  }

  function sum32(a, b) {
    return (a & 0xffff) + (b >>> 16) + (((a >>> 16) + b & 0xffff) << 16) & 0xffffffff;
  }

  function rotl32(a, b) {
    return (a << b) | (a >>> (32 - b));
  }

  var k1;

  for (var i = 0; i + 4 <= data.length; i += 4) {
    k1 = data[i] |
      (data[i + 1] << 8) |
      (data[i + 2] << 16) |
      (data[i + 3] << 24);

    k1 = mul32(k1, c1);
    k1 = rotl32(k1, r1);
    k1 = mul32(k1, c2);

    hash ^= k1;
    hash = rotl32(hash, r2);
    hash = mul32(hash, m);
    hash = sum32(hash, n);
  }

  k1 = 0;

  switch(data.length & 3) {
    case 3:
      k1 ^= data[i + 2] << 16;
      /* falls through */
    case 2:
      k1 ^= data[i + 1] << 8;
      /* falls through */
    case 1:
      k1 ^= data[i];
      k1 = mul32(k1, c1);
      k1 = rotl32(k1, r1);
      k1 = mul32(k1, c2);
      hash ^= k1;
  }

  hash ^= data.length;
  hash ^= hash >>> 16;
  hash = mul32(hash, 0x85ebca6b);
  hash ^= hash >>> 13;
  hash = mul32(hash, 0xc2b2ae35);
  hash ^= hash >>> 16;

  return hash >>> 0;
}

module.exports = MurmurHash3;
