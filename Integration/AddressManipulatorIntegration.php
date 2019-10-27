<?php

namespace MauticPlugin\MauticAddressManipulatorBundle\Integration;

use Mautic\CoreBundle\Form\Type\SortableListType;
use Mautic\LeadBundle\Form\Type\LeadFieldsType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\FormBuilder;

class AddressManipulatorIntegration extends AbstractIntegration
{
    CONST NAME = 'AddressManipulator';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return 'Address Manipulator';
    }

    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }

    public function getSupportedFeatures()
    {
        return [
        ];
    }

    /**
     * Get icon for Integration.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/MauticAddressManipulatorBundle/Assets/img/icon.png';
    }


    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'features') {
            $builder->add(
                'contact_address_sync',
                'yesno_button_group',
                [
                    'label' => 'mautic.integration.form.feature.address_sync_contact',
                    'attr'  => [
                    ],
                    'data'       => isset($data['contact_address_sync']) ? $data['contact_address_sync'] : false
                ]
            );

            $this->generateFieldsMatching($builder, '', false, 'contact_address_sync');

            $builder->add(
                'company_address_sync',
                'yesno_button_group',
                [
                    'label' => 'mautic.integration.form.feature.address_sync_company',
                    'attr'  => [
                    ],
                    'data'       => isset($data['company_address_sync']) ? $data['company_address_sync'] : false
                ]
            );

            $this->generateFieldsMatching($builder, 'company', true, 'company_address_sync');


            $builder->add(
                'domain_sync',
                'yesno_button_group',
                [
                    'label' => 'mautic.integration.form.feature.domain_sync',
                    'attr'  => [
                    ],
                    'data'       => isset($data['domain_sync']) ? $data['domain_sync'] : false,
                ]
            );

            $builder->add(
                'exclude_domains',
                SortableListType::class,
                [
                    'with_labels'            => false,
                    'label'            => 'mautic.integration.form.exclude_domains',
                    'option_notblank'  => false,
                    'attr'=> [
                        'data-show-on' => '{"integration_details_featureSettings_domain_sync_1":["checked"]}',
                    ],
                    'option_required' => false,

                ]
            );

            $builder->add(
                'domain_field',
                LeadFieldsType::class,
                [
                    'label'       => 'mautic.integration.form.domain_field',
                    'label_attr'  => ['class' => 'control-label'],
                    'attr'        => [
                        'class'   => 'form-control',
                        'data-show-on' => '{"integration_details_featureSettings_domain_sync_1":"checked"}',
                    ],
                    'with_company_fields' => true,
                    'required'    => false,
                    'empty_value' => '',
                    'multiple'    => false,
                ]
            );

        }
    }

    /**
     * @param FormBuilder $builder
     * @param string      $object
     * @param bool        $withCompanyFields
     * @param string      $parent
     */
    private function generateFieldsMatching(FormBuilder $builder, $object = '', $withCompanyFields = false, $parent = '')
    {
        $fields = AddressManipulatorSettings::getFields();
        foreach ($fields as $field) {
            $builder->add(
                $object.$field,
                LeadFieldsType::class,
                [
                    'label'       => 'mautic.integration.form.domain_field.'.$field.$object,
                    'label_attr'  => ['class' => 'control-label'],
                    'attr'        => [
                        'class'   => 'form-control',
                        'data-show-on' => '{"integration_details_featureSettings_'.$parent.'_1":"checked"}',
                    ],
                    'with_company_fields' => $withCompanyFields,
                    'required'    => false,
                    'empty_value' => '',
                    'multiple'    => false,
                ]
            );
        }
    }


}
