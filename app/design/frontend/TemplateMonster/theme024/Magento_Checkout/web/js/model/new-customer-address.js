/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(['jquery',], function () {
    /**
     * @param {Object} addressData
     * Returns new address object
     */
    return function (addressData) {
        var identifier = Date.now();

        return {
            email: addressData.email,
            countryId: addressData['country_id'] || addressData.countryId || window.checkoutConfig.defaultCountryId,
            regionId: (addressData.region && addressData.region.region_id) ?
                addressData.region.region_id
                : window.checkoutConfig.defaultRegionId,
            regionCode: (addressData.region) ? addressData.region.region_code : null,
            region: (addressData.region) ? addressData.region.region : null,
            customerId: addressData.customer_id,
            street: addressData.street,
            company: addressData.company,
            telephone: addressData.telephone,
            fax: addressData.fax,
            postcode: addressData.postcode ? addressData.postcode : window.checkoutConfig.defaultPostcode,
            city: addressData.city,
            firstname: addressData.firstname,
            lastname: addressData.lastname,
            middlename: addressData.middlename,
            prefix: addressData.prefix,
            suffix: addressData.suffix,
            vatId: addressData.vat_id,
            saveInAddressBook: addressData.save_in_address_book,
            customAttributes: addressData.custom_attributes,
            isDefaultShipping: function () {
                return addressData.default_shipping;
            },
            isDefaultBilling: function () {
                return addressData.default_billing;
            },
            getType: function () {
                return 'new-customer-address';
            },
            getKey: function () {
                return this.getType();
            },
            getCacheKey: function () {
                return this.getType() + identifier;
            },
            isEditable: function () {
                return true;
            },
            canUseForBilling: function () {
                return true;
            },

            drawMap: function () {
                var marker = false;
                var map;

                //The center location of our map.
                var centerOfMap = new google.maps.LatLng(45.7541614,21.2324269);

                //Map options.
                var options = {
                    center: centerOfMap, //Set center.
                    zoom: 13 //The zoom value.
                };

                //Create the map object.
                map = new google.maps.Map(document.getElementById('map-checkout'), options);
                var lat =  $("input[name='lat']").val();
                var lon =  $("input[name='lon']").val();
                if(lat != '' && lon != '' ){
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(lat,lon),
                        map: map,
                        draggable: true //make it draggable
                    });
                }

                //Listen for any clicks on the map.
                google.maps.event.addListener(map, 'click', function(event) {
                    //Get the location that the user clicked.
                    var clickedLocation = event.latLng;
                    //If the marker hasn't been added.
                    if(marker === false){
                        //Create the marker.
                        marker = new google.maps.Marker({
                            position: clickedLocation,
                            map: map,
                            draggable: true //make it draggable
                        });
                        //Listen for drag events!
                        google.maps.event.addListener(marker, 'dragend', function(event){
                            markerLocation();
                        });
                    } else{
                        //Marker has already been added, so just change its location.
                        marker.setPosition(clickedLocation);
                    }
                    //Get the marker's location.
                    //Get location.
                    var currentLocation = marker.getPosition();
                    //Add lat and lng values to a field that we can save.
                    $("input[name='lat']").val(currentLocation.lat());  //latitude
                    $("input[name='lon']").val(currentLocation.lng());

                    var markerLoc = new google.maps.LatLng(currentLocation.lat(),currentLocation.lng());
                    var geocoder = new google.maps.Geocoder;
                    geocoder.geocode({'location': markerLoc}, function(results, status) {
                        if (status === 'OK') {
                            if (results[0]) {
                                var addressArray = (results[0].formatted_address).split(',', 2);
                                $("input[name='street[0]']").val(addressArray[0]);

                                var addressArray = (results[0].formatted_address).split(',');
                                var city = (addressArray[1].trim()).split(' ', 2);
                                if(SelectHasValue('city', city[0])){
                                    $("select[name='city']").val(city[0]);
                                    $("input[name='street[0]']").val(addressArray[0]);
                                } else {
                                    $("select[name='city']").val('');
                                    $("input[name='street[0]']").val('');
                                    $("input[name='street[1]']").val('');
                                    window.alert('Adresa nu se afla in zona de livrare');
                                }
                            } else {
                                window.alert('No results found');
                            }
                        } else {
                            window.alert('Geocoder failed due to: ' + status);
                        }
                        function SelectHasValue(select, value) {
                            var obj = $("select[name='city'] option");
                            var exist = false;
                            obj.each(function() {
                                if (this.value == value) {
                                    exist = true;
                                }
                            });
                            return exist;
                        }
                    });

                });
            }
        }
    }
});
