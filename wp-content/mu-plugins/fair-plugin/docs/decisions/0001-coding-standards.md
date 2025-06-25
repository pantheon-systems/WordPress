---
# These are optional metadata elements. Feel free to remove any of them.
status: proposed
date: 2025-04-19
---

# Coding Standards

## Context and Problem Statement

In order to work effectively as a team, we need to agree on coding standards for consistency.


## Considered Options

* WP Core standard ("WordPress" in wpcs)
* PSR-12
* A variant of either
* Other modernised WP standards from the ecosystem


## Rationale

We have two primary options to pick from, namely WP' standards or an industry standard like PSR-4.

Given the nature of this project as something that may become part of core itself, staying close to the patterns already used in core would assist with merging the project in the future. Additionally, with a large number of contributors who are familiar with WP core, there's a greater familiarity generally with this style than with rest-of-PHP standards like PSR-4.

However, the WP standards don't fully account for some modern coding techniques, like namespaces. The norms of core development can also be outdated in various aspects, such as long arrays (`array()` instead of `[]`) or the use of classes as pseudo-namespaces (since much of core predates real namespaces). Alternative WordPress-based standards such as the Human Made standard account for this while remaining "WordPress-y", but are by their nature less familiar.


## Decision Outcome

Proposed outcome is a standard derived from the WordPress coding standards, with customization as necessary inspired by the Human Made standard. This achieves a good balance of familiarity and modernity.


## Coding Standard Details

This project uses the WP coding standards, with various additions based on other standards (such as the Human Made coding standards).

The following represent the changes and additions compared to the standard WP coding standards.


### File Layout

PHP files should either declare symbols (classes, functions, etc) or run code (function calls, etc), but not both. The only place that code is run at the top-level outside of a function/class should be in the root `plugin.php`

(An exception is allowed for `require` statements only if there isn't a better way to include those files, but preferentially these should be included in the top-level `plugin.php`)

Classes must be in their own file, which should not declare any other functions or classes, or run any code.

Generally, the file should follow the following order (with each group separated by a blank line):

* `namespace` declaration
* `use` declarations
* `const` declarations for the namespace
* require when allowed
* Declarations or run code

For namespaced functions which are primarily action and filter callbacks, the `add_action`/`add_filter` calls should generally live in a `bootstrap()` function in the namespace. This allows the file to be loaded without adding hooks immediately, but still allows the hook declarations to live with the callbacks.


### Project Layout and File Naming

`inc/` contains the bulk of the code for the project, with only necessary files (like the `plugin.php` entrypoint) living outside of it.

Each directory within `inc/` directly represents a "module" of functionality within FAIR, and is designed so that each module may be maintained independently. Generally speaking, avoid adding any functionality directly outside of a module unless it is strictly necessary.

Namespaces (excluding the parent `FAIR` namespace) are mapped to filesystem directories (or files) by lower-casing them and replacing underscores with dashes. For example, `FAIR\Foo_Bar\Quux\Zorb` becomes `inc/foo-bar/quux/zorb`.

If a namespace consists entirely of functions and constants, contains no classes, and contains no sub-namespaces, it can live in a file directly. For example, `FAIR\Foo_Bar\Quux` could live at `inc/foo-bar/quux.php`.

Namespaces which have sub-namespaces or have classes must use a directory instead, with any namespace symbols (functions, constants, etc) in a `namespace.php` file inside this directory.

Generally, you should use the directory style when you expect more classes or subnamespaces, and you should use the file style when you expect this to be functions only. Both styles have their place, and you should carefully consider the future of this code when you decide.

For example, the file layout could look like:

```sh
inc/                               # namespace FAIR
inc/foo/                           # namespace FAIR\Foo
inc/foo/class-bar.php              # namespace FAIR\Foo, class Bar
inc/foo/namespace.php              # namespace FAIR\Foo, functions
inc/quux/                          # namespace FAIR\Quux
inc/quux/namespace.php             # namespace FAIR\Quux, functions
inc/quux/zorb/                     # namespace FAIR\Quux\Zorb
inc/quux/zorb/class-waldo.php      # namespace FAIR\Quux\Zorb, class Waldo
inc/quux/zorb/thud.php             # namespace FAIR\Quux\Zorb\Thud, functions
inc/quux/zorb/xyzzy.php            # namespace FAIR\Quux\Zorb\Xyzzy, functions
```


### Namespaces and Classes

FAIR is built for a modern era of PHP, and so is built namespace-first. Generally, non-stateful functions should live in namespaces, and classes should be used only for object-oriented, stateful needs.

Namespaces should be logical groupings of related functionality. Typically, this means along feature lines, not along technology lines; a WP-CLI command for example should live alongside the functionality it serves, rather than in a dedicated `FAIR\CLI` namespace.

Where functions or classes from other namespaces are used, the namespace should be explicitly `use`'d into the file. You should `use` classes directly, but avoid `use function`, as this gets confusing with the overlap of large number of un-prefixed functions in WP. `use` statements should not start with a redundant `\`, and there's no need to `use` global functions.

Avoid aliasing unless you need to, as it reduces the readability of the code.

```php
// Good:
use FAIR\Foo;
use FAIR\Foo\Bar;

Foo\some_function();
new Bar();

// Bad:
use \FAIR\Foo;
use function FAIR\Foo\some_function();
\get_option();
```


### Classes

Use classes for places where you need objects, and avoid using them only for pseudo-namespacing. (A class composed of all `static` functions should likely be a namespace instead.)

Class methods and properties should always be marked with a visibility keyword, one of `public`, `protected` or `private`.

Generally speaking, `private` should be avoided in favour of `protected`, as it doesn't allow subclasses to access the method. In most cases, this is an unnecessary hindrance, and leads to subclasses simply copy-and-pasting the code when they need to override it.

In some cases, for forward compatibility, `private` or `final` may be required. Use these sparingly.


### Statements

Avoid using Yoda-style backwards statements, as they hurt readability.

Anonymous functions should use nacin-spacin, with extra spaces between elements. This applies both for long (`function ()`) and short (`fn ()`) functions:

```php
// Good:
function () {}
function ( $x ) {}
function ( $x ) use ( $y ) {}
fn ( $x ) => $x + 1

// Bad:
function() {}
function( $x ) {}
function( $x ) use( $y ) {}
function( $x ) use ( $y ) {}
fn($x)=>$x+1
fn($x) => $x + 1
```

Arrays should be specified as short arrays (`[]`). Unless the array is a list of short items (such as a few integers or a couple of strings), each item should be on its own line.


### Documentation

Generally, follow the WP standard for inline documentation.

An exception is made for the description. Descriptions should be written in the imperative (as a command) rather than third-person indicative. This is the **opposite** of what WordPress coding standards recommend.

This is an intentional stylistic choice and deviation from the WordPress coding standards. The WordPress standards produce an incomplete sentence, missing a subject, which can be confusing. The imperative reads more clearly in many cases.

The short description should be able to replace the function name. Doing this for all your functions should produce a sentence that describes what you're doing (just like a flowchart!):

* Bad: "Queries the database. Formats the data for output."
* Good: "Query the database. Format the data."
