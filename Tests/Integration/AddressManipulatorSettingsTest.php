<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Test\Integration;


use Mautic\LeadBundle\Entity\Lead;
use Mautic\PluginBundle\Entity\Integration;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorIntegration;
use MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorSettings;
use MauticPlugin\MauticRandomSmtpBundle\Integration\RandomSmtpIntegration;

class AddressManipulatorSettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddressManipulatorSettings
     */
    private $addressManipulatorSettings;

    protected function setUp()
    {
        parent::setUp();
        $integrationMock = $this->createMock(Integration::class);
        $integrationMock->method('getIsPublished')->willReturn(true);

        $addressManipulatorIntegration = $this->createMock(AddressManipulatorIntegration::class);
        $addressManipulatorIntegration->method('getIntegrationSettings')->willReturn($integrationMock);

        $addressManipulatorIntegration->method('mergeConfigToFeatureSettings')->willReturn(
            ['domain_sync' => 1, 'address_sync' => 1, 'company_address_sync'=> 0]
        );

        $addressManipulatorSettings = $this->createMock(IntegrationHelper::class);
        $addressManipulatorSettings->method('getIntegrationObject')->willReturn($addressManipulatorIntegration);

        $this->addressManipulatorSettings = new AddressManipulatorSettings($addressManipulatorSettings);
    }

    public function testHasDomainSync()
    {
        self::assertTrue($this->addressManipulatorSettings->hasDomainSync());
    }

    public function testHasAddressSync()
    {
        self::assertTrue($this->addressManipulatorSettings->hasAddressSync());
    }

    public function testCompanyAddressSync()
    {
        self::assertFalse($this->addressManipulatorSettings->hasCompanyAddressSync());
    }
}
