function initMap() {
    var myLatLng = {lat: 52.5200, lng: 13.4050};

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: myLatLng
    });

    var content = "Loading...";

    var infoWindow = new google.maps.InfoWindow();
    infoWindow.setContent(content);

    for (var property in TWIG.eventData) {
        if (TWIG.eventData.hasOwnProperty(property)) {
            // do stuff

            if (typeof TWIG.eventData[property].name !== 'undefined' &&
                typeof TWIG.eventData[property].venue !== 'undefined') {

                var groupUrl = TWIG.eventData[property].group.urlname;
                var eventId = TWIG.eventData[property].id;

                var eventDescription;

                if (typeof TWIG.eventData[property].description !== 'undefined') {
                    eventDescription = TWIG.eventData[property].description;
                } else {
                    eventDescription = "";
                }

                myLatLng = {lat: TWIG.eventData[property].venue.lat, lng: TWIG.eventData[property].venue.lon };

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    title: TWIG.eventData[property].name
                });

                google.maps.event.addListener(marker, 'click', (function(marker, infoWindow, groupUrl, eventId) {
                    return function() {
                        if (infoWindow) {
                            infoWindow.close();
                        }
                        infoWindow.setContent(content);
                        infoWindow.open(map, this);
                        infoWindowAjax(groupUrl, eventId, function(data) {
                            infoWindow.setContent(data);
                        });
                    };
                })(marker, infoWindow, groupUrl, eventId));
            }
        }
    }
}

function infoWindowAjax(groupUrl, eventId, callback) {
    return $.ajax({
        url: '/event/' + groupUrl + '/' + eventId
    })
    .done(callback)
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
    });
}
