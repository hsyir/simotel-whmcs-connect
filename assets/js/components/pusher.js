const adminPanelUrl = window.panelWebUrl;
const addonUrl = window.addonUrl;

import CallerId from "./CallerId/CallerId";
//pusher
if (window.callerIdPopUpActive) {
    var pusherOptions = {
        useTLS: true,
        cluster: app_cluster,
        wsHost: wsHost,
        wsPort: wsPort,
        wssPort: wsPort,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: authEndpoint,
    }
    let pusher = new Pusher(app_key, pusherOptions);
    let channel = pusher.subscribe(window.channelName);
    channel.bind("CallerId", showCallerId);
}

function showCallerId (callData) {
    let callerIdConfig = {
        selectedPopUpButtons: window.selectedPopUpButtons,
        adminPanelUrl,
        addonUrl,
        popUpTime: window.popUpTime,
    }
    let callerIdd = new CallerId(callData, callerIdConfig);
    callerIdd.show();
}
