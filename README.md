Composer Init
=============

[![Build Status](https://travis-ci.org/clippings/composer-init.png?branch=master)](https://travis-ci.org/clippings/composer-init)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/composer-init/badges/quality-score.png?s=a1404674f68c4894d651150caf4985aa59597515)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/composer-init/badges/coverage.png?s=3d5fb55c42c6887679915320658b543ed935e00a)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Latest Stable Version](https://poser.pugx.org/clippings/composer-init/v/stable.png)](https://packagist.org/packages/clippings/composer-init)

Initialize a project based on a template, inspired by [grunt-init](https://github.com/gruntjs/grunt-init)

Instalation
-----------

Install via composer

```
composer global require clippings/composer-init
```

This will install it to your user's global composer. If you already have ~/.composer/vendor/bin/ in your PATH you can start using it with
```
composer-init
```
otherwise you can do it by calling directly
```
~/.composer/vendor/bin/composer-init
```

Basic Usage
-----------

- ``composer-init search`` to discover templates
- ``composer-init use {template-package}`` in an empty folder to use a template

Composer Init asumes the package is on github, so if you create a github repo first, it will get data like title/description issues url from it

Creating Templates
------------------

Templates are just files with placeholders, and a prompts.json file to tell how to fill those places

Here's a list of available prompts:

- author_email : Use git config user.email as default
- author_name : Use git config user.name as default
- bugs : "Submit new bug / issues url" defaults to gihub.com/:repo/issues/new url
- copyright : "{year}, {copyright holder}" where copyright holder defaults to github organization / github user
- description : Defaults to github description
- php_namespace : Use package name to guess phpnamespace
- package_name : Package name
- slack_notification : Enter an encrypted slack key, to be easily put in a travis.yml file
- title : Defaults to github title

Here's an example:
https://github.com/clippings/package-template/

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.
