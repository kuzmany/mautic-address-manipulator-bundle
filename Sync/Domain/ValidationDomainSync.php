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

class ValidationDomainSync
{
    /**
     * @param Lead  $lead
     *
     * @param array $excludeDomains
     *
     * @throws SkipMappingException
     */
    public function validationByLead(Lead $lead, array $excludeDomains = [])
    {
        if ($lead->isAnonymous()) {
            throw new SkipMappingException('Contact '.$lead->getId().' is anonymous');
        }

        if (empty($lead->getEmail())) {
            throw new SkipMappingException('Contact '.$lead->getId().' has not email address in profile.');
        }

        if (!$domain = self::domainExists($lead->getEmail())) {
            throw new SkipMappingException('Not find domain in email address '.$lead->getEmail().' ');
        }
        foreach ($excludeDomains as $excludeDomain) {
            if (fnmatch($excludeDomain, $domain)) {
                throw new SkipMappingException(sprintf("Domain %s exist on blacklist. Can't be processed", $domain));
            }
        }
    }

    /**
     * Checks if email address' domain has a DNS MX record. Returns the domain if found.
     *
     * @param string $email
     *
     * @return string|false
     */
    public static function domainExists($email)
    {
        if (!strstr($email, '@')) { //not a valid email adress
            return false;
        }

        list($user, $domain) = explode('@', $email);
        $arr                 = dns_get_record($domain, DNS_MX);

        if (empty($arr)) {
            return false;
        }
        if ($arr[0]['host'] === $domain) {
            return $domain;
        }

        return false;
    }
}
