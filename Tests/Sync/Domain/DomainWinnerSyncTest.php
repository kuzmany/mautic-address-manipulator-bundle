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



use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\CompanyLead;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;

class DomainWinnerSyncTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lead
     */
    private $lead;

    /**
     * @var WinnerSync
     */
    private $winnerSync;

    /**
     * @var Company
     */
    private $company;

    protected function setUp()
   {
       parent::setUp();
       $this->lead = new Lead();
       $this->lead->setEmail('test@gmail.com');
       $this->lead->setId(1);
       $this->company = new Company();
       $this->winnerSync = new WinnerSync();
   }

    public function testSyncBySingleCompany()
    {
        $leadCompany = new CompanyLead();
        $leadCompany->setLead($this->lead);
        $leadCompany->setCompany($this->company);
        $leadCompany->setPrimary(true);
        $leadCompanies = [$leadCompany];

        $this->winnerSync->getWinnerDomain($leadCompanies, $this->lead);
    }


    public function testSyncByMultipleCompanySkip()
    {
        $this->expectException(SkipMappingException::class);

        $leadCompany = new CompanyLead();
        $leadCompany->setLead($this->lead);
        $leadCompany->setCompany($this->company);
        $leadCompany->setPrimary(true);

        $leadCompany2 = clone $leadCompany;
        $lead2 = clone $this->lead;
        $lead2->setEmail('test2@hotmail.com');
        $lead2->setId(2);
        $leadCompany2->setLead($lead2);
        $leadCompany2->setCompany($this->company);

        $leadCompanies = [$leadCompany, $leadCompany2];

        $this->winnerSync->getWinnerDomain($leadCompanies, $this->lead);
    }

    public function testSyncByMultipleCompanyLooser()
    {
        $this->expectException(SkipMappingException::class);

        $leadCompany = new CompanyLead();
        $leadCompany->setLead($this->lead);
        $leadCompany->setCompany($this->company);
        $leadCompany->setPrimary(true);

        $leadCompany2 = clone $leadCompany;
        $lead2 = clone $this->lead;
        $lead2->setEmail('test2@hotmail.com');
        $lead2->setId(2);
        $leadCompany2->setLead($lead2);
        $leadCompany2->setCompany($this->company);

        $leadCompany3 = clone $leadCompany;
        $lead2 = clone $this->lead;
        $lead2->setEmail('test2@hotmail.com');
        $lead2->setId(3);
        $leadCompany3->setLead($lead2);
        $leadCompany3->setCompany($this->company);

        $leadCompanies = [$leadCompany, $leadCompany2, $leadCompany3];

        $this->winnerSync->getWinnerDomain($leadCompanies, $this->lead);
    }

    public function testSyncByMultipleCompanyWinner()
    {
        $leadCompany = new CompanyLead();
        $leadCompany->setLead($this->lead);
        $leadCompany->setCompany($this->company);
        $leadCompany->setPrimary(true);

        $leadCompany2 = clone $leadCompany;
        $lead2 = clone $this->lead;
        $lead2->setEmail('test2@gmail.com');
        $lead2->setId(2);
        $leadCompany2->setLead($lead2);
        $leadCompany2->setCompany($this->company);

        $leadCompany3 = clone $leadCompany;
        $lead2 = clone $this->lead;
        $lead2->setEmail('test2@hotmail.com');
        $lead2->setId(3);
        $leadCompany3->setLead($lead2);
        $leadCompany3->setCompany($this->company);

        $leadCompanies = [$leadCompany, $leadCompany2, $leadCompany3];

        $this->winnerSync->getWinnerDomain($leadCompanies, $this->lead);
    }




}
