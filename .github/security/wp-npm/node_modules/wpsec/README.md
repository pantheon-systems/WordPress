Wordpress Verifier
==================

A simple tool to verify wordpress installation and look for modified files.

Install with `npm install -g wpsec`

Use
----

```
wpsec scan /path/to/wordpress
```

Will print a list of files that differ from the Wordpress distribution that is currently installed in that directory, or differ from the distribution of plugin or theme..

Caveats
-------

For speed this tool uses a bloom filter to verify files against. There is a possibility of false negatives.
