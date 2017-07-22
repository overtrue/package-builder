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

use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand.
 *
 * Updates package-builder.phar to the latest version.
 *
 * <pre>
 * ```
 * $ php package-builder.phar phar:update [-m|--major] [-p|--pre] [version]
 * ```
 * </pre>
 *
 * @author overtrue <i@overtrue.me>
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'http://overtrue.github.io/package-builder/manifest.json';

    /**
     * Initializes this command and sets the name, description, options and arguments.
     */
    protected function configure()
    {
        $this->setName('update')
            ->setDescription('Updates package-builder.phar to the latest version')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Updates to version-number (i.e. 2.6.0). When omitted package-builder will update to the latest version'
            )
            ->addOption('major', 'm', InputOption::VALUE_NONE, 'Lock to current major version')
            ->addOption('pre', 'p', InputOption::VALUE_NONE, 'Allow pre-release version update')
        ;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Looking for updates...');
        $manager = $this->createManager($output);
        $version = $input->getArgument('version')
            ? $input->getArgument('version')
            : $this->getApplication()->getVersion();
        $allowMajor = $input->getOption('major');
        $allowPreRelease = $input->getOption('pre');
        $this->updateCurrentVersion($manager, $version, $allowMajor, $allowPreRelease, $output);

        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }

    /**
     * Returns manager instance or exit with status code 1 on failure.
     *
     * @param OutputInterface $output
     *
     * @return \Herrera\Phar\Update\Manager
     */
    private function createManager(OutputInterface $output)
    {
        try {
            return new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        } catch (FileException $e) {
            $output->writeln('<error>Unable to search for updates.</error>');
            exit(1);
        }
    }

    /**
     * Updates current version.
     *
     * @param Manager         $manager
     * @param string          $version
     * @param bool|null       $allowMajor
     * @param bool|null       $allowPreRelease
     * @param OutputInterface $output
     */
    private function updateCurrentVersion(
        Manager $manager,
        $version,
        $allowMajor,
        $allowPreRelease,
        OutputInterface $output
    ) {
        if ($manager->update($version, $allowMajor, $allowPreRelease)) {
            $output->writeln('<info>Updated to latest version.</info>');
        } else {
            $output->writeln('<comment>Already up-to-date.</comment>');
        }
    }
}
