function initMap() {
    var myLatLng = {lat: 52.5200, lng: 13.4050};

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: myLatLng
    });

    for (var property in TWIG.eventData) {
        if (TWIG.eventData.hasOwnProperty(property)) {
            // do stuff

            if (typeof TWIG.eventData[property].name !== 'undefined' &&
                typeof TWIG.eventData[property].venue !== 'undefined') {

                var eventVenue = TWIG.eventData[property].venue;
                var eventName = TWIG.eventData[property].name;
                var eventTime = TWIG.eventData[property].time;

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

                marker.content = formattedInfoWindowContent(eventName, eventVenue, eventTime, eventDescription);

                var infoWindow = new google.maps.InfoWindow();

                google.maps.event.addListener(marker, 'click', function () {
                    infoWindow.setContent(this.content);
                    infoWindow.open(this.getMap(), this);
                });

                //console.log(TWIG.eventData[property].name);
            }
        }
    }
}

function formattedInfoWindowContent(eventName, eventVenue, eventTime, eventDescription)
{
    var options = {
        hour: "2-digit", minute: "2-digit"
    };

    var date = new Date(eventTime);
    var infoWindowContent = "<p><b>Event:</b> " + eventName + "</p>";

    infoWindowContent += "<p><b>Date:</b>" + date.toLocaleDateString("en-GB") +
        " <b>Time:</b>" + date.toLocaleTimeString("en-GB", options) + "</p>";

    infoWindowContent += "<p><b>Location:</b><br/>";

    if (typeof eventVenue.name != 'undefined') {
        infoWindowContent += eventVenue.name + "<br/>";
    }

    if (typeof eventVenue.address_1 != 'undefined') {
        infoWindowContent += eventVenue.address_1 + "<br/>";
    }

    if (typeof eventVenue.address_2 != 'undefined') {
        infoWindowContent += eventVenue.address_2 + "<br/>";
    }

    if (typeof eventVenue.address_3 != 'undefined') {
        infoWindowContent += eventVenue.address_3 + "<br/>";
    }

    if (typeof eventVenue.city != 'undefined') {
        infoWindowContent += eventVenue.city + "<br/>";
    }

    infoWindowContent += "</p>";

    if (eventDescription != "") {
        infoWindowContent += "<p><b>Description:</b><br/>" + eventDescription + "</p>";
    }

    return infoWindowContent;
}
