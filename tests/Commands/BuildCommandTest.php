<?php

use PHPUnit\Framework\TestCase;
use Overtrue\PackageBuilder\Application;
use Symfony\Component\Filesystem\Filesystem;
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

    public function tearDown()
    {
        parent::tearDown();

        $this->clearTestTempDir();
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
        $this->commandTester->setInputs(['foo']);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }

    public function testNormal()
    {
        $this->commandTester->setInputs([
            'overtrue/package-builder', // Name of package
            'Overtrue\\PackageBuilder\\', // Namespace of package
            'A composer package builder.', // description
            'overtrue', // author name
            'i@overtrue.me', // email
            null, // License of package  MIT
            'n', // Do you want to test this package ?
            'n', // Do you want to use php-cs-fixer format your code ?
        ]);
        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'directory' => TEST_TEMP_DIR
        ]);

        $this->assertTrue(true);
    }

    public function clearTestTempDir()
    {
        $fileSystem = new Filesystem();

        if ($fileSystem->exists(TEST_TEMP_DIR)) {
            $fileSystem->remove(TEST_TEMP_DIR);
        }
    }
}
