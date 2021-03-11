# wpcheck

`wpcheck` is a Node.js CLI tool that allows you to quickly scan WordPress sites looking for known vulnerabilities, security issues and misconfigurations. `wpcheck` helps you secure and maintain your WordPress against hackers.

[![Dependency Status](https://david-dm.org/sergejmueller/wpcheck.svg)](https://david-dm.org/sergejmueller/wpcheck)
[![Code Climate](https://codeclimate.com/github/sergejmueller/wpcheck/badges/gpa.svg)](https://codeclimate.com/github/sergejmueller/wpcheck)
[![Build Status](https://travis-ci.org/sergejmueller/wpcheck.svg?branch=master)](https://travis-ci.org/sergejmueller/wpcheck)
[![Known Vulnerabilities](https://snyk.io/test/github/sergejmueller/wpcheck/badge.svg)](https://snyk.io/test/github/sergejmueller/wpcheck)


### Features

- [Preinstalled rules](#default-rules) for a quick start.
- [Custom rules](#custom-rules) increase the functionality.
- [Selectively ignore](#ignore-rules) default and custom rules.
- Multiple WordPress scans from a [bulk file](#bulk-scan).
- Detection for
  - WordPress directories (`wp-content`, ...).
  - WordPress installed in a subdirectory.
- Changeable User-Agent string.
- Silent mode displays warnings only.
- Fix issues: [WordPress security best practices](HOWTO.md).
- Beginner friendly, easy to install.
- Lightweight, cross plattform framework.
- **Work in progress**, see [todos](TODO.md) and [changelog](CHANGELOG.md).


### Install

```bash
npm install --global wpcheck
```

*or*

```bash
yarn global add wpcheck
```

##### Notes
* `wpcheck` requires `Node.js >= 6` and [npm](http://blog.npmjs.org/post/85484771375/how-to-install-npm).
* [Fix](https://docs.npmjs.com/getting-started/fixing-npm-permissions) `npm` permissions if you get the `Permission denied` error.


### Usage

```bash
wpcheck <url> [url] [options]
```

`url` → WordPress site URL (e.g. `https://ma.tt`)

Multiple URLs can be separated by spaces.


### Options

Option | Shortcut | Description
------ | -------- | -----------
`--help` | `-h` | Outputs supplied help text.
`--silent` | `-s` | Disables success and info messages. Displays warnings only.
`--version` | `-v` | Prints `wpcheck` version.
`--rules-dir` | `-r` | Loads additional rules from a directory (see [Custom rules](#custom-rules)).
`--bulk-file` | `-b` | Reads additional WordPress site URLs from a text file (see [Bulk scan](#bulk-scan)).
`--ignore-rule` | `-i` | Skips loading and execution of a specific rule (see [Ignore rules](#ignore-rules)).
`--user-agent` | `-u` | Defines a custom `User-Agent` string. Default is `wpcheck`.


### Quick examples

```bash
wpcheck https://ma.tt
wpcheck https://ma.tt --silent
wpcheck https://ma.tt --rules-dir ~/path/to/custom/rules
wpcheck https://ma.tt --bulk-file ~/path/to/sources.txt
wpcheck https://ma.tt --user-agent "Netscape Gold"
wpcheck https://ma.tt --ignore-rule wp-login.js
```


### Default rules

`wpcheck` has a few rules that are enabled by default. Follow also our [WordPress security best practices](HOWTO.md) to fix vulnerabilities detected by `wpcheck` default rules.

##### 1. Checks sensitive WordPress/Apache/Dot files for their availability
  - `/wp-config.php`
  - `/wp-admin/maint/repair.php`
  - `/.htaccess`
  - `/.htpasswd`
  - `/.ssh`
  - `/.npmrc`
  - `/.gitconfig`
  - `/config.json`
  - `/wp-config-sample.php`
  - `/wp-content/debug.log`

##### 2. Scans WordPress login page for security issues
  - Basic access authentication
  - HTTPS protocol usage

##### 3. Checks whether WordPress is affected by FPD vulnerability

##### 4. Checks whether the Apache directory listing is activated


### Custom rules

The power of `wpcheck` is the flexibility: You can expand the tool functionality by building their own rules, scans and checks. The option `--rules-dir` allows loading of user-defined rules from a custom directory.

- The directory path
  - can be absolute or relative to the `wpcheck` folder
- The custom rules
  - must be stored as `.js` files
  - can be a `Node.js` script
  - can be a `npm` package
  - must have an exported function named `fire`

```javascript
exports.fire = ( data ) => {
    // Play with data
    // console.log( data )
}
```

`wpcheck` will run (technically `require`) every custom rule file. The file naming does not matter, short and unique names are welcome. Feel free to create your own rules, enjoy!

##### Get inspired
- [example custom rules](example/rules)
- [wpcheck default rules](lib/rules)


### Ignore rule(s)

`wpcheck` can skip certain [default](lib/rules) and custom rules. The CLI option `--ignore-rule` takes a rule name, the rule name is the JavaScript file name of the rule without path. Multiple rule filtering is possible by a multiple use of the CLI option.

```bash
wpcheck ma.tt --ignore-rule wp-login.js
wpcheck ma.tt --ignore-rule wp-login.js --ignore-rule sensitive-files.js
wpcheck ma.tt --rules-dir ./example/rules --ignore-rule custom-rule.js
```


### Bulk scan

Multiple WordPress site URLs can be imported from a single file. This is a simple text file with one URL per line.

```bash
wpcheck -b ~/path/to/sources.txt
```


### Use, don't abuse!
