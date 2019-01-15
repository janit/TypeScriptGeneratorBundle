PHP Classes to TypeScript Interfaces Generator Bundle
======

A Symfony bundle that adds a command to extract [TypeScript interface](https://www.typescriptlang.org/docs/handbook/interfaces.html) from PHP classes. Based on [the example from Martin Vseticka](https://stackoverflow.com/questions/33176888/export-php-interface-to-typescript-interface-or-vice-versa?answertab=votes#tab-top) this bundle uses [the PHP-Parser library](https://github.com/nikic/PHP-Parser) and annotations. This is currently very basic feature wise, but it does work.

TypeScript is a superscript of JavaScript that adds strong typing and other features on top of JS. Automatically generated classes can be useful, for example when using a simple JSON API to communicate to a JavaScript client. This way you can get typing for your API responses in an easy way.

This is currently tightly coupled to the Symfony Framework, but could be extracted. Feel free to build on this or use as inspiration to build something completely different.

## Installation

As a Symfony bundle you'll need to start by add the package to your project with composer:

```
$ composer req macavity/typescript-generator-bundle
```

After this you'll need to activate the bundle in your `app/AppKernel.php` file:

```
new Macavity\TypeScriptGeneratorBundle\TypeScriptGeneratorBundle()
```

Once this is done you should have the added command in place in your console and you can run:

```
$ php bin/console typescript:generate-interfaces
```

This will yield an error because the command expects `fromDir` as a parameter on where to scan for PHP classes.

NOTE: These instructions are for Symfony Standard Edition (3.3), but the bundle should work with Symfony Flex as well.

## Usage

The command scans directories recursively for all `.php` files. It will only generate Type Definitions (interfaces) for files with appropriate annotations.

To generate interfaces, create a new class in `src/AppBundle/Entity/Person.php` and enter the following:

```php
<?php

namespace AppBundle\Entity;

/**
 * @TypeScriptMe
 */
class Person
{
    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var int
     */
    public $age;
}
```

Once this is in place you can run the command with the argument `src/`:

```
$ php bin/console typescript:generate-interfaces src/
```

This will generate the following file `typescript/Person.d.ts` with the following content:

```
interface IPerson {
  firstName: string,
  lastName: string,
  age: number
}

```

If you provide another argument (`toDir`) you can change the target directory to something else.
