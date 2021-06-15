const adminPanelUrl = window.panelWebUrl;
const addonUrl = window.addonUrl;

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
    channel.bind("CallerId", callData => {
        let callerIdConfig = {
            selectedPopUpButtons: window.selectedPopUpButtons,
            adminPanelUrl,
            addonUrl,
            popUpTime: window.popUpTime,
        }
        let callerIdd = new CallerId(callData, callerIdConfig);
        callerIdd.show();
    });
}
class CallerId {
    constructor(callData, config) {
        this.callData = callData;
        this.uniqueId = callData.unique_id;
        this.config = config;
    }

    createHtml() {
        let callData = this.callData;
        let client = this.callData.client;
        let html;
        html = $("<div>");
        if (client) {
            //  client name ---------------------------------------------
            let html_clientName;
            html_clientName = $("<a>").attr("href", `${this.config.adminPanelUrl}/clientssummary.php?userid=${client.id}`).html(`<strong> ${client.fullname}</strong>`);
            $(html).append($("<div class='clientName'>").append(html_clientName))

            //  company name ---------------------------------------------
            let companyName = $("<a>").attr("href", `${this.config.adminPanelUrl}/clientssummary.php?userid=${client.id}`).html(` ${client.companyname}`);
            if (client.hasOwnProperty("companyname"))
                $(html).append($("<div class='clientCompany'>").append(companyName))

            //  participant number ---------------------------------------------
            let html_tell;
            html_tell = $("<a>").attr("href", `tel:${callData.participant}`).html(`<strong> ${callData.participant}</strong>`);
            $(html).append($("<div class='clientTell'>").append(html_tell))

            //  notes ---------------------------------------------
            const notesSummaryCharactersCount = 30;
            if (client.notes) {
                if (client.notes.length > notesSummaryCharactersCount) {
                    let readMoreBtn = $("<span>").click(function () {
                        $(this).html(client.notes)
                    }).html(client.notes.slice(0, notesSummaryCharactersCount)
                        + "<a class='readMoreDots'> <span class='bracket'>[</span>...<span class='bracket'>]</span> </a>")
                    let clientNotes = $("<div>").html(`<strong>یادداشت: </strong>`).append(readMoreBtn);
                    $(html).append($("<div class='clientNotes'>").append(clientNotes))
                } else {
                    let clientNotes = $("<div>").html(`<strong>یادداشت: </strong>${client.notes}`);
                    $(html).append($("<div class='clientNotes'>").append(clientNotes))
                }
            }

            $(html).append($("<div class='clrfx'>"))

            //--------------------------------------------------
            let popUpButtons = this.getAllButtons(client);

            let html_footer = $("<div class='simotelBtns'>");
            this.config.selectedPopUpButtons.forEach(btn => {
                if (popUpButtons.hasOwnProperty(btn)) {
                    let btnToAppend = $("<a>").attr("href", popUpButtons[btn].url).html(popUpButtons[btn].caption)
                    $(html_footer).append(btnToAppend)
                }
            })

            $(html).append(html_footer)
            $(html).append($("<div class='clrfx'>"))
            return html;

        } else {
            let clientName = "بدون نام";
            let html_tell;
            html_tell = $("<a>").attr("href", "tel:" + callData.participant).html(callData.participant);
            let thisPopup = this;
            let copyBtn = $("<a>").attr("href", "#")
                .click(function (e) {
                    e.preventDefault();
                    thisPopup.CopyMe(callData.participant);
                })
            $(copyBtn).append($("<img>").attr("src", addonUrl + "/templates/images/copy.png").css({
                width: "15px",
                margin: "0 5px"
            }).attr("title", "کپی"));
            $(html_tell).append(copyBtn);


            $(html).append($("<div>").append(clientName))
            $(html).append($("<div>").append(html_tell))

            let adminPanelUrl = this.config.adminPanelUrl;
            let buttons = [
                {
                    url: `${adminPanelUrl}/clientsadd.php?phonenumber=${callData.participant}`,
                    caption: "ایجاد مشتری جدید"
                },
            ]

            let html_footer = $("<div class='simotelBtns'>");
            for (let btn in buttons) {
                let btnToAppend = $("<a>").attr("href", buttons[btn].url).html(buttons[btn].caption)
                $(html_footer).append(btnToAppend)
            }

            $(html).append($("<div class='clrfx'>"))
            $(html).append(html_footer)

            return html;
        }
    }

