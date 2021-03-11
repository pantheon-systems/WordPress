'use strict';

var tap = require('tap');
var fs = require("fs");
var path = require("path");
var streamBuffers = require("stream-buffers");
var async = require('async');
var PullStream = require('../');

tap.test("source sending 1-byte at a time", function (t) {
  t.plan(3);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('finish', function () {
    sourceStream.destroy();
  });

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());

    var writableStream = new streamBuffers.WritableStreamBuffer({
      initialSize: 100
    });
    writableStream.on('close', function () {
      var str = writableStream.getContentsAsString('utf8');
      t.equal(' World', str);

      ps.pull(function (err, data) {
        if (err) {
          return t.end(err);
        }
        t.equal('!', data.toString());
        return t.end();
      });
    });

    ps.pipe(' World'.length, writableStream);
  });
});

tap.test("source sending twelve bytes at once", function (t) {
  t.plan(3);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('finish', function () {
    sourceStream.destroy();
  });

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());

    var writableStream = new streamBuffers.WritableStreamBuffer({
      initialSize: 100
    });
    writableStream.on('close', function () {
      var str = writableStream.getContentsAsString('utf8');
      t.equal(' World', str);

      ps.pull(function (err, data) {
        if (err) {
          return t.end(err);
        }
        t.equal('!', data.toString());
        return t.end();
      });
    });

    ps.pipe(' World'.length, writableStream);
  });
});

tap.test("source sending 512 bytes at once", function (t) {
  t.plan(512 / 4);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('finish', function() {
    sourceStream.destroy();
  });

  var values = [];
  for (var i = 0; i < 512; i += 4) {
    values.push(String(i + 1000));
  }
  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  values.forEach(function(val) {
    sourceStream.put(val);
  });

  async.forEachSeries(values, function (val, callback) {
    ps.pull(4, function (err, data) {
      if (err) {
        return callback(err);
      }
      t.equal(val, data.toString());
      return callback(null);
    });
  }, function (err) {
    t.end(err);
  });
});

tap.test("two length pulls", function (t) {
  t.plan(2);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('finish', function () {
    sourceStream.destroy();
  });

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());

    ps.pull(' World!'.length, function (err, data) {
      if (err) {
        return t.end(err);
      }
      t.equal(' World!', data.toString());
      return t.end();
    });
  });
});

tap.test("pulling zero bytes returns empty data", function (t) {
  t.plan(1);
  var ps = new PullStream({ lowWaterMark: 0 });

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull(0, function (err, data) {
    if (err) {
      return t.end(err);
    }

    t.equal(0, data.length, "data is empty");
    sourceStream.destroy();
    return t.end();
  });
});

tap.test("read from file", function (t) {
  t.plan(2);
  var ps = new PullStream({ lowWaterMark: 0 });

  var sourceStream = fs.createReadStream(path.join(__dirname, 'testFile.txt'));

  sourceStream.pipe(ps);

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());

    ps.pull(' World!'.length, function (err, data) {
      if (err) {
        return t.end(err);
      }
      t.equal(' World!', data.toString());
      return t.end();
    });
  });
});

tap.test("read past end of stream", function (t) {
  t.plan(2);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('finish', function () {
    sourceStream.destroy();
  });

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 1,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello World!'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello World!', data.toString());

    ps.pull(1, function (err) {
      if (err) {
        t.ok(err, 'should get an error');
      }
      t.end();
    });
  });
});

tap.test("pipe with no length", function (t) {
  t.plan(2);
  var ps = new PullStream({ lowWaterMark: 0 });
  ps.on('end', function () {
    t.ok(true, "pullstream should end");
  });

  var writableStream = new streamBuffers.WritableStreamBuffer({
    initialSize: 100
  });
  writableStream.on('close', function () {
    var str = writableStream.getContentsAsString('utf8');
    t.equal('Hello World!', str);
    t.end();
  });

  ps.pipe(writableStream);

  setImmediate(function () {
    ps.write(new Buffer('Hello', 'utf8'));
    ps.write(new Buffer(' World', 'utf8'));
    setImmediate(function () {
      ps.write(new Buffer('!', 'utf8'));
      ps.end();
    });
  });
});

