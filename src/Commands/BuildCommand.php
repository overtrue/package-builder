<?php

/*
 * This file is part of the overtrue/package-builder.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\PackageBuilder\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BuildCommand.
 *
 * @author overtrue <i@overtrue.me>
 */
class BuildCommand extends Command
{
    /**
     * @var string
     */
    protected $stubsDirectory;

    /**
     * @var string
     */
    protected $packageDirectory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var array
     */
    protected $info = [
        'NAME' => '',
        'EMAIL' => '',
        'PACKAGE_NAME' => '',
        'VENDOR' => '',
        'NAMESPACE' => '',
        'DESCRIPTION' => '',
        'PHPCS_STANDARD' => 'symfony',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build package')
            ->addArgument(
                'directory',
                InputArgument::OPTIONAL,
                'Directory name for composer-driven project'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fs = new Filesystem();
        $this->stubsDirectory = __DIR__.'/../stubs/';

        $helper = $this->getHelper('question');
        $git = $this->getGitGlobalConfig();

        $config = [
                    'name' => 'Package Name',
                    'namespace' => '',
                    'phpunit' => true,
                    'phpcs' => true,
                    'phpcs_standards' => 'symfony',
                  ];

        $question = new Question('Name of package (example: <fg=yellow>foo/bar</fg=yellow>): ');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The package name can not be empty');
            }

            if (!preg_match('/[a-z0-9\-_]+\/[a-z0-9\-_]+/', $value)) {
                throw new \Exception('The package name is invalid, format: vendor/product');
            }

            return $value;
        });
        $question->setMaxAttempts(5);

        // package name
        $this->info['PACKAGE_NAME'] = $helper->ask($input, $output, $question);
        $defaultNamespace = implode('\\', array_map([$this, 'studlyCase'], explode('/', $this->info['PACKAGE_NAME'])));

        // vendor/namespace
        $question = new Question("Namespace of package [<fg=yellow>{$defaultNamespace}</fg=yellow>]: ", $defaultNamespace);
        $this->info['NAMESPACE'] = $helper->ask($input, $output, $question);
        $this->info['VENDOR'] = strtolower(strstr($this->info['NAMESPACE'], '\\', true));
        $this->info['PACKAGE'] = substr($this->info['PACKAGE_NAME'], strlen($this->info['VENDOR']) + 1);

        // description
        $question = new Question('Description of package: ');
        $this->info['DESCRIPTION'] = $helper->ask($input, $output, $question);

        // name
        $question = new Question(sprintf('Author name of package [<fg=yellow>%s</fg=yellow>]: ', $git['user.name'] ?? $this->info['VENDOR']), $git['user.name'] ?? $this->info['VENDOR']);
        $this->info['NAME'] = $helper->ask($input, $output, $question);

        // email
        if (!empty($git['user.email'])) {
            $question = new Question(sprintf('Author email of package [<fg=yellow>%s</fg=yellow>]: ', $git['user.email']), $git['user.email']);
        } else {
            $question = new Question('Author email of package?');
        }
        $this->info['EMAIL'] = $helper->ask($input, $output, $question);

        // license
        $question = new Question('License of package [<fg=yellow>MIT</fg=yellow>]: ', 'MIT');
        $this->info['LICENSE'] = $helper->ask($input, $output, $question);

        // test?
        $question = new ConfirmationQuestion('Do you want to test this package ? [<fg=yellow>Y/n</fg=yellow>]: ', 'yes');
        $config['phpunit'] = $helper->ask($input, $output, $question);

        // phpcs?
        $question = new ConfirmationQuestion('Do you want to use php-cs-fixer format your code ? [<fg=yellow>Y/n</fg=yellow>]:', 'yes');
        $config['phpcs'] = $helper->ask($input, $output, $question);

        if ($config['phpcs']) {
            $question = new Question('Standard name of php-cs-fixer [<fg=yellow>symfony</fg=yellow>]: ', 'symfony');
            $this->info['PHPCS_STANDARD'] = ucfirst(strtolower($helper->ask($input, $output, $question)));
        }

        $directory = './'.$input->getArgument('directory');
        $this->packageDirectory = $directory;

        $this->createPackage($config);
        $this->initComposer($config);
        $this->setNamespace($config);

        $output->writeln(\sprintf('<info>Package %s created in: </info><comment>%s</comment>', $this->info['PACKAGE_NAME'], $directory));
    }

    /**
     * @return array
     */
    public function getGitGlobalConfig()
    {
        $config = [];
        try {
            $segments = preg_split("/\n[\r]?/", trim(shell_exec('git config --list --global')));
            foreach ($segments as $segment) {
                list($key, $value) = array_pad(explode('=', $segment), 2, null);
                $config[$key] = $value;
            }
        } catch (\Exception $e) {
            //
        }

        return $config;
    }

    /**
     * Create package directory and base files.
     *
     * @param array $config
     *
     * @return string
     */
    protected function createPackage(array $config)
    {
        $this->fs->mkdir($this->packageDirectory.'/src/', 0755);
        $this->fs->touch($this->packageDirectory.'/src/.gitkeep');
        $this->copyFile('gitattributes', '.gitattributes');
        $this->copyFile('gitignore', '.gitignore');
        $this->copyFile('editorconfig', '.editorconfig');

        $this->copyReadmeFile($config);

        if ($config['phpunit']) {
            $this->copyPHPUnitFile($config);
        }
        if ($config['phpcs']) {
            $this->createCSFixerConfiguration($config);
        }

        return $this->packageDirectory;
    }

    public function setNamespace(array $config)
    {
        $composerJson = $this->packageDirectory.'/composer.json';
        $composer = \json_decode(\file_get_contents($composerJson));

        $composer->autoload = [
            'psr-4' => [
                $this->info['NAMESPACE'].'\\' => 'src',
            ],
        ];

        \file_put_contents($composerJson, \json_encode($composer, \JSON_PRETTY_PRINT|\JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    protected function studlyCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function camelCase($string)
    {
        return lcfirst($this->studlyCase($string));
    }

    /**
     * Create README.md.
     */
    protected function copyReadmeFile()
    {
        $this->copyFile('README.md');
    }

    /**
     * Create PHPUnit files.
     */
    protected function copyPHPUnitFile()
    {
        $this->fs->dumpFile($this->packageDirectory.'/tests/.gitkeep', '');
        $this->copyFile('phpunit_config', 'phpunit.xml.dist');
    }

    /**
     * Create PHP-CS-fixer.
     */
    protected function createCSFixerConfiguration()
    {
        $this->copyFile('php_cs', '.php_cs');
    }

    /**
     * Init composer.
     *
     * @param array $config
     */
    protected function initComposer($config)
    {
        $author = sprintf('%s <%s>', $this->info['NAME'], $this->info['EMAIL']);

        exec(sprintf(
            'composer init --no-interaction --name "%s" --author "%s" --description "%s" --license %s --working-dir %s',
            $this->info['PACKAGE_NAME'],
            $author,
            $this->info['DESCRIPTION'] ?? 'Package description here.',
            $this->info['LICENSE'],
            $this->packageDirectory
        ));
    }

    /**
     * Copy file.
     *
     * @param string $file
     * @param string $filename
     *
     * @internal param string $directory
     */
    protected function copyFile($file, $filename = '')
    {
        $target = $this->packageDirectory.'/'.($filename ?: $file);
        $content = str_replace(array_keys($this->info), array_values($this->info), file_get_contents($this->stubsDirectory.$file));

        $this->fs->dumpFile($target, $content);
    }
}
