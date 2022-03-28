<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomDimensionsManager\Commands;

use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Synchronize command
 */
class Synchronize extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('customdimensionsmanager:synchronize');
        $this->setDescription('Synchronize custom dimensions configuration between sites.');
        $this->addArgument('idsite_source', InputArgument::REQUIRED, 'Site ID to use as the source for configuration.');
        $this->addargument('idsite_target', InputArgument::REQUIRED, "Target site ID. Use '*' synchronize all sites.");
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'For tests. Runs the command w/o actually '
            . 'changing anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $idSiteSource = $input->getArgument('idsite_source');
        $idSiteTarget = $input->getArgument('idsite_target');
        $write = !$input->getOption('dry-run');

        $manager = new \Piwik\Plugins\CustomDimensionsManager\CustomDimensionsManager();
        return $manager->synchronizeSettings($idSiteSource, $idSiteTarget, $write, $output) ? 0 : 1;
    }
}
