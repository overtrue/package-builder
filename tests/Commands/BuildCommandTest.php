<?php

namespace Overtrue\Tests\Commands;

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

    public function testBuildPackageWithoutTestAndPhpCsConfig()
    {
        $this->commandTester->setInputs([
            'test/package-name', // Name of package
            'Test\\PackageName', // Namespace of package
            'Test description', // description
            'test', // author name
            'test@test.com', // email
            null, // License of package  MIT
            'n', // Do you want to test this package ?
            'n', // Do you want to use php-cs-fixer format your code ?
        ]);
        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'directory' => TEST_TEMP_DIR
        ]);

        $this->assertFileExists(TEST_TEMP_DIR . '/src/.gitkeep');
        $this->assertFileExists(TEST_TEMP_DIR . '/composer.json');
        $this->assertFileExists(TEST_TEMP_DIR . '/.editorconfig');
        $this->assertFileExists(TEST_TEMP_DIR . '/.gitattributes');
        $this->assertFileExists(TEST_TEMP_DIR . '/.gitignore');

        $this->assertContains('test\/package-name', file_get_contents(TEST_TEMP_DIR . '/composer.json'));
    }

    public function testBuildPackageWithTestAndPhpCsConfig()
    {
        $this->commandTester->setInputs([
            'test/package-name', // Name of package
            'Test\\PackageName', // Namespace of package
            'Test description', // description
            'test', // author name
            'test@test.com', // email
            null, // License of package  MIT
            'yes', // Do you want to test this package ?
            'yes', // Do you want to use php-cs-fixer format your code ?
            'symfony', // Standard name of php-cs-fixer
        ]);
        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            'directory' => TEST_TEMP_DIR
        ]);

        $this->assertFileExists(TEST_TEMP_DIR . '/src/.gitkeep');
        $this->assertFileExists(TEST_TEMP_DIR . '/tests/.gitkeep');
        $this->assertFileExists(TEST_TEMP_DIR . '/composer.json');
        $this->assertFileExists(TEST_TEMP_DIR . '/.editorconfig');
        $this->assertFileExists(TEST_TEMP_DIR . '/.gitattributes');
        $this->assertFileExists(TEST_TEMP_DIR . '/.gitignore');
        $this->assertFileExists(TEST_TEMP_DIR . '/phpunit.xml.dist');
        $this->assertFileExists(TEST_TEMP_DIR . '/.php_cs');
    }

    public function clearTestTempDir()
    {
        $fileSystem = new Filesystem();

        if ($fileSystem->exists(TEST_TEMP_DIR)) {
            $fileSystem->remove(TEST_TEMP_DIR);
        }
    }
}
