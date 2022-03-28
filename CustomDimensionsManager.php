<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomDimensionsManager;

use Piwik\Site;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Plugins\CustomDimensions\API as CustomDimensionsAPI;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Methods for handling custom dimensions configurations
 */
class CustomDimensionsManager extends \Piwik\Plugin
{
    /**
     * Synchronize custom dimensions settings from one site to another or all others.
     *
     * @param mixed $idSiteSource Source site ID
     * @param mixed $idSiteTarget Target site ID or '*' for all sites
     * @param bool  $write        Whether to execute any changes
     * @param OutputInterface     $output Output interface
     *
     * @return bool
     */
    public function synchronizeSettings($idSiteSource, $idSiteTarget, $write, $output)
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln(
                "<info>Synchronize custom dimensions: source idsite: $idSiteSource, target idsite: $idSiteTarget.</info>"
            );
            if (!$write) {
                $output->writeln('<info>Only report will be created.</info>');
            } else {
                $output->writeln('<info>Changes will be written.</info>');
            }
        }

        $sourceSite = Site::getSite($idSiteSource);
        if (!$sourceSite) {
            $output->writeln("<error>Site $idSiteSource not found</error>");
            return false;
        }
        $api = CustomDimensionsAPI::getInstance();
        $sourceDimensions = $api->getConfiguredCustomDimensions($idSiteSource);
        $checked = 0;
        $updated = 0;
        $added = 0;

        // Make sure dimensions are in id order so that adding new ones is done in
        // order:
        usort(
            $sourceDimensions,
            function ($a, $b) {
                return $a['idcustomdimension'] <=> $b['idcustomdimension'];
            }
        );

        $idSiteTargets = '*' === $idSiteTarget
            ? $idSiteTargets = SitesManagerAPI::getInstance()->getAllSitesId()
            : (array)$idSiteTarget;

        foreach ($idSiteTargets as $idSite) {
            if ($idSite == $idSiteSource) {
                continue;
            }
            ++$checked;
            // Update idsite for easy comparison:
            $sourceDimensions = array_map(
                function ($dimension) use ($idSite) {
                    $dimension['idsite'] = $idSite;
                    return $dimension;
                },
                $sourceDimensions
            );
            $dimensions = $api->getConfiguredCustomDimensions($idSite);
            if ($dimensions === $sourceDimensions) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln("<info>Site $idSite: No changes needed.</info>");
                }
                continue;
            }
            foreach ($sourceDimensions as $sourceDimension) {
                // Compare with any existing configuration:
                $dimensionExists = false;
                foreach ($dimensions as $dimension) {
                    if ($dimension === $sourceDimension) {
                        // We have a matching config, no changes needed
                        continue 2;
                    }
                    if ($dimension['idcustomdimension'] === $sourceDimension['idcustomdimension']) {
                        if ($dimension['scope'] !== $sourceDimension['scope']) {
                            $output->writeln("<error>Site $idSite: Dimension {$dimension['idcustomdimension']} scope mismatch -- unable to continue.</error>");
                            return false;
                        }
                        $dimensionExists = true;
                        break;
                    }
                }
                if ($dimensionExists) {
                    // Update the existing dimension:
                    ++$updated;
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                        $output->writeln("<info>Site $idSite: Updating {$sourceDimension['idcustomdimension']} - {$sourceDimension['name']}</info>");
                    }

                    if ($write) {
                        $api->configureExistingCustomDimension(
                            $sourceDimension['idcustomdimension'],
                            $idSite,
                            $sourceDimension['name'],
                            $sourceDimension['active'],
                            $sourceDimension['extractions'],
                            $sourceDimension['case_sensitive']
                        );
                    }
                } else {
                    // Add a new dimension:
                    ++$added;
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                        $output->writeln("<info>Site $idSite: Adding {$sourceDimension['idcustomdimension']} - {$sourceDimension['name']}</info>");
                    }

                    if ($write) {
                        $api->configureNewCustomDimension(
                            $idSite,
                            $sourceDimension['name'],
                            $sourceDimension['scope'],
                            $sourceDimension['active'],
                            $sourceDimension['extractions'],
                            $sourceDimension['case_sensitive']
                        );
                    }
                }
            }
        }
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln("<info>$checked sites checked. Added $added and updated $updated dimensions.</info>");
        }
        return true;
    }

    /**
     * Delete custom dimensions settings of a site.
     *
     * @param mixed           $idSite Site ID
     * @param OutputInterface $output Output interface
     *
     * @return bool
     */
    public function deleteSettings($idSite, $output)
    {
        $site = Site::getSite($idSite);
        if (!$site) {
            $output->writeln("<error>Site $idSite not found</error>");
            return false;
        }
        $plugin = \Piwik\Plugin\Manager::getInstance()->loadPlugin('CustomDimensions');
        $plugin->deleteCustomDimensionDefinitionsForSite($idSite);
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln(
                "<info>Custom dimensions configuration of site $idSite deleted.</info>"
            );
        }
    }
}
