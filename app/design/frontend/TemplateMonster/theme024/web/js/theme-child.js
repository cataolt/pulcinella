define([
    'jquery',
    'jquery/ui',
    'theme'
], function ($) {
    'use strict';

    $.widget('TemplateMonster.themeChild', $.TemplateMonster.theme, {

        options: {},

        hide_footer_col: function () {
            var idClick = '.footer-col h4';
            var idSlide = '.footer-col .footer-col-content';
            var idClass = 'id-active';
            $(idClick).on('click', function (e) {
                e.stopPropagation();
                var subUl = $(this).next(idSlide);
                if (subUl.is(':hidden')) {
                    subUl.slideDown();
                    $(this).addClass(idClass);
                }
                else {
                    subUl.slideUp();
                    $(this).removeClass(idClass);
                }
                $(idClick).not(this).next(idSlide).slideUp();
                $(idClick).not(this).removeClass(idClass);
                return false;
            });
            $(idSlide).on('click', function (e) {
                e.stopPropagation();
            });
        },

        _create: function() {
            this._super();
            this.hide_footer_col();
        }
    });

    return $.TemplateMonster.themeChild;

});