tap.test("emit error on calling write() after end", function (t) {
  t.plan(2);

  var ps = new PullStream({ lowWaterMark: 0 });
  ps.end();

  ps.on('error', function (err) {
    t.ok(err);
  });

  setImmediate(function () {
    ps.write(new Buffer('hello', 'utf8'), function (err) {
      t.ok(err);
      t.end();
    });
  });
});

tap.test("pipe more bytes than the pullstream buffer size", function (t) {
  t.plan(1);
  var ps = new PullStream();
  ps.on('end', function() {
    sourceStream.destroy();
  });

  var aVals = "", bVals = "";
  for (var i = 0; i < 20 * 1000; i++) {
    aVals += 'a';
  }
  for (var j = 0; j < 180 * 1000; j++) {
    bVals += 'b';
  }
  var combined = aVals + bVals;

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 40 * 1024
  });

  sourceStream.pipe(ps);
  sourceStream.put(aVals);

  var writableStream = new streamBuffers.WritableStreamBuffer({
    initialSize: 200 * 1000
  });
  writableStream.on('close', function () {
    var str = writableStream.getContentsAsString('utf8');
    t.equal(combined, str);
    t.end();
  });

  ps.once('drain', function () {
    ps.pipe(200 * 1000, writableStream);
    setImmediate(sourceStream.put.bind(null, bVals));
  });
});

tap.test("mix asynchronous pull with synchronous pullUpTo - exact number of bytes returned", function (t) {
  t.plan(2);
  var ps = new PullStream();

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());
    var data2 = ps.pullUpTo(" World!".length);
    t.equal(" World!", data2.toString());
    sourceStream.destroy();
    t.end();
  });
});

tap.test("mix asynchronous pull with pullUpTo - fewer bytes returned than requested", function (t) {
  t.plan(2);
  var ps = new PullStream();

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");


  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());
    var data2 = ps.pullUpTo(1000);
    t.equal(" World!", data2.toString());
    sourceStream.destroy();
    t.end();
  });
});

tap.test("retrieve all currently remaining bytes", function (t) {
  t.plan(2);
  var ps = new PullStream();

  var sourceStream = new streamBuffers.ReadableStreamBuffer({
    frequency: 0,
    chunkSize: 1000
  });

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.pull('Hello'.length, function (err, data) {
    if (err) {
      return t.end(err);
    }
    t.equal('Hello', data.toString());
    var data2 = ps.pullUpTo();
    t.equal(" World!", data2.toString());
    sourceStream.destroy();
    t.end();
  });
});

// TODO: node PassThrough stream doesn't handle unshift the same way anymore.
//  tap.test("prepend", function (t) {
//    t.plan(1);
//    var ps = new PullStream();
//
//    var sourceStream = new streamBuffers.ReadableStreamBuffer();
//
//    sourceStream.pipe(ps);
//    sourceStream.put("World!");
//    ps.prepend("Hello ");
//
//    ps.pull('Hello World!'.length, function (err, data) {
//      if (err) {
//        return t.end(err);
//      }
//      t.equal('Hello World!', data.toString());
//      sourceStream.destroy();
//      t.end();
//    });
//  });

tap.test("drain", function (t) {
  t.plan(2);
  var ps = new PullStream();

  var sourceStream = new streamBuffers.ReadableStreamBuffer();

  sourceStream.pipe(ps);
  sourceStream.put("Hello World!");

  ps.drain('Hello '.length, function (err) {
    if (err) {
      return t.end(err);
    }
    ps.pull('World!'.length, function (err, data) {
      t.error(err);
      t.equal('World!', data.toString());
      sourceStream.destroy();
      t.end();
    });
  });
});
