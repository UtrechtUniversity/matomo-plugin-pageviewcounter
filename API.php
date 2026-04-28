<?php
namespace Piwik\Plugins\PageViewCounter;

use Piwik\Access;
use Piwik\Plugin\API as BaseAPI;

class API extends BaseAPI
{
    /**
     * Example API method
     * 
     * @return string
     */
    public function getVisits(int $siteId, string $url)
    {
        $settings = new SystemSettings();
        $enabledSiteIds = $settings->enabledSiteIds->getValue();
        if (in_array($siteId, explode(',', $settings->enabledSiteIds->getValue()), false)) {
            return Access::getInstance()->doAsSuperUser(
                function () use ($siteId, $url) {
                    return \Piwik\API\Request::processRequest(
                        'VisitsSummary.getVisits', array(
                        'idSite' => $siteId,
                        'segment' => 'pageUrl==' . urlencode($url),
                        'period' => "range",
                        'date' => "2000-01-01,2100-01-01",
                        )
                    )->getFirstRow()->export()[0];
                }
            );    
        }
        else { return "Not allowed!";
        }
    }
}
