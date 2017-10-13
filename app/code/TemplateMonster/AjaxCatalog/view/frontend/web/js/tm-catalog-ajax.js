/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

define([
    'jquery',
    'mage/translate',
    'Magento_Catalog/js/product/list/toolbar',
    'mage/loader'
],function($,$t){

    var isLoadTmAjaxCatalog = false;

    $.widget('tm.productListToolbarForm',$.mage.productListToolbarForm,{

        options: {
            paginationControl : ".pages a",
            layoutNavigation : "#layered-filter-block a",
            layerMap: {name:'layer'},
            paginationMap: {name:'showpagination'},
            activeFilters: {},
            catalogAjaxLoading: 'catalog-ajax-loading',
            showLoader: true
        },

        _create: function () {
            // Add ajax handlers for pagination and layer filter
            this._layerPaginationInit();
            this._super();
        },

        ////@overwrite function in Magento_Catalog/js/product/list/toolbar.js
        changeUrl: function (paramName, paramValue, defaultValue) {

            //Check if need activate ajax filter
            if(!this.options.activeFilters[paramName]) {
                this._super(paramName, paramValue, defaultValue);
                return;
            }

            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters;
            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[parameters[0]] = parameters[1] !== undefined
                    ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }
            paramData[paramName] = paramValue;
            if (paramValue == defaultValue) {
                delete paramData[paramName];
            }
            paramData = $.param(paramData);

            var locationURL = baseUrl + (paramData.length ? '?' + paramData : '');

            this._ajaxNavClient(locationURL);

        },

        //Init selectors for layered filters and pagination
        _layerPaginationInit : function() {

            //Layer filtes intercept
            var actionType = $(this.options.paginationControl).is("select") ? 'change' : 'click';

            $(this.options.paginationControl).on(actionType,
                {paramName:this.options.paginationMap.name},
                $.proxy(this._layerPagination,this)
            );

            //Pagination filtes intercept
            $(this.options.layoutNavigation).on(actionType,
                {paramName:this.options.layerMap.name},
                $.proxy(this._layerPagination,this)
            );
        },

        // Handler for layered filters and pagination
        _layerPagination : function(event){

            if(!this.options.activeFilters[event.data.paramName]) {
                return true;
            }
            event.preventDefault();

            //TODO: need fix for differend layered filter type.In next version!
            var locationURL = $(event.currentTarget).attr('href');
            this._ajaxNavClient(locationURL);
        },

        // Return and insert html by ajax request
        _ajaxNavClient: function(locationURL){

            if(isLoadTmAjaxCatalog)
            {
                return false;
            }

            isLoadTmAjaxCatalog = true;

            var encodeUrl = decodeURIComponent(locationURL);
            $.ajax(encodeUrl,{
                method: 'POST',
                showLoader: this.options.showLoader
            }).done(function(data){


                if (data.error){
                    if ((typeof data.message) == 'string') {
                        alert(data.message);
                    } else {
                        alert(data.message.join("\n"));
                    }
                    return false;
                }

                var contentHtml = data.content;
                if(contentHtml) {
                    $('.toolbar.toolbar-products').replaceWith(
                        $('<div />').html(contentHtml).find('.toolbar.toolbar-products').first()
                    );
                    $('.products.wrapper').replaceWith(
                        $('<div />').html(contentHtml).find('.products.wrapper')
                    );
                }

                var layerHtml = data.layer;
                if(layerHtml) {
                    $('#layered-filter-block').replaceWith(
                        $('<div />').html(layerHtml).find('#layered-filter-block')
                    );
                }

                var parser;
                parser = document.createElement('a');
                parser.href = locationURL;

                var pathName = parser.pathname;
                var search = parser.search;
                var pathSearch = pathName + search;
                var pathSearchEncode = decodeURIComponent(pathSearch);

                /*@overwrite function in SwatchRenderer.js*/
                $.parseParams = function (query) {
                    var re = /([^&=]+)=?([^&]*)/g,
                        decodeRE = /\+/g,  /*Regex for replacing addition symbol with a space*/
                        decode = function (str) {
                            return decodeURIComponent(str.replace(decodeRE, " "));
                        },
                        params = {}, e;

                    while (e = re.exec(query)) {
                        var k = decode(e[1]), v = decode(e[2]);
                        if (k.substring(k.length - 2) === '[]') {
                            k = k.substring(0, k.length - 2);
                            (params[k] || (params[k] = [])).push(v);
                        }
                        else params[k] = v;
                    }
                    return params;
                };

                var result = $.parseParams(parser.search.substring(1));
                /*@overwrite function in SwatchRenderer.js
                when SR Swatcher try change color after filter apply*/
                $.parseParams = function() {
                    return result;
                };

                if(pathSearchEncode) {
                    window.history.pushState({"path":pathSearchEncode}, "Ajax Search", pathSearchEncode);
                }

                /*REFRESH ALL WIDGETS*/
                $('body').trigger('contentUpdated');

            }).fail(function(){
                alert($t('Can not finish request.Try again.'));
            }).always(function(){
                isLoadTmAjaxCatalog = false;
            });

        }
    });

    return $.tm.catalogAjax;
});