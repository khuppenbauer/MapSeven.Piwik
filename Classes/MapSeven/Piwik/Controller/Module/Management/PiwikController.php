<?php
namespace MapSeven\Piwik\Controller\Module\Management;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "MapSeven.Piwik".        *
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Configuration\Source\YamlSource;
use Neos\Flow\Package\PackageManagerInterface;
use Neos\Utility\Arrays;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Neos\Controller\Module\AbstractModuleController;

/**
 * Piwik Site Management Module Controller
 *
 * @package MapSeven\Piwik\Controller\Module\Management
 */
class PiwikController extends AbstractModuleController
{

    /**
     * @Flow\Inject
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @Flow\Inject
     * @var YamlSource
     */
    protected $configurationSource;

    /**
     * @Flow\Inject
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * The settings parsed from Settings.yaml
     *
     * @var array
     */
    protected $settings;

    /**
     * @Flow\Inject
     * @var Browser
     */
    protected $browser;

    /**
     * @Flow\Inject
     * @var CurlEngine
     */
    protected $browserRequestEngine;

    /**
     * Inject settings
     *
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * A edit view for the global Piwik settings and
     * Management Module for Piwik Sites through Piwik API
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assignMultiple(array(
            'piwikSettings' => $this->settings,
            'piwikSites' => $piwikSites = $this->callAPI('SitesManager.getAllSites')
        ));
    }

    /**
     * Update global Piwik settings
     *
     * @param array $piwik
     * @return void
     */
    public function updateAction(array $piwik)
    {
        $settings = $this->configurationSource->load(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
        $settings = Arrays::setValueByPath($settings, 'MapSeven.Piwik', $piwik);
        $this->configurationSource->save(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $settings);
        $this->configurationManager->flushConfigurationCache();
        $this->redirect('index');
    }

    /**
     * Create a new Piwik Site form
     *
     * @param array $piwikSite Site to view
     * @return void
     */
    public function newSiteAction($piwikSite = null)
    {
        $this->view->assign('piwikSite', $piwikSite);
    }

    /**
     * Create a new Piwik Site through Piwik API
     *
     * @param array $piwikSite Site to view
     * @return void
     */
    public function createSiteAction(array $piwikSite)
    {
        $this->callAPI('SitesManager.addSite', $piwikSite);
        $this->redirect('index');
    }

    /**
     * A edit view for a Piwik Site and its settings
     *
     * @param array $piwikSite Site to view
     * @return void
     */
    public function editSiteAction(array $piwikSite)
    {
        $this->view->assign('piwikSite', $piwikSite);
    }

    /**
     * Update a Piwik Site through Piwik API
     *
     * @param array $piwikSite Site to view
     * @return void
     */
    public function updateSiteAction(array $piwikSite)
    {
        $this->callAPI('SitesManager.updateSite', $piwikSite);
        $this->redirect('index');
    }

    /**
     * Delete a Piwik Site through Piwik API
     *
     * @param integer $idsite Site to view
     * @return void
     */
    public function deleteSiteAction($idsite)
    {
        $this->callAPI('SitesManager.deleteSite', array('idSite' => $idsite));
        $this->redirect('index');
    }

    /**
     * Call Piwik API
     *
     * @param $method
     * @param array $params
     * @return array
     */
    protected function callAPI($method, $params = array())
    {
        if (!empty($this->settings['host']) && !empty($this->settings['token_auth'])) {
            $this->browser->setRequestEngine($this->browserRequestEngine);
            $param = '';
            foreach ($params as $key => $value) {
                if (!empty($value)) {
                    $param .= '&' . $key . '=' . rawurlencode($value);
                }
            }
            $allSitesUrl = $this->getAPIBaseUrl($method) . $param;
            $response = $this->browser->request($allSitesUrl);
            return json_decode($response->getContent(), true);
        }
    }

    /**
     * Gets Piwik API Base Url
     *
     * @param string $module
     * @return string
     */
    protected function getAPIBaseUrl($module)
    {
        $url = $this->settings['host'] . '/index.php?module=API&method=' . $module . '&format=JSON&token_auth=' . $this->settings['token_auth'];
        return $url;
    }
}
