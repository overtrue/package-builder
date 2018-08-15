<?php

/*
 * This file is part of the overtrue/package-builder.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\PackageBuilder;

use Overtrue\PackageBuilder\Commands\BuildCommand;
use Overtrue\PackageBuilder\Commands\UpdateCommand;
use Symfony\Component\Console\Application as BasicApplication;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 */
class Application extends BasicApplication
{
    /**
     * Application constructor.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct($name, $version)
    {
        parent::__construct($name, $version);

        $this->add(new BuildCommand());
    }
}
