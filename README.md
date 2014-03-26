Harris Street for Magento
=========================

### @todos

- create a flag to remove unnecessary files like RELEASE_NOTES, license [depending on the target]
- create two flags for removing downloader and/or compiler folders/files [depending on the target]
- show a hint for which target and which DVCS branch we're currently building
- if build is for !development then create a confirm question to proceed this build. include also a json flag to switch off this confirmation
- try to separate between Magento frontend and backend. the frontend version has a complete stripped off backend. [magerun-addons uninstall-a-module](https://github.com/kalenjordan/magerun-addons#uninstall-a-module)
- try to build for target: production-be or production-fe or staging-jenkins
- create option either to move htdocs to xxxx-data dir or remove folder
- suppress output of git rm --cached configuration
- preRunCheck() add checks for required php modules like PDO ...
- if a module is `<active>false</active>` in the config xml then remove it completely ... but with care, maybe devs need some classes so we would have an
option to either remove or keep the files.
- integrate the target.json into the composer file and let the user choose for which target he/she wants to build, extend composer.
- this readme
- create a symlink for `/<timestamp>/skin/...` but that needs a webserver rewrite

### Description

This software is pre-alpha.

Composer's script handler to install, update and maintain Magento (>=1.6).

Provides equal (Java-Free!) functionality like Maven or Ants programs but written in plain PHP.

Configuration of Harris Street will be read from your root composer.json file.

After composer install run this modules configures e.g. database connectivity, different configurations for Solr and so on.

Use the composer hooks:  post-install-cmd and pre-install-cmd

Example json [https://github.com/zookal/magento/blob/master/composer.json](https://github.com/zookal/magento/blob/master/composer.json)

This module should be a Zookal refactored version of [https://github.com/zookal/magento-installer](https://github.com/zookal/magento-installer)

### Config Values depending on the environment

- web/cookie/cookie_*
- dev/template/allow_symlink
- each %url% path
- netsuite credentials
- zendesk credentials

### How to magento-composer-installer

For dev env the checkout strategy is symlink while on staging and production the checkout strategy is copy and allow symlinks false

### How to use

Add to your project-based composer.json: Please see the demo composer.json file.


Installation
------------

Require this installer in your `composer.json` file:

	"require": {
		…
        "zookal/harris-street": "dev-master",
        …
    }

Running tests
-------------
Tests must run in process isolation due to instance mocking of Mockery.

	$ git clone …
	$ composer install
	$ phpunit --process-isolation

License
-------

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

Author
------

Cyrill at Schumacher dot fm or cyrill at zookal dot com

[@SchumacherFM](https://github.com/SchumacherFM)
