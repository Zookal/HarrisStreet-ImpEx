HarrisStreet ImpEx for Magento
===============================

Import and Export of Magentos core_config_data table. Handling of different environments with inheritance.

Supports multiple formats like:

- CSV
- Json
- LimeSodaXml [LimeSoda EnvironmentConfiguration](https://github.com/LimeSoda/LimeSoda_EnvironmentConfiguration)
- Xml
- Yaml
- ASCII [Text File formats – ASCII Delimited Text – Not CSV or TAB delimited text](https://ronaldduncan.wordpress.com/2009/10/31/text-file-formats-ascii-delimited-text-not-csv-or-tab-delimited-text/)

At the moment the Yaml import format is the most supported. Other formats will follow soon.

### Description

This software is pre-alpha.

@todo -> Create folder somewhere in your system. Hierarchical structure for different environments and developers.

### Export

```
$  ./n98-magerun.phar hs:ccd:export --help
Usage:
 hs:ccd:export [-m|--format[="..."]] [-a|--hierarchical[="..."]] [-f|--filename[="..."]] [-i|--include[="..."]] [-x|--exclude[="..."]] [-s|--filePerNameSpace[="..."]] [-c|--exclude-default[="..."]]

Options:
 --format (-m)           Format: yaml, json, csv, xml, limeSodaXml (default: "yaml")
 --hierarchical (-a)     Create a hierarchical or a flat structure (not all export format supports that). Enable with: y (default: "n")
 --filename (-f)         File name into which should the export be written. Defaults into var directory.
 --include (-i)          Path prefix, multiple values can be comma separated; exports only those paths
 --exclude (-x)          Path prefix, multiple values can be comma separated; exports everything except ...
 --filePerNameSpace (-s) Export each namespace into its own file. Enable with: y (default: "n")
 --exclude-default (-c)  Excludes default values (@todo)
 --help (-h)             Display this help message.
 ```


### Import

...

### Running tests

...

Installation
------------

Require this installer in your `composer.json` file:

	"require": {
		…
        "zookal/harris-street-impex": "dev-master",
        …
    }

Or via modman:

```
$ modman clone git@github.com:Zookal/HarrisStreet-ImpEx.git
```

License
-------

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------

Copyright (c) Zookal Pty Ltd, Sydney Australia

Author
------

Cyrill at Schumacher dot fm or cyrill at zookal dot com

[My pgp public key](http://www.schumacher.fm/cyrill.asc)

[@SchumacherFM](https://github.com/SchumacherFM)
