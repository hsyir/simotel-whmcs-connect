//pusher init
var pusherOptions = {
    useTLS: true,
    cluster: app_cluster,
    wsHost: wsHost,
    wsPort: wsPort,
    wssPort: wsPort,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: auth_endpoint,
}

var pusher = new Pusher(app_key, pusherOptions);

var channel = pusher.subscribe(channelName);
channel.bind("newCall", (data) => {
    callerId(data);
});

function callerId(data) {

    $.notify.addStyle('simotel-caller-id', {
        html:
            "<div class=''>" +
            "<div class='body' data-notify-html='body'/>" +
            "</div>" +
            "</div>"
    });

    var client = data.client;
    var notifBox = $("<div>");
    var clientName = $("<a>").attr("href", panelWebUrl + "/clientssummary.php?userid=" + client.id).html(`<strong> ${client.fullname}</strong>`);
    var tell = $("<a>").attr("href", "tel:" + client.phonenumber).html(client.phonenumber);
    var tickets = $("<a>").attr("href", panelWebUrl + "/client/" + client.id + "/tickets").html("تیکت های فعال");
    var newTicket = $("<a>").attr("href", panelWebUrl + "/supporttickets.php?action=open&userid=" + client.id + "m").html("تیکت جدید");
    $(notifBox).append($("<div>").append(clientName))
    $(notifBox).append($("<div>").append(tell))
    $(notifBox).append($("<div>").append(tickets))
    $(notifBox).append($("<div>").append(newTicket))

    $.notify({
        body: notifBox,
        button: 'YES !'
    }, {
        style: 'simotel-caller-id',
    });


}

// load notify js script
var notifyScript = document.createElement('script');
notifyScript.src = rootWebUrl + "/modules/addons/simotel/templates/js/notify.min.js";
notifyScript.onload = function () {
    $.notify.defaults(notifyOptions)
};
document.head.appendChild(notifyScript); //or something of th

var notifyOptions = {
    // whether to hide the notification on click
    clickToHide: true,
    // whether to auto-hide the notification
    autoHide: true,
    // if autoHide, hide after milliseconds
    autoHideDelay: 50000,
    // show the arrow pointing at the element
    arrowShow: true,
    // arrow size in pixels
    arrowSize: 5,
    // position defines the notification position though uses the defaults below
    position: 'bottom right',
    // default positions
    elementPosition: 'bottom right',
    globalPosition: 'bottom right',
    // default style
    style: 'bootstrap',
    // default class (string or [string])
    className: 'info',
    // show animation
    showAnimation: 'slideDown',
    // show animation duration
    showDuration: 400,
    // hide animation
    hideAnimation: 'slideUp',
    // hide animation duration
    hideDuration: 200,
    // padding between element and notification
    gap: 2
}


// load css
var cssId = 'simotelCss';
if (!document.getElementById(cssId)) {
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.id = cssId;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = rootWebUrl + '/modules/addons/simotel/templates/css/simotel.css';
    link.media = 'all';
    head.appendChild(link);
}
