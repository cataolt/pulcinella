<?php

namespace TemplateMonster\LatLng\Model\Checkout;

class LayoutProcessorPlugin
{

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['lat'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'options' => [],
                'id' => 'lat'
            ],
            'dataScope' => 'shippingAddress.lat',
            'label' => 'Latitude',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 200,
            'id' => 'lat',
            'additionalClasses' => 'hide-checkout',
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['lon'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'options' => [],
                'id' => 'lon'
            ],
            'dataScope' => 'shippingAddress.lon',
            'label' => 'Longitude',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 200,
            'id' => 'lon',
            'additionalClasses' => 'hide-checkout',

        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['additionalClasses'] = 'hide-checkout';
        $regionOptions = array(
            array(
                'label' => 'Timișoara',
                'value' => 'Timișoara'
            ),
            array(
                'label' => 'Săcălaz',
                'value' => 'Săcălaz'
            ),
            array(
                'label' => 'Dumbrăvița',
                'value' => 'Dumbrăvița'
            ),
            array(
                'label' => 'Ghiroda',
                'value' => 'Ghiroda'
            ),
            array(
                'label' => 'Giroc',
                'value' => 'Giroc'
            ),
        );
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'drop-down',
            ],
            'dataScope' => 'shippingAddress.city',
            'label' => 'Localitate',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 115,
            'id' => 'city',
            'options' => $regionOptions

        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['additionalClasses'] = 'hide-checkout';

        return $jsLayout;
    }
}