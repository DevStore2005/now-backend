var destination_latitude=0, destination_longitude=0, source_latitude=0, source_longitude=0;

var map, mapMarkers = [];
var source, destination;

var s_input, d_input;
var s_latitude, s_longitude;
var d_latitude, d_longitude;
var distance;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 2,
    });
}


function createRideInitialize() {

    s_input = document.getElementById('s_address');
    d_input = document.getElementById('d_address');

    s_latitude = document.getElementById('s_latitude');
    s_longitude = document.getElementById('s_longitude');

    d_latitude = document.getElementById('d_latitude');
    d_longitude = document.getElementById('d_longitude');
    
    distance = document.getElementById('distance');

    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 2,
    });

    var autocomplete_source = new google.maps.places.Autocomplete(s_input);
    autocomplete_source.bindTo('bounds', map);

    var autocomplete_destination = new google.maps.places.Autocomplete(d_input);
    autocomplete_destination.bindTo('bounds', map);

    var service = new google.maps.places.PlacesService(map);
    var des_service = new google.maps.places.PlacesService(map);

    var marker = new google.maps.Marker({
        map: map,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29),
        icon: '/assets/layout/images/common/marker-start.png'
    });

    var markerSecond = new google.maps.Marker({
        map: map,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29),
        icon: '/assets/layout/images/common/marker-end.png'
    });

    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});

    google.maps.event.addListener(map, 'click', updateMarker);
    google.maps.event.addListener(map, 'click', updateMarkerSecond);
    
    google.maps.event.addListener(marker, 'dragend', updateMarker);
    google.maps.event.addListener(markerSecond, 'dragend', updateMarkerSecond);

    autocomplete_source.addListener('place_changed', function(event) {
        marker.setVisible(false);
        var place = autocomplete_source.getPlace();

        var admin_service = $('select[name=admin_service] option:selected').attr('data-service');
        var provider_service = $('select[name=provider_service]').val();

        

        if (place.hasOwnProperty('place_id')) {
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            if(admin_service == "SERVICE" && provider_service != "") {
                getProviders(place.geometry.location.lat(), place.geometry.location.lng(), provider_service);
            }
            updateSource(place.geometry.location);
        } else {
            service.textSearch({
                query: place.name
            }, function(results, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                    console.log('Autocomplete Has No Property');
                    if(admin_service == "SERVICE" && provider_service != "") {
                        getProviders(results[0].geometry.location.lat(), results[0].geometry.location.lng(), provider_service);
                    }
                    updateSource( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
                    s_input.value = results[0].formatted_address;
                }
            });
        }
    });

    autocomplete_destination.addListener('place_changed', function(event) {
        markerSecond.setVisible(false);
        var place = autocomplete_destination.getPlace();

        if (place.hasOwnProperty('place_id')) {
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            updateDestination(place.geometry.location);
        } else {
            des_service.textSearch({
                query: place.name
            }, function(results, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                    updateDestination(results[0].geometry.location);

                    console.log('destination', results[0]);
                    d_input.value = results[0].formatted_address;
                }
            });
        }
    });

    function getProviders(lat, long, id) {
        $.ajax({
            url: getBaseUrl() + "/admin/list?lat="+lat+"&long="+long+"&id="+id,
            type: "GET",
            headers: {
                Authorization: "Bearer " + getToken("admin")
            },
            success:function(data){
                var html = ``;
                var currency = $('#getCurrency').attr('data-value')
                html += `<ul id="provider_list" class="provider-list invoice height50vh">`;
                
                    if( Object.keys(data.responseData).length > 0  && data.responseData.provider_service.length > 0) {
                        
                        var result = data.responseData.provider_service;
                        for(var i in result) {
                            html += `<li class="row set_provider" data-id='`+result[i].id+`' data-name='`+result[i].first_name+`' data-picture='`+result[i].picture+`' data-base_fare='`+result[i].base_fare+`'>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                                  <img src="`+result[i].picture+`" class="user-img">
                                  <div class="user-right">
                                     <h5 class="d-inline">`+result[i].first_name+`</h5>
                                     <a class="float-right c-pointer txt-primary review" data-id="1"> Base fare: `+currency+result[i].base_fare+`</a>
                                     <div class="rating-outer">
                                        <span style="cursor: default;">
                                           <div class="rating-symbol" style="display: inline-block; position: relative;">
                                              <div class="fa fa-star-o" style="visibility: visible;"></div>
                                              <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                           </div>
                                           <div class="rating-symbol" style="display: inline-block; position: relative;">
                                              <div class="fa fa-star-o" style="visibility: visible;"></div>
                                              <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                           </div>
                                           <div class="rating-symbol" style="display: inline-block; position: relative;">
                                              <div class="fa fa-star-o" style="visibility: visible;"></div>
                                              <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                           </div>
                                           <div class="rating-symbol" style="display: inline-block; position: relative;">
                                              <div class="fa fa-star-o" style="visibility: visible;"></div>
                                              <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                           </div>
                                           <div class="rating-symbol" style="display: inline-block; position: relative;">
                                              <div class="fa fa-star-o" style="visibility: visible;"></div>
                                              <div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px;top: 0; width: auto;"><span class="fa fa-star" aria-hidden="true"></span></div>
                                           </div>
                                        </span>
                                        <input type="hidden" class="rating" value="1" disabled="disabled">
                                     </div>
                                  </div>
                               </div>
                            </div>
                         </li>`;
                        }

                        html += `</ul>`;
                        $('#providerModal .modal-body').html(html);
                        $('#providerModal').modal('show');
                    } else {
                        alertMessage("Error", "No providers available", "danger");
                    }
                
            } 
        });
    }

    function updateSource(location) {
        map.panTo(location);
        marker.setPosition(location);
        marker.setVisible(true);
        map.setZoom(15);
        updateSourceForm(location.lat(), location.lng());
        if(destination != undefined) {
            updateRoute();
            updateEstimated();
        }
    }

    function updateDestination(location) {
        map.panTo(location);
        markerSecond.setPosition(location);
        markerSecond.setVisible(true);
        updateDestinationForm(location.lat(), location.lng());
        updateRoute();
        updateEstimated();
    }

    function updateRoute() {
        directionsDisplay.setMap(null);
        directionsDisplay.setMap(map);

        directionsService.route({
            origin: source,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
            // unitSystem: google.maps.UnitSystem.IMPERIAL,
        }, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);

                marker.setPosition(result.routes[0].legs[0].start_location);
                markerSecond.setPosition(result.routes[0].legs[0].end_location);

                distance.value = result.routes[0].legs[0].distance.value / 1000;
            }
        });

    }

    $(document).on('change', '#admin_service', function() {
        $('.selected_provider').html('')
    })



    $('body').on('click', '.set_provider', function() {
        $('#estimated').text( $(this).data('base_fare') );
        $('.selected_provider').html(`<div><label for="schedule_time" style="float: left; width: 100%;">Selected Provider</label> <p><img style="padding:10px 10px 10px 0;" width="80px" src="`+$(this).data('picture')+`" />`+ $(this).data('name') +`</p> <input name="provider_id" type="hidden" value="`+ $(this).data('id') +`" /></div>`);   
        $('#providerModal').modal('hide');
    });

    $("#provider_service").on('change', function(){
        if($(this).val() != "") updateEstimated();
    });

    function updateEstimated(){
        $.ajax({
           type: "GET",
           url: getBaseUrl() + "/admin/fare",
           processData: false,
           contentType: false,
           headers: {
               Authorization: "Bearer " + getToken("admin")
           },
           data: $("#form-create-ride").serialize(),

           success: function(data)
           { 
               $("#estimated").text(data.estimated_fare);
               $("#showbtn").attr('disabled', false);
           }
        });
    }

    function updateSourceForm(lat, lng) {
        s_latitude.value = lat;
        s_longitude.value = lng;

        source = new google.maps.LatLng(lat, lng);
    }

    function updateDestinationForm(lat, lng) {
        d_latitude.value = lat;
        d_longitude.value = lng;
        destination = new google.maps.LatLng(lat, lng);
    }

    function updateMarker(event) {

        marker.setVisible(true);
        marker.setPosition(event.latLng);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': event.latLng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    s_input.value = results[0].formatted_address;
                    s_state.value = '';
                    s_country.value = '';
                    s_city.value = '';
                    s_pin.value = '';
                } else {
                    alert('No Address Found');
                }
            } else {
                alert('Geocoder failed due to: ' + status);
            }
        });

        updateSource(event.latLng);
    }

    function updateMarkerSecond(event) {

        markerSecond.setVisible(true);
        markerSecond.setPosition(event.latLng);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': event.latLng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    d_input.value = results[0].formatted_address;
                    d_state.value = '';
                    d_country.value = '';
                    d_city.value = '';
                    d_pin.value = '';
                } else {
                    alert('No Address Found');
                }
            } else {
                alert('Geocoder failed due to: ' + status);
            }
        });

        updateDestination(event.latLng);
    }
}

