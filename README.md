<h1 align="center"> Package Builder </h1>

<p align="center"> :package: A composer package builder.</p>


# Installation


```shell
$ composer global require 'overtrue/package-builder' --prefer-source
```

# Usage

```shell
 $ package-builder help
```

## create a composer package:

```
package-builder build [target directory]
```
example:

```shell
$ package-builder build ./

# Please enter the name of the package (example: foo/bar): vendor/product
# Please enter the namespace of the package [Vendor\Product]:
# Do you want to test this package ?[Y/n]:
# Do you want to use php-cs-fixer format your code ? [Y/n]:
# Please enter the standard of php-cs-fixer [symfony] ?
# Package vendor/product created in: ./
```
The follow package will be created:

```
vendor-product
├── .editorconfig
├── .gitattributes
├── .gitignore
├── .php_cs
├── README.md
├── composer.json
├── phpunit.xml.dist
├── src
│   └── .gitkeep
└── tests
    └── .gitkeep
```

## Update Package Builder

```shell
$ package-builder update
```

# Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/package-builder/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/package-builder/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

# License

MIT
