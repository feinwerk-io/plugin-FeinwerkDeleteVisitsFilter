<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Piwik;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BatchSize;
use Piwik\Settings\FieldConfig;
use Piwik\Settings\Setting;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $backgroundTimeBudget;

    /** @var Setting */
    public $defaultBatchSize;

    protected function init()
    {
        // The heading in General settings: the plugin's name with spaces, the same in every language (house rule).
        $this->title = 'Feinwerk Delete Visits Filter';

        $this->backgroundTimeBudget = $this->makeSetting('backgroundTimeBudget', 300, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('FeinwerkDeleteVisitsFilter_SettingBackgroundBudget');
            $field->description = Piwik::translate('FeinwerkDeleteVisitsFilter_SettingBackgroundBudgetHelp');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->validate = function ($value) {
                if (!self::isWholeNumber($value)) {
                    throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_SettingBackgroundBudgetInvalid'));
                }
                $value = (int) $value;
                if ($value !== 0 && ($value < 60 || $value > 3300)) {
                    throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_SettingBackgroundBudgetInvalid'));
                }
            };
        });

        $this->defaultBatchSize = $this->makeSetting('defaultBatchSize', BatchSize::DEFAULT, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = Piwik::translate('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSize');
            $field->description = Piwik::translate('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeHelp');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->validate = function ($value) {
                if (!self::isWholeNumber($value)) {
                    throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeInvalid'));
                }
                $value = (int) $value;
                if ($value < BatchSize::MIN || $value > BatchSize::MAX) {
                    throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeInvalid'));
                }
            };
        });
    }

    /**
     * Matomo runs a custom validator instead of its built-in numeric check, so a cast to int would turn
     * "abc" or "" into 0 (for the budget: "background processing off"). Only plain digit strings or ints pass.
     */
    private static function isWholeNumber($value): bool
    {
        return (is_int($value) || is_string($value)) && ctype_digit(trim((string) $value));
    }
}
