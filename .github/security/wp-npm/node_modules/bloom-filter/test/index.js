'use strict';

var chai = require('chai');
var should = chai.should();
var assert = require('assert');
var expect = chai.expect;

var Filter = require('../');
var MurmurHash3 = Filter.MurmurHash3;

// convert a hex string to a bytes buffer
function ParseHex(str) {
  var result = [];
  while (str.length >= 2) {
    result.push(parseInt(str.substring(0, 2), 16));
    str = str.substring(2, str.length);
  }
  var buf = new Buffer(result, 16);
  return buf;
}

describe('Bloom', function() {

  describe('MurmurHash3', function() {

    // format: expected, seed, data
    // see: https://github.com/bitcoin/bitcoin/blob/master/src/test/hash_tests.cpp
    var data = [
      [0x00000000, 0x00000000, ''],
      [0x6a396f08, 0xFBA4C795, ''],
      [0x81f16f39, 0xffffffff, ''],
      [0x514e28b7, 0x00000000, '00'],
      [0xea3f0b17, 0xFBA4C795, '00'],
      [0xfd6cf10d, 0x00000000, 'ff'],
      [0x16c6b7ab, 0x00000000, '0011'],
      [0x8eb51c3d, 0x00000000, '001122'],
      [0xb4471bf8, 0x00000000, '00112233'],
      [0xe2301fa8, 0x00000000, '0011223344'],
      [0xfc2e4a15, 0x00000000, '001122334455'],
      [0xb074502c, 0x00000000, '00112233445566'],
      [0x8034d2a0, 0x00000000, '0011223344556677'],
      [0xb4698def, 0x00000000, '001122334455667788']
    ];

    data.forEach(function(d){
      it('seed: "'+d[1].toString(16)+'" and data: "'+d[2]+'"', function() {
        MurmurHash3(d[1], ParseHex(d[2])).should.equal(d[0]);
      });
    });

  });

  // test data from bitcoind
  // see: https://github.com/bitcoin/bitcoin/blob/master/src/test/bloom_tests.cpp
  var a = ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8');
  var b = ParseHex('19108ad8ed9bb6274d3980bab5a85c048f0950c8');
  var c = ParseHex('b5a2c786d9ef4658287ced5914b37a1b4aa32eee');
  var d = ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5');

  describe('Filter', function() {

    it('create with false positive settings', function() {
      var filter = Filter.create(100, 0.1);
      should.exist(filter.vData);
      should.exist(filter.nHashFuncs);
    });

    it('error if missing vData', function(){
      expect(function(){
        var a = new Filter({});
      }).to.throw('Data object should include filter data "vData"');
    });

    it('error if vData exceeds max', function(){
      expect(function(){
        var a = new Filter({vData: Array(10000000)});
      }).to.throw('"vData" exceeded');
    });

    it('error if missing nHashFuncs', function(){
      expect(function(){
        var a = new Filter({vData: [121, 12, 200]});
      }).to.throw('Data object should include number of hash functions');
    });

    it('error if nHashFuncs exceeds max', function(){
      expect(function(){
        var a = new Filter({vData: [121, 12, 200], nHashFuncs: 51});
      }).to.throw('"nHashFuncs" exceeded max size');
    });

    it('error if missing object', function(){
      expect(function(){
        var a = new Filter('unrecognized');
      }).to.throw(Error);
    });

    describe('correctly calculate size of filter and number of hash functions', function() {
      // elements, fprate, expected length, expected funcs
      // calculated with: https://github.com/petertodd/python-bitcoinlib/blob/master/bitcoin/bloom.py
      var data = [
        [2, 0.001, 3, 8],
        [3, 0.01, 3, 5],
        [10, 0.2, 4, 2],
        [100, 0.2, 41, 2],
        [10000, 0.3, 3132, 1]
      ];

      data.forEach(function(d){
        it('elements: "'+d[0]+'" and fprate: "'+d[1]+'"', function() {
          var filter = Filter.create(d[0], d[1]);
          filter.vData.length.should.equal(d[2]);
          filter.nHashFuncs.should.equal(d[3]);
        });
      });

    });

    it('add items and test if they match the filter correctly', function() {
      var filter = Filter.create(3, 0.01);
      filter.insert(a);
      assert(filter.contains(a));
      assert(!filter.contains(b));
      filter.insert(c);
      assert(filter.contains(c));
      filter.insert(d);
      assert(filter.contains(d));
    });

    it('correctly serialize to an object', function() {

      var filter = Filter.create(3, 0.01, 0, Filter.BLOOM_UPDATE_ALL);

      filter.insert(ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8'));
      assert(filter.contains(ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8')));

      // one bit different in first byte
      assert(!filter.contains(ParseHex('19108ad8ed9bb6274d3980bab5a85c048f0950c8')));

      filter.insert(ParseHex('b5a2c786d9ef4658287ced5914b37a1b4aa32eee'));
      assert(filter.contains(ParseHex("b5a2c786d9ef4658287ced5914b37a1b4aa32eee")));

      filter.insert(ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5'));
      assert(filter.contains(ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5')));

      var actual = filter.toObject();

      var expected = {
        vData: [ 97, 78, 155 ],
        nHashFuncs: 5,
        nTweak: 0,
        nFlags: 1
      };

      actual.should.deep.equal(expected);

    });

    it('correctly serialize to an object with tweak', function() {

      var filter = Filter.create(3, 0.01, 2147483649, Filter.BLOOM_UPDATE_ALL);

      filter.insert(ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8'));
      assert(filter.contains(ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8')));

      // one bit different in first byte
      assert(!filter.contains(ParseHex('19108ad8ed9bb6274d3980bab5a85c048f0950c8')));

      filter.insert(ParseHex('b5a2c786d9ef4658287ced5914b37a1b4aa32eee'));
      assert(filter.contains(ParseHex('b5a2c786d9ef4658287ced5914b37a1b4aa32eee')));

      filter.insert(ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5'));
      assert(filter.contains(ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5')));

      var expected = {
        vData: [ 206, 66, 153 ],
        nHashFuncs: 5,
        nTweak: 2147483649,
        nFlags: 1
      };

      var actual = filter.toObject();
      actual.should.deep.equal(expected);

    });

    it('correctly serialize filter with public keys added', function() {

      var filter = Filter.create(2, 0.001, 0, Filter.BLOOM_UPDATE_ALL);

      // WIF: 5Kg1gnAjaLfKiwhhPpGS3QfRg2m6awQvaj98JCZBZQ5SuS2F15C
      filter.insert(new Buffer('045b81f0017e2091e2edcd5eecf10d5bdd120a5514cb3ee65b8447ec18bfc4575c6d5bf415e54e03b1067934a0f0ba76b01c6b9ab227142ee1d543764b69d901e0', 'hex'));
      filter.insert(new Buffer('477abbacd4113f2e6b100526222eedd953c26a64', 'hex'));

      var expectedFilter = new Filter({
        vData: [ 143, 193, 107 ],
        nHashFuncs: 8,
        nTweak: 0,
        nFlags: 1
      });

      filter.toObject().should.deep.equal(expectedFilter.toObject());

    });


    it('correctly deserialize', function() {

      var filter = new Filter({
        vData: [ 97, 78, 155 ],
        nHashFuncs: 5,
        nTweak: 0, nFlags: 1
      });

      assert(filter.contains(ParseHex('99108ad8ed9bb6274d3980bab5a85c048f0950c8')));
      assert(!filter.contains(ParseHex('19108ad8ed9bb6274d3980bab5a85c048f0950c8')));
      assert(filter.contains(ParseHex("b5a2c786d9ef4658287ced5914b37a1b4aa32eee")));
      assert(filter.contains(ParseHex('b9300670b4c5366e95b2699e8b18bc75e5f729c5')));

    });

    it('clear the filter', function() {
      var filter = Filter.create(1, 0.01);
      filter.insert(a);
      assert(filter.contains(a));
      filter.clear();
      assert(!filter.contains(a));
    });

    it('use the max size', function() {
      var filter = Filter.create(900000000000000000000000000000000000, 0.01);
      filter.vData.length.should.equal(Filter.MAX_BLOOM_FILTER_SIZE * 8);
    });

    it('use the max number of hash funcs', function() {
      var filter = Filter.create(10, 0.0000000000000001);
      filter.nHashFuncs.should.equal(Filter.MAX_HASH_FUNCS);
    });

    it('display in the console', function() {
      var filter = Filter.create(3, 0.01);
      filter.insert(a);
      filter.inspect().should.equal('<BloomFilter:1,0,152 nHashFuncs:5 nTweak:0 nFlags:0>');
    });

  });

});