function ongoingInitialize(request) {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 2,
    });

    var bounds = new google.maps.LatLngBounds();

    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29),
        icon: '/assets/layout/images/common/marker-start.png'
    });

    var markerSecond = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29),
        icon: '/assets/layout/images/common/marker-end.png'
    });

    source = new google.maps.LatLng(request.request.s_latitude, request.request.s_longitude);
    destination = new google.maps.LatLng(request.request.d_latitude, request.request.d_longitude);

    marker.setPosition(source);
    markerSecond.setPosition(destination);

    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true, preserveViewport: true});
    directionsDisplay.setMap(map);

    directionsService.route({
        origin: source,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);

            marker.setPosition(result.routes[0].legs[0].start_location);
            markerSecond.setPosition(result.routes[0].legs[0].end_location);
        }
    });

    if(request.provider) {
        var markerProvider = new google.maps.Marker({
            map: map,
            icon: "/assets/layout/images/common/marker-car.png",
            anchorPoint: new google.maps.Point(0, -29)
        });

        provider = new google.maps.LatLng(request.provider.latitude, request.provider.longitude);
        markerProvider.setVisible(true);
        markerProvider.setPosition(provider);
        
        bounds.extend(markerProvider.getPosition());
    }
    bounds.extend(marker.getPosition());
    bounds.extend(markerSecond.getPosition());
    map.fitBounds(bounds);
}

