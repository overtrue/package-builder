<?php

use PHPUnit\Framework\TestCase;
use Overtrue\PackageBuilder\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class BuildCommandTest extends TestCase
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        parent::setUp();

        $this->application = new Application('Package Builder', '@package_version@');
        $this->command = $this->application->find('build');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testEmptyPackageName()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
        $this->commandTester->setInputs(['']);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }

    public function testInvalidPackageName()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
        $this->commandTester->setInputs(['vendor']);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }
}
