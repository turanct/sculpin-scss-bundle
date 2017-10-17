# Sculpin Scss Bundle

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[Sculpin](http://sculpin.io) bundle that integrates the [leafo/scssphp](https://github.com/leafo/scssphp/) SCSS processor.

Each `*.scss` or `*.sass` file is parsed to CSS.
When the parser generates any CSS output the file is renamed to `*.css` in place. Otherwise, the file will be ignored.

## Installation

* Add the following to your `sculpin.json` file:

```json
{
    "require": {
        "devworks/sculpin-scss-bundle": "~0.1"
    }
}
```

* Run `sculpin update`.
* Add the bundle to your sculpin kernel `app/SculpinKernel.php`:

```php
<?php
class SculpinKernel extends \Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel
{
    /**
     * {@inheritDoc}
     */
    protected function getAdditionalSculpinBundles()
    {
        return array(
            'DevWorks\Sculpin\Bundle\ScssBundle\SculpinScssBundle'
        );
    }
}
```

## Configuration

```yaml
# app/config/sculpin_kernel.yml
sculpin_scss:

    # The formatter to use
    formatter_class: 'Leafo\\ScssPhp\\Formatter\\Compressed'
    extensions: ["scss"]
    files: ["assets/css/style.scss"]
```

### formatter_class

This setting controls the formatter used for the css output. By default the compressed formatter is used. This can be changed to the fully qualified class name of one of the other formatters provided by `scssphp` to change the css output format.

### extensions/files

By default the `extensions` whitelist is used. If the `files` whitelist is set it takes precedence and all other SCSS files are not converted.

To ignore non matching SCSS files the sculping `ignore` configuration can be used:

```yaml
# app/config/sculpin_kernel.yml
sculpin:
    ignore: ["assets/css/_imports/"]
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
