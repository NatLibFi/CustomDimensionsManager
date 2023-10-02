<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomDimensionsManager\Commands;

use Piwik\Plugin\ConsoleCommand;

/**
 * Synchronize command
 */
class Synchronize extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('customdimensionsmanager:synchronize');
        $this->setDescription('Synchronize custom dimensions configuration between sites.');
        $this->addRequiredArgument('idsite_source', 'Site ID to use as the source for configuration.');
        $this->addRequiredArgument('idsite_target', "Target site ID. Use '*' synchronize all sites.");
        $this->addNoValueOption('dry-run', null, 'For tests. Runs the command w/o actually changing anything.');
    }

    protected function doExecute(): int
    {
        $input = $this->getInput();
        $idSiteSource = $input->getArgument('idsite_source');
        $idSiteTarget = $input->getArgument('idsite_target');
        $write = !$input->getOption('dry-run');

        $manager = new \Piwik\Plugins\CustomDimensionsManager\CustomDimensionsManager();
        return $manager->synchronizeSettings($idSiteSource, $idSiteTarget, $write, $this->getOutput())
            ? static::SUCCESS : static::FAILURE;
    }
}
