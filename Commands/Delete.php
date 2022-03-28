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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete command
 */
class Delete extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('customdimensionsmanager:delete');
        $this->setDescription('Delete custom dimensions configuration of a site.');
        $this->addArgument('idsite', InputArgument::REQUIRED, 'Site ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $idSite = $input->getArgument('idsite');

        $manager = new \Piwik\Plugins\CustomDimensionsManager\CustomDimensionsManager();
        return $manager->deleteSettings($idSite, $output) ? 0 : 1;
    }
}
