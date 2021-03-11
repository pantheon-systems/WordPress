'use strict';

var MurmurHash3 = require('./murmurhash3');

/**
 * A Bloom Filter implemented as for use in Bitcoin Connection Bloom Filtering (BIP37) that
 * uses version 3 of the 32-bit Murmur hash function.
 *
 * @see https://github.com/bitcoin/bips/blob/master/bip-0037.mediawiki
 * @see https://github.com/bitcoin/bitcoin/blob/master/src/bloom.cpp
 *
 * @param {Object} data - The data object used to initialize the filter.
 * @param {Array} data.vData - The data of the bloom filter.
 * @param {Number} data.nHashFuncs - The number of hash functions.
 * @param {Number} data.nTweak - A random value to seed the hash functions.
 * @param {Number} data.nFlag - A flag to determine how matched items are added to the filter.
 * @constructor
 */
function Filter(arg) {
  /* jshint maxcomplexity: 10 */
  if (typeof(arg) === 'object') {
    if (!arg.vData) {
      throw new TypeError('Data object should include filter data "vData"');
    }
    if (arg.vData.length > Filter.MAX_BLOOM_FILTER_SIZE * 8) {
      throw new TypeError('"vData" exceeded max size "' + Filter.MAX_BLOOM_FILTER_SIZE + '"');
    }
    this.vData = arg.vData;
    if (!arg.nHashFuncs) {
      throw new TypeError('Data object should include number of hash functions "nHashFuncs"');
    }
    if (arg.nHashFuncs > Filter.MAX_HASH_FUNCS) {
      throw new TypeError('"nHashFuncs" exceeded max size "' + Filter.MAX_HASH_FUNCS + '"');
    }
    this.nHashFuncs = arg.nHashFuncs;
    this.nTweak = arg.nTweak || 0;
    this.nFlags = arg.nFlags || Filter.BLOOM_UPDATE_NONE;
  } else {
    throw new TypeError('Unrecognized argument');
  }
}

Filter.prototype.toObject = function toObject() {
  return {
    vData: this.vData,
    nHashFuncs: this.nHashFuncs,
    nTweak: this.nTweak,
    nFlags: this.nFlags
  };
};

Filter.create = function create(elements, falsePositiveRate, nTweak, nFlags) {
  /* jshint maxstatements: 18 */

  var info = {};

  // The ideal size for a bloom filter with a given number of elements and false positive rate is:
  // * - nElements * log(fp rate) / ln(2)^2
  // See: https://github.com/bitcoin/bitcoin/blob/master/src/bloom.cpp
  var size = -1.0 / Filter.LN2SQUARED * elements * Math.log(falsePositiveRate);
  var filterSize = Math.floor(size / 8);
  var max = Filter.MAX_BLOOM_FILTER_SIZE * 8;
  if (filterSize > max) {
    filterSize = max;
  }
  info.vData = [];
  for (var i = 0; i < filterSize; i++) {
    info.vData.push(0);
  }

  // The ideal number of hash functions is:
  // filter size * ln(2) / number of elements
  // See: https://github.com/bitcoin/bitcoin/blob/master/src/bloom.cpp
  var nHashFuncs = Math.floor(info.vData.length * 8 / elements * Filter.LN2);
  if (nHashFuncs > Filter.MAX_HASH_FUNCS) {
    nHashFuncs = Filter.MAX_HASH_FUNCS;
  }
  if (nHashFuncs < Filter.MIN_HASH_FUNCS) {
    nHashFuncs = Filter.MIN_HASH_FUNCS;
  }

  info.nHashFuncs = nHashFuncs;
  info.nTweak = nTweak;
  info.nFlags = nFlags;

  return new Filter(info);

};

Filter.prototype.hash = function hash(nHashNum, vDataToHash) {
  var h = MurmurHash3(((nHashNum * 0xFBA4C795) + this.nTweak) & 0xFFFFFFFF, vDataToHash);
  return h % (this.vData.length * 8);
};

Filter.prototype.insert = function insert(data) {
  for (var i = 0; i < this.nHashFuncs; i++) {
    var index = this.hash(i, data);
    var position = (1 << (7 & index));
    this.vData[index >> 3] |= position;
  }
  return this;
};

/**
 * @param {Buffer} Data to check if exists in the filter
 * @returns {Boolean} If the data matches
 */
Filter.prototype.contains = function contains(data) {
  if (!this.vData.length) {
    return false;
  }
  for (var i = 0; i < this.nHashFuncs; i++) {
    var index = this.hash(i, data);
    if (!(this.vData[index >> 3] & (1 << (7 & index)))) {
      return false;
    }
  }
  return true;
};

Filter.prototype.clear = function clear() {
  this.vData = [];
};

Filter.prototype.inspect = function inspect() {
  return '<BloomFilter:' +
    this.vData + ' nHashFuncs:' +
    this.nHashFuncs + ' nTweak:' +
    this.nTweak + ' nFlags:' +
    this.nFlags + '>';
};

Filter.BLOOM_UPDATE_NONE = 0;
Filter.BLOOM_UPDATE_ALL = 1;
Filter.BLOOM_UPDATE_P2PUBKEY_ONLY = 2;
Filter.MAX_BLOOM_FILTER_SIZE = 36000; // bytes
Filter.MAX_HASH_FUNCS = 50;
Filter.MIN_HASH_FUNCS = 1;
Filter.LN2SQUARED = Math.pow(Math.log(2), 2); // 0.4804530139182014246671025263266649717305529515945455
Filter.LN2 = Math.log(2); // 0.6931471805599453094172321214581765680755001343602552

module.exports = Filter;
