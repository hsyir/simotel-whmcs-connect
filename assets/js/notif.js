let notif = require("./notify.min");
$.notify.addStyle('simotel-caller-id', {
    html:
        "<div class=''>"
        + "<div class='icon-holder'><img class='newcall-icon' src='" + addonUrl + "/templates/images/call-simotel-blue.png' /></div>"
        + "<div class='simotel-popup-body' data-notify-html='body'/>"
        + "<div class='clrfx'>"
        + "<a class='closeNotify' ><strong>x</strong></a>"
        + "</div>"
        + `<div class='countDownProgress' style='animation: progressBarCountDown forwards linear ${popUpTime}s'><span></span></div>`
});

$(document).on('click', '.notifyjs-simotel-caller-id-base .closeNotify', function () {
    $(this).trigger('notify-hide');
});
var notifyOptions = {
    clickToHide: false,
    autoHide: false,
    position: 'bottom right',
    className: 'info',
}
$.notify.defaults(notifyOptions)
