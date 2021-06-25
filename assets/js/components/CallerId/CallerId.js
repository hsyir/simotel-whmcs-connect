import Html from "./Html"

export default class CallerId {
    constructor(callData, config) {
        this.callData = callData;
        this.config = config;
        this.htmlBuilder = new Html(config);
        this.setUpNotifyJs();
    }

    setUpNotifyJs() {
        let notifyOptions = {
            clickToHide: false,
            autoHide: false,
            position: 'bottom right',
            className: 'info',
        }
        $.notify.defaults(notifyOptions)
        $.notify.addStyle('simotel-caller-id', {
                html: `<div class=''>
                    <a class='closeNotify' ><strong>x</strong></a>
                    <div class='icon-holder'><img class='newcall-icon' src='${addonUrl}/templates/images/call-simotel-blue.png' /></div>
                    <div class='simotel-popup-body' data-notify-html='body'/></div>
                    <div class='clrfx'></div>
                    <div class='countDownProgress' style='animation: progressBarCountDown forwards linear ${popUpTime}s'><span></span></div>
                </div>`
            }
        );

        $(document).on('click', '.notifyjs-simotel-caller-id-base .closeNotify', function () {
            $(this).trigger('notify-hide');
        });
    }

    createHtml() {
        let callData = this.callData;
        let client = this.callData.client;
        let html;
        html = $("<div>");
        $(html).append(this.htmlBuilder.htmlClientName(client));
        $(html).append(this.htmlBuilder.companyName(client));
        $(html).append(this.htmlBuilder.clientTell(callData));
        $(html).append(this.htmlBuilder.clientNotes(client ? client.notes : null));
        $(html).append($("<div class='clrfx'>"))
        $(html).append(this.htmlBuilder.popupActions(callData))
        $(html).append($("<div class='clrfx'>"))
        return html;

    }

    show() {
        let html = this.createHtml();
        let popup = $.notify({
            body: html,
        }, {
            style: 'simotel-caller-id',
        });
        this.popUp = $(popup.body[0]).parents(".notifyjs-wrapper");
        this.turnOnHideTimer(this.popUp);
    }

    turnOnHideTimer(popUpElement) {

        $(popUpElement).find(".countDownProgress").on("animationend", x => {
            popUpElement.slideUp(400, function () {
                popUpElement.remove()
            })
        })

        $(popUpElement).on("click", function () {
            popUpElement.find(".notifyjs-simotel-caller-id-base").addClass("fixed")
            $(popUpElement).find(".countDownProgress").slideUp(150);
        }).on("mouseover", function () {
            $(popUpElement).find(".countDownProgress").css("animation-play-state", "paused")
        }).on("mouseleave", function () {
            $(popUpElement).find(".countDownProgress").css("animation-play-state", "running")
        });

    }

    adminUrl(url) {
        return this.config.adminPanelUrl + url;
    }

    addon(url) {
        return this.config.adminPanelUrl + url;
    }

}
