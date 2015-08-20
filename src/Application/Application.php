<?php

namespace Overtrue\PackageBuilder\Application;

use Symfony\Component\Console\Application as BasicApplication;

class Application extends BasicApplication
{
    public function __construct()
    {
        parent::__construct(func_get_args());

        $this->add(new BuildCommand());
    }
}