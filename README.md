Composer Init
=============

[![Build Status](https://travis-ci.org/clippings/composer-init.png?branch=master)](https://travis-ci.org/clippings/composer-init)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/composer-init/badges/quality-score.png?s=a1404674f68c4894d651150caf4985aa59597515)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/composer-init/badges/coverage.png?s=3d5fb55c42c6887679915320658b543ed935e00a)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Latest Stable Version](https://poser.pugx.org/clippings/composer-init/v/stable.png)](https://packagist.org/packages/clippings/composer-init)

Tired of creating the same directory structure every time you start a new project? Tired of constantly modifying files if you clone github template repo? This command line tool allows you to initialize a project based on a template, and fills in values in the template.

Installation
------------

Install via composer

```
$ composer global require clippings/composer-init
```

This will install it to your user's global composer. If you already have ~/.composer/vendor/bin/ in your PATH you can start using it with
```
$ composer-init
```
otherwise you can do it by calling directly
```
$ ~/.composer/vendor/bin/composer-init
```

Basic Usage
-----------

- ``composer-init search`` to discover templates
- ``composer-init use {template-package}`` in an empty folder to use a template
- ``composer-init token {token}`` set a github token for downloading past the [github rate limit](https://developer.github.com/v3/rate_limit/)

`composer-init` Gets a lot of defaults from github repo & organization, so it is best to create an empty repo in github, clone it locally and run "composer-init use ..." there.

Creating Templates
------------------

A composer-init template must be published to Packagist.org, (therefore have a composer.json file) and have a prompts.json file to describe which of the available prompts will be used. The package should be published as "type": "composer-init-template". All the code for the template is present in the "root" directory.

example composer.json file:

``` json
{
    "name": "clippings/package-template",
    "description": "Package Template",
    "license": "MIT",
    "type": "composer-init-template",
    "authors": [
        {
            "name": "John Smith",
            "email": "john@example.com",
            "role": "Author"
        }
    ]
}
```

example prompts.json file:

``` json
[
    "package_name",
    "title",
    "description",
    "php_namespace",
    "author_name",
    "author_email",
    "copyright",
    "bugs"
]
```
This states that this package will use these prompts, gather their input and then fill in the placeholders inside all the files in the root folder.


Here's an example template:
https://github.com/clippings/package-template/

Prompts
-------

All prompts try to guess a reasonable default, but ask the user to confirm/correct its value.

#### author\_email

The email of the author, by default uses `git config user.email`. You can set it yourself with `git config user.email {my email}` or globally with `git config --global user.email {my email}`. As stated in [first time git setup](https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup) guide

Adds `{% author_email %}` template variable

#### author\_name

The name of the author, by default uses `git config user.name`. You can set it yourself with `git config user.name {my name}` or globally with `git config --global user.name {my name}`. As stated in [first time git setup](https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup) guide

Adds `{% author_name %}` template variable

#### bugs

The url for submitting new issues. By default gets the repo's gitub issues url. e.g. `https://github.com/clippings/composer-init/issues`

Adds `{% bugs %}` template variable

#### copyright

Tries to guess the copyright holder by going through

- github organization
- github user
- git user
- file owner

And exposes it as "{year}, {copyright\_entity}" - where year is the current year and copyright\_entity is the guessed value. You can also get to the `copyright_entity` value separately

Adds `{% copyright %}` template variable <br>
Adds `{% copyright_entity %}` template variable

#### description

The description of the github repo.

Adds `{% description %}` template variable

####  package\_name

The github package name e.g. `clippings/composer-init`

Adds `{% package_name %}` template variable

#### php\_namespace

Tires to guess the package name, using github's organization/username and repo name. So `clippings/composer-init` would be converted to `Clippings\ComposerInit`. It also tries to guess the name with initials, so in this case it would also give the option of `CL\ComposerInit`. These can be cycled with tab completion or auto-completed when entering

Adds `{% php_namespace %}` <br>
Adds `{% php_namespace_escaped %}` template variable where all "\\" characters are converted to "\\\\"

#### slack\_notification

Get a "secure slack notification token". basically asks for a value and returns "slack:\n    secure: {value}\n", so you can easily add slack to your .travis.yml notifications

#### title

The title of the github repo.

Adds `{% title %}` template variable

Credits
-------

Inspired by [grunt-init](https://github.com/gruntjs/grunt-init)

Copyright (c) 2014-2015, Clippings Ltd. Developed by [Ivan Kerin](https://github.com/ivank) as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.
