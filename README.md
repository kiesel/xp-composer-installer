# Composer Plugin for XP Framework

This plugin enabled [Composer](http://getcomposer.org/) to install packages written with
the [XP Framework](http://xp-framework.net/).

## Status

This is work-in-progress, at an experimental (even pre-alpha) level.

## Usage

To use this plugin in a project, you need to have a `composer.json` file:

```json
{
  "name" : "kiesel/composer-example",
  "minimum-stability": "dev",

  "repositories" : [
    {
      "type": "vcs",
      "url": "https://github.com/kiesel/oauth"
    },
    {
      "type": "vcs",
      "url": "https://github.com/kiesel/xp-composer-installer"
    },
    {
      "type": "vcs",
      "url": "https://github.com/kiesel/core"
    }
  ],
  "require" : {
    "xp-forge/oauth": "dev-composer"
  }
}
```

Then, run `composer install`. What now effectively happens is:

1. `xp-forge/oauth` will be fetched, branch `composer` will be used.
2. That in return depends on `xp-framework/core` which will then be fetched.
3. Both depend on `xp-forge/xp-composer-installer` which is a Composer plugin,
   so that will be fetched, as well.
4. Once all dependencies have been resolved, each dependency's root directory
   is searched for `*.pth` files, and their contents be added to
   this project's `composer.pth`.

Notes:

* `xp-composer-plugin` is a Composer plugin responsible for handling `xp-library` types
  of dependencies.
* adding these repositories must be done in the root `composer.json` file
* adding these repositories is only necessary as long as there are no official
  packages on [packagist](http://packagist.org).
* Then, also the `minimum-stability: dev` line can probably be removed