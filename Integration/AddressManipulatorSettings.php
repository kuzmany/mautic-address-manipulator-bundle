<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Integration;


use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;

class AddressManipulatorSettings
{
    private static $fields = ['address1', 'address2', 'city', 'state', 'zip', 'country'];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var bool|\Mautic\PluginBundle\Integration\AbstractIntegration
     */
    private $integration;

    /**
     * AddressManipulatorSettings constructor.
     *
     * @param IntegrationHelper    $integrationHelper
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integration = $integrationHelper->getIntegrationObject(AddressManipulatorIntegration::NAME);
        if ($this->integration instanceof AbstractIntegration && $this->integration->getIntegrationSettings()->getIsPublished()) {
            $this->settings = $this->integration->mergeConfigToFeatureSettings();
        }
    }

    /**
     * @return bool
     */
    public function hasAddressSync()
    {
        return (bool) ArrayHelper::getValue('address_sync', $this->settings);
    }

    /**
     * @return bool
     */
    public function hasDomainSync()
    {
        return (bool) ArrayHelper::getValue('domain_sync', $this->settings);
    }

    /**
     * @return bool
     */
    public function hasContactAddressSync()
    {
        return (bool) ArrayHelper::getValue('contact_address_sync', $this->settings);
    }

    /**
     * @return bool
     */
    public function hasCompanyAddressSync()
    {
        return (bool) ArrayHelper::getValue('company_address_sync', $this->settings);
    }

    /**
     * @return bool
     */
    public function enabledDebugMode()
    {
        return (bool) ArrayHelper::getValue('debug_mode', $this->settings);
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param string $object
     *
     * @return array
     */
    public function getMatchingFields($object)
    {
        $fieldsToMatch = [];
        foreach (self::getFields() as $field) {
            if (isset($this->settings[$object.'_'.$field])) {
                $fieldsToMatch[$field] = $this->settings[$object.'_'.$field];
            }
        }

        return $fieldsToMatch;
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return self::$fields;
    }

}

