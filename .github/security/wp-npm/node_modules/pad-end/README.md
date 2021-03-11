# pad-end <sup>[![Version Badge](http://versionbadg.es/gearcase/pad-end.svg)](https://npmjs.org/package/pad-end)</sup>

> ES7 spec-compliant String.prototype.padEnd shim.

[![MIT License](https://img.shields.io/badge/license-MIT_License-green.svg?style=flat-square)](https://github.com/gearcase/pad-end/blob/master/LICENSE)

[![build:?](https://img.shields.io/travis/gearcase/pad-end/master.svg?style=flat-square)](https://travis-ci.org/gearcase/pad-end)
[![coverage:?](https://img.shields.io/coveralls/gearcase/pad-end/master.svg?style=flat-square)](https://coveralls.io/github/gearcase/pad-end)



## Install

```
$ npm install --save pad-end 
```


## Usage

> For more use-cases see the [tests](https://github.com/gearcase/pad-end/blob/master/test/spec/index.js)

```js

// a polyfill that doesn't overwrite the native method

var padEnd = require('pad-end');

padEnd('x', 4, 'ab');        // => 'xaba'
padEnd('x', 4);              // => 'x   '
padEnd('abcd', 2, '#');      // => 'abcd'
padEnd('abcd', 6, '123456'); // => 'abcd12'

```


## Related

- [pad-start](https://github.com/gearcase/pad-start) - ES spec-compliant String.prototype.padStart shim.
- [start-with](https://github.com/gearcase/start-with) - Determines whether a string begins with the characters of another string.
- [end-with](https://github.com/gearcase/end-with) - Determines whether a string ends with the characters of another string.



## Contributing
 
Pull requests and stars are highly welcome. 

For bugs and feature requests, please [create an issue](https://github.com/gearcase/pad-end/issues).