function assignProviderShow(providers, request) {
    

    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 2,
    });
  
    if(request.service.admin_service=="ORDER"){
        var s_lat=request.request.pickup.latitude;
        var s_long=request.request.pickup.longitude;
        var d_lat=request.request.delivery.latitude;
        var d_long=request.request.delivery.longitude;
    } else if(request.service.admin_service=="TRANSPORT"){
        var s_lat=request.request.s_latitude;
        var s_long=request.request.s_latitude;
        var d_lat=request.request.d_latitude;
        var d_long=request.request.d_longitude;
    }
    
    var bounds = new google.maps.LatLngBounds();
    bounds.extend({lat: parseFloat(s_lat), lng: parseFloat(s_long)});
    bounds.extend({lat: parseFloat(d_lat), lng: parseFloat(d_long)});

    providers.forEach(function(provider) {
        var marker = new google.maps.Marker({
            position: {lat: parseFloat(provider.latitude), lng: parseFloat(provider.longitude)},
            map: map,
            provider_id: provider.id,
            title: provider.first_name + " " + provider.last_name,
            icon: '/assets/layout/images/common/marker-car.png'
        });

        var content = "<p>Name : "+provider.first_name+" "+provider.last_name+"<br />"+
                "Rating : "+provider.rating+"<br />"+
                "Service Type : "+request.service.admin_service+"<br />"+
                "Service Name  : "+(provider.service.ride_vehicle ? provider.service.ride_vehicle.vehicle_name : provider.service.vehicle.vehicle_make)+"</p>"+
                "<a href='javascript:;' data-id='"+request.request.id+"' data-service='"+provider.service.admin_service+"' data-city='"+request.city_id+"' data-provider='"+provider.id+"' class='btn btn-success assign_provider'>Assign this Provider</a>";

        marker.infowindow = new google.maps.InfoWindow({
            content: content
        });

        marker.addListener('click', function(){ 
            marker.infowindow.open(map, marker);
        });

        bounds.extend(marker.getPosition());
        mapMarkers.push(marker);
        
    });

    map.fitBounds(bounds);

    $('body').on('click', '.assign_provider', function() {
        var id = $(this).data('id');
        var service = $(this).data('service');
        var provider = $(this).data('provider');
        var city = $(this).data('city');
        $.ajax({
            url: getBaseUrl() + "/admin/dispatcher/assign",
            type: "post",
            headers: {
                Authorization: "Bearer " + getToken("admin")
            },
            data: {
                id: id,
                service_id: service,
                admin_service: service,
                provider_id: provider,
            },
            success: (response, textStatus, jqXHR) => {
                
                location.reload();

            }, error: (jqXHR, textStatus, errorThrown) => {}
        });
    });
}

function assignProviderPopPicked(provider) {
    
    var index;
    for (var i = mapMarkers.length - 1; i >= 0; i--) {
        if(mapMarkers[i].provider_id == provider.id) {
            index = i;
        }
        mapMarkers[i].infowindow.close();
    }

    mapMarkers[index].infowindow.open(map, mapMarkers[index]);
}