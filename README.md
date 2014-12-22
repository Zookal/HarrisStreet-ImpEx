HarrisStreet ImpEx for Magento
===============================

Import and Export of Magentos configuration stored in core_config_data table. Handling of different environments with inheritance.

There is an [awesome blogpost](http://magerun.net/harrisstreet-impex-for-magento/) from [@cmuench](https://twitter.com/cmuench) about this module!

Supports multiple formats like:

- CSV
- Json
- LimeSodaXml [LimeSoda EnvironmentConfiguration](https://github.com/LimeSoda/LimeSoda_EnvironmentConfiguration)
- Xml
- Yaml
- ASCII [Text File formats – ASCII Delimited Text – Not CSV or TAB delimited text](https://ronaldduncan.wordpress.com/2009/10/31/text-file-formats-ascii-delimited-text-not-csv-or-tab-delimited-text/)

At the moment the Yaml import format is the most supported. CSV and JSON works also well.

## Export

```
$ ./n98-magerun.phar hs:ccd:export --help
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

#### Examples

Export hierarchical xml for the `general` and `tax` namespace:

```
$ ./n98-magerun.phar hs:ccd:export --format=xml --include=general,tax --hierarchical=y
```

Export all configuration settings each namespace into its own file starting with the prefix `test`:

```
$ ./n98-magerun.phar hs:ccd:export --filePerNameSpace=y --filename=test
Wrote: 12 settings to file test_admin.yaml
Wrote: 118 settings to file test_advanced.yaml
Wrote: 18 settings to file test_bestsellerproductslider.yaml
Wrote: 6 settings to file test_bss.yaml
Wrote: 294 settings to file test_carriers.yaml
Wrote: 61 settings to file test_catalog.yaml
Wrote: 12 settings to file test_cataloginventory.yaml
Wrote: 16 settings to file test_checkout.yaml
...
```

## Import

To import a configuration you need a specific setup of folders in the filesystem.

```
$ ./n98-magerun.phar hs:ccd:import --help
Usage:
 hs:ccd:import [-m|--format[="..."]] [-a|--hierarchical[="..."]] [--base[="..."]] folder environment

Arguments:
 folder                Import folder name
 env                   Environment name. SubEnvs separated by slash e.g.: development/osx/developer01

Options:
 --format (-m)         Format: yaml, json, csv, xml, limeSodaXml (default: "yaml")
 --hierarchical (-a)   Create a hierarchical or a flat structure (not all export format supports that). Enable with: y (default: "n")
 --base                Base folder name (default: "base")
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 --root-dir            Force magento root dir. No auto detection
 --skip-config         Do not load any custom config.
```

#### Folder setup

![Folder Structure](https://raw.githubusercontent.com/Zookal/HarrisStreet-ImpEx/master/doc/folderStructure.png "Folder Structure")

An example import for the development environment of *cyrill* would like:

```
$ ./n98-magerun.phar hs:ccd:import ./configuration/newCoreConfigData development/cyrill
```

#### How does the import work?

Focusing in the printscreen above on the folder: `configuration/newCoreConfigData` and our example import command.

The importer expects always a base folder (also configurable via CLI option) where all default configuration options are store in n-files. It reads all those files and loads their settings into Magento.

In the next step the importer loads all files from the `development` folder but non-resursive and loads that content into Magento. After that it jumps into the folder `cyrill` and loads there the files. Finished!

The output looks like:

```
Processed: ./configuration/newCoreConfigData/base/contacts.yaml with 4 values.
Processed: ./configuration/newCoreConfigData/base/crontab.yaml with 6 values.
Processed: ./configuration/newCoreConfigData/base/currency.yaml with 11 values.
Processed: ./configuration/newCoreConfigData/base/customer.yaml with 33 values.
Processed: ./configuration/newCoreConfigData/base/design.yaml with 40 values.
Processed: ./configuration/newCoreConfigData/base/dev.yaml with 14 values.
Processed: ./configuration/newCoreConfigData/base/general.yaml with 15 values.
Processed: ./configuration/newCoreConfigData/development/test_web.yaml with 36 values.
Processed: ./configuration/newCoreConfigData/development/cyrill/test_web.yaml with 4 values.
```

You are totally free of naming the folders and files.

The file format during import will be detected with its extension. `.yaml` works were as `.yml` won't.

## Convert

Is really useful to create a .magerun file from your configuration to process later that file on a server.

```
$ ./n98-magerun.phar help hs:ccd:convert
Usage:
 hs:ccd:convert [-m|--format[="..."]] [-a|--hierarchical[="..."]] [--base[="..."]] [--export-file[="..."]] folder env

Arguments:
 folder                Import folder name
 env                   Environment name. SubEnvs separated by slash e.g.: development/osx/developer01

Options:
 --base                Base folder name (default: "base")
 --export-file         File name in which the n98 commands shoud be written. If empty -> stdout
```

## Installation

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

History
-------

### 1.0.0

Initial release

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
