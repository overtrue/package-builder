> 🚨 此项目已经停止维护，建议使用以下两个模板来快捷创建项目：
>
> - [overtrue/php-package](https://github.com/overtrue/php-package)
> - [overtrue/laravel-package](https://github.com/laravel-package)

<h1 align="center"> Package Builder </h1>

<p align="center"> :package: A composer package builder.</p>

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

# Installation

```shell
composer global require 'overtrue/package-builder' --prefer-source
```

# Usage

```shell
 package-builder help
```

## Create a composer package

Make sure you have `~/.composer/vendor/bin/` in your path.

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
package-builder update
```

## :heart: Sponsor me

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

# Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/package-builder/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/package-builder/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

# License

MIT
