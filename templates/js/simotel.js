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
        authEndpoint: auth_endpoint,
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


const fixedPopups = {
    add: function (callData) {
        let popups = this.getCookie();
        if (!popups) popups = [];
        if (this.numberExistsInHistory(callData.participant))
            return;
        popups.push(callData);
        setCookie("FixedPopups", JSON.stringify(popups), 5);
    },
    getCookie: function () {
        let popups = getCookie("FixedPopups");
        if (!popups) return null;
        return JSON.parse(popups);
    },
    numberExistsInHistory: function () {

    },
    getAll: function () {
        return this.getCookie();
    }
}
// load notify js script
var notifyJsScript = document.createElement('script');
notifyJsScript.src = rootWebUrl + "/modules/addons/simotel/templates/js/notify.min.js";
notifyJsScript.onload = function () {
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
    $.notify.defaults(notifyOptions)

    $(document).on('click', '.notifyjs-simotel-caller-id-base .closeNotify', function () {
        $(this).trigger('notify-hide');
    });

    $(document).ready(function () {
        let popups = fixedPopups.getAll();
        /*for(let i in popups){
            callerId(popups[i])
        }*/
    })

};
document.head.appendChild(notifyJsScript); //or something of th
var notifyOptions = {
    clickToHide: false,
    autoHide: false,
    position: 'bottom right',
    className: 'info',
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

//--- simotel click to dial --------------------------------------
// find numbers in page and attach the ClickToDIal Balloon
const phoneNumberRegx = window.phoneNumberRegx;
if (window.clickToDialActive) {
    $('p ,td').each(function () {
        if ($(this).find("textarea,input,a").length > 0) return null;
        let newContent = $(this).html().replaceAll(phoneNumberRegx, makeBalloon)
        $(this).html(newContent);
    });
    $("input[type=text],input[type=number],input[type=tel]").each(function () {
        let numbers = $(this).val().match(phoneNumberRegx);
        if (numbers != null) {
            for (let i in numbers) {
                let balloon = makeBalloon(numbers[i]);
                $(balloon).insertAfter($(this));
            }
        }
    })

    $('body').on("click", function (e) {
        if ($(e.target).parents(".simotelClickToDial").length > 0)
            return;
        $(".simotelClickToDial").removeClass("active");
    })
    $('body').on("click", ".simotelClickToDial .number", function () {
        $(".simotelClickToDial").removeClass("active");
        $(this).parent().addClass("active");
        resetElHeight($(this).parent().find(".balloon"));
    })


    $('body').on("click", ".simotelClickToDial .balloon a", function (e) {
        e.preventDefault();
        let balloon = $(this).parents(".balloon");
        $(balloon).find(".message").html("")
        setStatus("pending", "در حال ارسال تماس ...");
        resetElHeight($(balloon));
        //https://mysup.ir/panel/s4admin/addonmodules.php?module=simotel
        $.get(adminPanelUrl + "/addonmodules.php?module=simotel&action=simotelCall&callee=" + $(this).data("number"))
            .done(x => {
                if (x.success == true)
                    setStatus("success", "با موفقیت ارسال شد.");
                else {
                    setStatus("error", "خطا در ارسال تماس.");
                    $(balloon).find(".message").html(x.message)
                }
            })
            .fail(x => {
                setStatus("error", "خطا در ارسال تماس.");
            })
            .done(x => {
                resetElHeight($(balloon));
            });

        function setStatus(status, message) {
            let statusElement = $(balloon).find(".status");
            $(statusElement).html(message);
            $(statusElement).removeClass("error pending success");
            $(statusElement).addClass(status);
        }

    })

}

function resetElHeight(el) {
    let balloonHeight = -1 * $(el).height() - 30;
    $(el).css({top: balloonHeight + "px"})
}

function makeBalloon(number) {
    return `<span class="simotelClickToDial">
                <span class="balloon">
                <div>${number}</div>
                    <div style="text-align: center"> <a href="#"  data-number="${number}">ارسال تماس</a></div>
                    <div class="status"></div>
                    <span class="message"></span>                    
                </span> 
                <span class="number">${number}</span>
            </span>`;
}

//-----------------------------------------------------------------------------
//--------------------- module configuration ----------------------------------
//-----------------------------------------------------------------------------

function addProfilesRow(name = "", address = "", user = "", pass = "") {
    let index = $("form.module-configs table tbody tr").length;
    let row = `
            <tr>
              <td>${index + 1}</td>
                <td><input type="text" name="simotelServerProfile[${index}][profile_name]" value="${name}" class="form-control">
                </td>
                <td><input dir="ltr" type="text" name="simotelServerProfile[${index}][server_address]" value="${address}"
                           class="form-control"></td>
                <td><input dir="ltr" type="text" name="simotelServerProfile[${index}][api_user]" value="${user}"
                           class="form-control"></td>
                <td><input dir="ltr" type="password" name="simotelServerProfile[${index}][api_pass]" value="${pass}"
                           class="form-control"></td>
                <td><input dir="ltr" type="text" name="simotelServerProfile[${index}][context]" value="${pass}"
                           class="form-control"></td>
                <td><a href="#" class="btn btn-danger btn-sm delete-row">حذف</a></td>           
                           
            </tr>               
            `

    $("form.module-configs tbody").append(row)
}

$("document").ready(function () {

    $("form.module-configs #addProfile").click(function (e) {
        e.preventDefault();
        addProfilesRow();
    })
    $("form.module-configs table").on("click", " a.delete-row", function (e) {
        $(this).parents("tr").remove();


        $("form.module-configs table tbody").find("tr").each(function (index, val) {
            $(this).find("td:first").html(index + 1);
        });


    })
    $("form.module-configs").submit(function (e) {
        e.preventDefault();
        $("#saveModuleConfigsBtn").attr("disabled", "disabled")
        $.post($(this).attr("action"), $(this).serialize())
            .success(x => {
                if (x.success == true)
                    alert("ذخیره شد.")
            })
            .done(function () {
                $("#saveModuleConfigsBtn").attr("disabled", false)
            })
    })

})

// ------------------------------------------
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


// -----------------------------------------------------------------

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

            $(html).append($("<div class='clrfx'>"))
            $(html).append(html_footer)
            return html;

        } else {
            let clientName = "بدون نام";
            let html_tell;
            html_tell = $("<a>").attr("href", "tel:" + callData.participant).html(callData.participant);
            $(html).append($("<div>").append(clientName))
            $(html).append($("<div>").append(html_tell))
            return html;
        }
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
        console.log(popup)
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
