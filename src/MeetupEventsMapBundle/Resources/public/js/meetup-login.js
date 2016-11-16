/**
 * Created by dave on 15/11/16.
 */

function meetupLoginWindow(url) {

    window.open(url, '_parent','location=no,menubar=no,scrollbars=no');

}

window.onload=function() {
    document.getElementById("meetup-login").addEventListener("click", function () {

        var url = "https://secure.meetup.com/oauth2/authorize"
        var clientId = TWIG.loginClientId;
        var responseType = "code";
        var redirectUri = TWIG.connectUrl;
        var urlString = url +
            "?client_id=" + clientId +
            "&response_type=" + responseType +
            "&redirect_uri=" + redirectUri;

        meetupLoginWindow(urlString);
    });
}