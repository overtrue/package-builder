<?php

namespace Overtrue\PackageBuilder;

use Symfony\Component\Console\Application as BasicApplication;
use Overtrue\PackageBuilder\Commands\BuildCommand;
use Overtrue\PackageBuilder\Commands\UpdateCommand;

class Application extends BasicApplication
{
    public function __construct($name, $version)
    {
        parent::__construct($name, $version);

        $this->add(new BuildCommand());
        $this->add(new UpdateCommand());
    }
}
