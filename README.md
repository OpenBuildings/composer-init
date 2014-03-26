Composer Init
=============

[![Build Status](https://travis-ci.org/clippings/composer-init.png?branch=master)](https://travis-ci.org/clippings/composer-init)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/composer-init/badges/quality-score.png?s=a1404674f68c4894d651150caf4985aa59597515)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/composer-init/badges/coverage.png?s=3d5fb55c42c6887679915320658b543ed935e00a)](https://scrutinizer-ci.com/g/clippings/composer-init/)
[![Latest Stable Version](https://poser.pugx.org/clippings/composer-init/v/stable.png)](https://packagist.org/packages/clippings/composer-init)

Initialize a composer package based on a template package, inspired by [grunt-init](https://github.com/gruntjs/grunt-init)

Basic Usage
-----------

- create an new github repo
- clone repo locally
- find / create the appropriate template repo, using ``composer-init search``
- inside your freshly cloned repo do ``composer-init use {template-package}``
- answer the questions presented

Creating Templates
------------------

To be able to discover and use your own templates, you need to create a simple github repo, and add it to packegist.

- composer type has be "composer-init-template"
- all the template files (that will later be in the main repo) have to go to "root" folder
- has to have Template.php file  with ``Template`` class and ``getTemplateValues`` method.

Here's an example:
https://github.com/clippings/package-template/

``getTemplateValues`` must return a key => value array that will be used for placeholders inside your templates. Placeholders are in the format ``{%name%}``, similar to grunt-init.

For conveniece, composer-init has some default "prompt" classes that ask you to enter values for the placeholders, and get their default values from the github api.

Prompts
-------

__Title__ - get the title from github api, adds "title" and "title\_underline" placeholders. The underline is useful for the README.md file, below the title.

__PHPNamespace__ - guess the php namespace, using the repo name. 2 versions as autocompletes - short and long ones. Adds "php\_namespace" and "php\_namespace\_escaped". The escaped version can be used inside php strings as the backslash is escaped.

__Description__ - get the description from github api, adds "description" placeholder.

__Copyright__ - get the organization and owner of the repo and set one of them for copyright. adds 2 placeholders: "copyright" (year, org) and "copyright_entity (org)". If there is no organization, the owner is used.

__AuthorName__ - get the author name from git config, add "author_name" placeholder

__AuthorEmail__ - get the author email from git config, add "author_email" placeholder

Custom Dialogs
--------------

You can use your own dialogs inside ``getTemplateValues``:

```php
class Template {

    public static function getTemplateValues(OutputInterface $output, TemplateHelper $template)
    {
        $values = array();
        $dialog = $template->getHelperSet()->get('dialog');

        $values['name'] = $dialog->ask($output, 'What do you want as a name?');

        return $value;
    }
}
```

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.
