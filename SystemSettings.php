<?php
namespace Piwik\Plugins\PageViewCounter;

use Piwik\Settings\FieldConfig;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /**
     * @var SystemSetting 
     */
    public $enabledSiteIds;

    protected function init()
    {
        $this->enabledSiteIds =  $this->createEnabledSiteIdsSetting();
    }

    private function createEnabledSiteIdsSetting()
    {
        return $this->makeSetting(
            'enabledSiteIds', $default = '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = "Enabled site IDs";
                $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
                $field->description = "Comma-separated list of site IDs for which page visit data should be public.";    
                $field->validate = function ($value, $setting) {

                    if ($value === '') {
                          return;
                    }

                    $parts = explode(',', $value);

                    foreach ($parts as $part) {
                        if (!preg_match('/^[0-9]*$/', $part)) {
                            throw new \Exception('enabledSiteIds contains non-numeric ID');
                        }
                    }
                };
            }
        );
    }
}
