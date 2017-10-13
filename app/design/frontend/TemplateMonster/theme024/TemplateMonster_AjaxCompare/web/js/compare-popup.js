/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
define([
    'jquery',
    'showCompareProduct'
],function($,$t) {
    'use strict';

    $.widget('tm.comparePopup', $.tm.showCompareProduct, {

        _actionCompare : function(){
            $('body').on('click','.action.compare',function(e){
                e.preventDefault();

                var href = $(e.currentTarget).attr('href');

                $.ajaxSetup({showLoader: true});
                $.post(href,function(data){
                    var compareProductBox = $('#productComparePopup');
                    compareProductBox.html('');
                    compareProductBox.html(data.content);
                    compareProductBox.modal({
                        "dialogClass": "page-footer",
                        "responsive": true,
                        "responsiveClass": "compare-popup",
                        "innerScroll": true
                    }).modal('openModal');
                }).fail(function(){
                    alert($t('Can not finish request.Try again.'));
                });
                return false;
            });
        },
    
    });

    return $.tm.comparePopup;

});