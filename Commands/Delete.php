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
 * Delete command
 */
class Delete extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('customdimensionsmanager:delete');
        $this->setDescription('Delete custom dimensions configuration of a site.');
        $this->addRequiredArgument('idsite', 'Site ID.');
    }

    protected function doExecute(): int
    {
        $idSite = $this->getInput()->getArgument('idsite');

        $manager = new \Piwik\Plugins\CustomDimensionsManager\CustomDimensionsManager();
        return $manager->deleteSettings($idSite, $this->getOutput()) ? static::SUCCESS : static::FAILURE;
    }
}
