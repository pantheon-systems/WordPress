# wpcheck / CHANGELOG


### 1.1.4 (2018-02-19)

##### Changes
* Update outdated packages


### 1.1.3 (2017-06-29)

##### Changes
* Update outdated packages


### 1.1.2 (2017-03-10)

##### Changes
* Update outdated packages


### 1.1.1 (2016-11-05)

##### Changes
* `.travis.yml`: Add `npm link` as `before_script`
* `package.json`: Update `request` to `v2.78.0`
* `package.json`: Remove `npm outdated` from `npm test`


### 1.1.0 (2016-11-03)

##### New

* `wpcheck [-v | --version]` to print `wpcheck` version


### 1.0.0 (2016-11-02)

##### New
* Rename project into `wpcheck` (closes #29)
* Add ESLint `node` plugin
* Add `yarn.lock` file

##### Changes
* `.eslintrc.json`: Rename `.eslintrc`
* `.gitignore`: Remove `*.lock` and `.idea`
* `README.md`: Add `yarn add global` command


### v0.7.2 (2016-10-08)

##### Changes
* Travis: Remove `node_js` v4 & v5
* package.json: Update `mocha`, `request`, `eslint`, `child-process-promise`
* Readme: Set Node.js version to `>= 6`
* package.json: Set `engines.node` to `>= 6`
* gitignore: Add `.idea` rule
* Closes #27


### v0.7.1 (2016-08-15)

##### Changes
* Readme: Fixes a few typos (#24)
* Readme: Remove `[root]` prefix from `install` command (#23)


### v0.7.0 (2016-08-04)

##### New
* Rule: Scan for Apache Directory Listing

##### Changes
* Test: Add *Directory listing* tests
* Howto: Add *Directory listing* part
* Todo: Remove *Directory listing* part
* Readme: Text changes
* package.json: Update `mocha` version


### v0.6.1 (2016-08-02)

##### Changes
* Test: Verify filter naming output
* Todo: Remove & restructure tasks
* Readme: Text changes
* package.json: Update `eslint` version


### v0.6.0 (2016-08-01)

##### New
* Rule: Scan WordPress for FPD vulnerability

##### Changes
* Core: Add filter name to the log output
* Core: Add module description to `wpcheck --help`
* Core: Add function `fileName` to `lib/finder.js`
* Rule: Rename `file-exists.js` into `sensitive-files.js`
* Rule: Rename `finder.js` into `fs.js`
* Rule: Refactor all wpcheck rules
* Test: Refactor some test rules
* Readme: Text changes
* Howto: Text changes
* package.json: Update `mocha` and `eslint` versions


### v0.5.5 (2016-07-28)

##### Changes
* Core: Add `new Error()` to `Promise` rejects
* Misc: Add `HOWTO.md` with WordPress security tips
* Readme: Text changes


### v0.5.4 (2016-07-27)

##### Changes
* Core: Refactor `file-exists.js` rule library
* Test: Extract testcase domain into `config/test.json`
* package.json: Add `config` to the `files` array
* package.json: Set `preferGlobal` to `true`


### v0.5.3 (2016-07-26)

##### Changes
* Core: Refactor all JS files for ES6 support
* Core: Add `url.js` as a new `wpcheck` module
* Core: Remove `app-module-path` `npm` module
* Core: Remove `helpers.js` `wpcheck` module
* Core: Update `request` `npm` module to v2.74.0
* Test: Use a testcase domain
* Readme: Text changes


### v0.5.2 (2016-07-22)

##### Changes
* Core: Add new library [finder.js](lib/finder.js) with file system functions
* Core: Refactor [app.js](lib/app.js) in association with [finder.js](lib/finder.js)
* Core: Remove `makeAbsolute` function from [helpers.js](lib/helpers.js)
* package.json: Add `npm outdated` to `npm test`
* Readme: Add *Features* part
* Readme: More text changes


### v0.5.1 (2016-07-21)

##### Changes
* Core: Split `config.json` into separate config files
* Core: Outsource `help` functionality into `lib/help.js`
* package.json: Update `must` and `eslint` versions


### v0.5.0 (2016-07-20)

##### New
* Option: `--ignore-rule` skips execution of a specific rule
* Core: Add Node.js version check
* Test: Add `--ignore-rule` cases

##### Changes
* Readme: Text changes
* Test: `must.include` instead of `must.have.string`


### v0.4.2 (2016-07-18)

##### New
* Option: `--help` outputs supplied help text
* Core: Add timeout to initial app requests
* Test: Add `wpcheck --help` case
* Readme: Add `Default rules` part
* Readme: Add `--help` option

##### Changes
* Core: Refactor app singletons
* Example: Rename `./examples` → `./example`
* Example: Rename `sources.txt` → `sources/list.txt`
* package.json: Set `repository` → `sergejmueller/wpcheck`
* package.json: Set `engines.node` → `>=4`
* package.json: Set `files` → `["lib","index.js","config.json"]`


### v0.4.1 (2016-07-15)

##### Changes
* Rule: Sensitive dotfiles availability check
* Core: Remove `prepend` helper function
* Readme: Text changes


### v0.4.0 (2016-07-14)

##### New
* Option: `--bulk-file` reads sources/URLs from a file
* Core: Add helper function `makeAbsolute`
* Test: Bulk file tests
* Rule: Availability check for `wp-config-sample.php`
* Examples: Add bulk file `sources.txt`

##### Changes
* Core: Refactor `index.js`
* Core: Export config data into `config.json`
* Core: Set the default User-Agent to `wpcheck`
* Core: Split error and status code warnings
* Readme: Text changes


### v0.3.0 (2016-07-04)

##### New
* Rule: Scan WordPress login page for vulnerabilities
* Option: Custom `User-Agent` string via `--user-agent`

##### Changes
* Test: Replace `chai` testing library by `must`
* Core: Rename `data` object key `silent` to `silentMode`
* Readme: Text changes
* Travis: Add `node_js` v4


### v0.2.2 (2016-07-03)

##### New
* Test: Add new mocha tests
* Test: Add comments to all mocha tests

##### Changes
* ESLint: Bump to v3.0.0
* Core: Rename app lib `message` to `log`
* Core: Rename `message.success` to `log.ok`
* Core: Remove function `message.die` and replace by `log.warn`


### v0.2.1 (2016-07-01)

##### Changes
* Core: Add `normalizeURL` function with `validUrl` check
* Readme: Text changes


### v0.2.0 (2016-06-30)

##### New
* Test: Add multiple mocha tests
* Core: URI autocomplete for CLI commands (`ma.tt → http://ma.tt`)
* Core: Extended functionality for CLI arguments handling
* Core: Shortcuts for CLI options (`-s` → `--silent` & `-r` → `--rules-dir`)

##### Changes
* Travis: Remove `node_js` versions <= 4
* ESLint: Add `mocha: true` to `env` arguments
* Codeclimate: Add `test` folder to `exclude_paths`


### v0.1.2 (2016-06-27)

##### New
* Readme: Add badges ;)

##### Changes
* Readme: Reorganize text blocks
* ESLint: Embed path to `.eslintrc`
* Codeclimate: Set `mass_threshold` to `50`


### v0.1.1 (2016-06-27)

##### New
* Examples: Add example file for custom rules

##### Changes
* Readme: Add more example snippets
* Readme: Add description for custom rules
* ESLint: Check all JS files
* ESLint: Add `sourceType: module` property
* Core: Replace `var` by `const` when it's necessary
* Core: Add `silent` state to `message.(success|notice)` calls
* Core: Better error handling for rules loading


### v0.1.0 (2016-06-26)

##### New
* Core functionality
* Option: Silent mode
* Option: Load additional rules from a custom directory
* Rule: System files exists
