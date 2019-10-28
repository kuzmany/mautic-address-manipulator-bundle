<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Logger;


use MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorSettings;
use Monolog\Logger;

class AddressSyncLogger
{
    /**
     * @var AddressManipulatorSettings
     */
    private $addressManipulatorSettings;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * AddressSyncLogger constructor.
     *
     * @param AddressManipulatorSettings $addressManipulatorSettings
     * @param Logger                     $logger
     */
    public function __construct(AddressManipulatorSettings $addressManipulatorSettings, Logger $logger)
    {
        $this->addressManipulatorSettings = $addressManipulatorSettings;
        $this->logger = $logger;
    }

    /**
     * @param string $message
     */
    public function log($message)
    {
        if ('dev' === MAUTIC_ENV) {
            $this->logger->debug($message);
        }else if ($this->addressManipulatorSettings->enabledDebugMode()) {
            $this->logger->warning($message);
        }
    }
}