    CopyMe(TextToCopy) {
        var TempText = document.createElement("input");
        TempText.value = TextToCopy;
        document.body.appendChild(TempText);
        TempText.select();

        document.execCommand("copy");
        document.body.removeChild(TempText);

        alert("کپی شد: " + TempText.value);
    }

    getAllButtons(client) {
        let adminPanelUrl = this.config.adminPanelUrl;
        return {
            view_profile: {
                url: `${adminPanelUrl}/clientssummary.php?userid=${client.id}`,
                caption: "مشاهده پروفایل"
            },
            edit_profile: {
                url: `${adminPanelUrl}/clientsprofile.php?userid=${client.id}`,
                caption: "ویرایش پروفایل"
            },
            services: {
                url: `${adminPanelUrl}/clientsservices.php?userid=${client.id}`,
                caption: "سرویس ها"
            },
            domains: {
                url: `${adminPanelUrl}/clientsdomains.php?userid=${client.id}`,
                caption: "دامنه ها"
            },
            notes: {
                url: `${adminPanelUrl}/clientsnotes.php?userid=${client.id}`,
                caption: "یادداشت ها"
            },
            tickets: {
                url: `${adminPanelUrl}/client/${client.id}/tickets`,
                caption: "تیکت ها"
            },
            transactions: {
                url: `${adminPanelUrl}/clientstransactions.php?userid=${client.id}`,
                caption: "تراکنش ها"
            },
            factors: {
                url: `${adminPanelUrl}/clientsinvoices.php?userid=${client.id}`,
                caption: "فاکتور ها"
            },
            pre_factors: {
                url: `${adminPanelUrl}/clientsquotes.php?userid=${client.id}`,
                caption: "پیش فاکتور ها"
            },
            create_ticket: {
                url: `${adminPanelUrl}/supporttickets.php?action=open&userid=${client.id}`,
                caption: "ایجاد تیکت"
            },
            create_factor: {
                url: `${adminPanelUrl}/clientssummary.php?userid=${client.id}`,
                caption: "ایجاد فاکتور"
            },
            create_pre_factor: {
                url: `${adminPanelUrl}/quotes.php?action=manage&userid=${client.id}`,
                caption: "ایجاد پیش فاکتور"
            },
            view_bill: {
                url: `${adminPanelUrl}/reports.php?report=client_statement&userid=${client.id}`,
                caption: "صورت حساب"
            },
        };
    }

    show() {
        let html = this.createHtml();
        let popup = $.notify({
            body: html,
        }, {
            style: 'simotel-caller-id',
        });
        this.popUp = $(popup.body[0]).parents(".notifyjs-wrapper");
        this.turnOnHideTimer();
    }

    turnOnHideTimer() {
        let countDownCounter = (this.config.popUpTime * 1000) / 100;
        let popUpElement = this.popUp;
        let interval;
        let intervalTime = 100;
        let popupFixed = false;

        let intervalAction = () => {
            if (countDownCounter < 1) {
                popUpElement.find(".notifyjs-simotel-caller-id-base").css("background-color", "#fa9393")
                popUpElement.slideUp(1000, function () {
                    popUpElement.remove()
                });
                clearInterval(interval);
            }
            countDownCounter--;
        };

        interval = setInterval(intervalAction, intervalTime)

        $(popUpElement).on("click", function () {
            popupFixed = true;
            popUpElement.find(".notifyjs-simotel-caller-id-base").addClass("fixed")
            clearInterval(interval);
            $(popUpElement).find(".countDownProgress").slideUp(150);
        }).on("mouseover", function () {
            $(popUpElement).find(".countDownProgress").css("animation-play-state", "paused")
            clearInterval(interval);
        }).on("mouseleave", function () {
            $(popUpElement).find(".countDownProgress").css("animation-play-state", "running")
            interval = !popupFixed ? setInterval(intervalAction, intervalTime) : null;
        });
    }

}
