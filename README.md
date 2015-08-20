# Package Builder

A composer package builder.


# Install

1. As a Phar (Recommended)

```shell
$ curl -LSs https://overtrue.me/package-builder/installer.php | php
# as a command.
mv package-builder.phar /usr/bin/package-builder
chmod +x /usr/bin/package-builder
```

2. As a Global Composer Install

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
```

## Update Package Builder

```shell
$ package-builder update
```

# Contributing

You can contribute in one of three ways:

1. File bug reports using the issue tracker.
2. Answer questions or fix bugs on the issue tracker.
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

# License

MIT