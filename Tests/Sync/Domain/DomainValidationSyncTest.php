<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain;



use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;

class DomainValidationSyncTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lead
     */
    private $lead;

    /**
     * @var ValidationSync
     */
    private $validationSync;

    protected function setUp()
   {
       parent::setUp();
       $this->lead = new Lead();
       $this->validationSync = new ValidationSync();
   }

    public function testValidationByLeadAnonym()
    {
        $this->expectException(SkipMappingException::class);
        $this->validationSync->validationByLead($this->lead);
    }


    public function testValidationByLeadEmptyEmail()
    {
        $this->expectException(SkipMappingException::class);
        $this->lead->setFirstname('identified');
        $this->validationSync->validationByLead($this->lead);
    }

    public function testValidationByLeadExcludeDomains()
    {
        $this->expectException(SkipMappingException::class);
        $this->lead->setEmail('identified@gmail.com');
        $this->validationSync->validationByLead($this->lead, ['gmail*']);
    }

    public function testValidationByLeadDomains()
    {
        $this->lead->setEmail('identified@gmail.com');
        $this->validationSync->validationByLead($this->lead);
    }

    public function testValidationByLeadDomain()
    {
        self::assertEquals('gmail.com', ValidationSync::domainExists('identified@gmail.com'));
    }


}
