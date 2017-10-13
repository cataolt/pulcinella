/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            "theme": 'js/theme',
            "selectize":    "js/selectize",
            "googleMapOptions": "js/googleMapOptions"
        }
    },
    paths: {
        "carouselInit":     'js/carouselInit',
        "blockCollapse":    'js/sidebarCollapse',
        "animateNumber":    'Magento_Theme/js/jquery.animateNumber.min',
        "rdnavbar":         'Magento_Theme/js/jquery.rd-navbar',
        "owlcarousel":      'Magento_Theme/js/owl.carousel.min',
        "customSelect":     "Magento_Theme/js/select2",
        "doubleTap":        "Magento_Theme/js/doubletaptogo",
        "googleMapOptions": "js/googleMapOptions"
    },
    shim: {
        "rdnavbar":         ["jquery"],
        "owlcarousel":      ["jquery"],
        "animateNumber":    ["jquery"],
        "doubleTap":        ["jquery", "jquery/ui"]
    },
    deps: [
        "jquery",
        "jquery/jquery.mobile.custom",
        "mage/common",
        "mage/dataPost",
        "mage/bootstrap"
    ]
};