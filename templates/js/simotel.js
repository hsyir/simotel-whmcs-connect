const adminPanelUrl = window.panelWebUrl;
const addonUrl = window.addonUrl;
const channelName = window.channelName;
const popUpTime = window.PopUpTime;
const popUpTimeMiliSeconds = PopUpTime * 1000;
const phoneNumberRegx = window.phoneNumberRegx;
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

let pusher = new Pusher(app_key, pusherOptions);
let channel = pusher.subscribe(channelName);
channel.bind("newCall", callerId);

function callerId(callData) {
    $.notify.addStyle('simotel-caller-id', {
        html:
            "<div class=''>"
            + "<div class='icon-holder'><img class='newcall-icon' src='" + addonUrl + "/templates/images/call.png' /></div>"
            + "<div class='body' data-notify-html='body'/>"
            + "<div class='clrfx'>"
            + "<a class='closeNotify' ><strong>-</strong></a>"
            + "</div>"
            + `<div class='countDownProgress' style='animation: progressBarCountDown linear ${popUpTime}s'><span></span></div>`

    });
    showNotif(callData);
}


$(document).on('click', '.notifyjs-simotel-caller-id-base .closeNotify', function () {
    $(this).trigger('notify-hide');
});

function showNotif(callData) {

    let html_notifBox;
    let html_tell;
    let html_clientName;

    let client = callData.client;

    if (client) {
        html_notifBox = $("<div>");

        //  client name ---------------------------------------------
        html_clientName = $("<a>").attr("href", `${adminPanelUrl}/clientssummary.php?userid=${client.id}`).html(`<strong> ${client.fullname}</strong>`);
        $(html_notifBox).append($("<div class='clientName'>").append(html_clientName))

        //  company name ---------------------------------------------
        let companyName = $("<a>").attr("href", `${adminPanelUrl}/clientssummary.php?userid=${client.id}`).html(` ${client.companyname}`);
        if (client.hasOwnProperty("companyname"))
            $(html_notifBox).append($("<div class='clientCompany'>").append(companyName))

        //  participant number ---------------------------------------------
        html_tell = $("<a>").attr("href", `tel:${callData.participant}`).html(`<strong> ${callData.participant}</strong>`);
        $(html_notifBox).append($("<div class='clientTell'>").append(html_tell))

        //  notes ---------------------------------------------
        const notesSummaryCharectersCount = 30;
        if (client.notes) {
            if (client.notes.length > notesSummaryCharectersCount) {
                let readMoreBtn = $("<span>").click(function () {
                    $(this).html(client.notes)
                }).html(client.notes.slice(0, notesSummaryCharectersCount)
                    + "<a class='readMoreDots'> <span class='bracket'>[</span>...<span class='bracket'>]</span> </a>")
                let clientNotes = $("<div>").html(`<strong>یادداشت: </strong>`).append(readMoreBtn);
                $(html_notifBox).append($("<div class='clientNotes'>").append(clientNotes))
            } else {
                let clientNotes = $("<div>").html(`<strong>یادداشت: </strong>${client.notes}`);
                $(html_notifBox).append($("<div class='clientNotes'>").append(clientNotes))
            }
        }


        //--------------------------------------------------
        $(html_notifBox).append($("<div class='clrfx'>"))


        let html_tickets = $("<a>").attr("href", `${adminPanelUrl}/client/${client.id}/tickets`).html(`<img src='${addonUrl}/templates/images/view-tickets.png' />`);
        let html_newTicket = $("<a>").attr("href", `${adminPanelUrl}/supporttickets.php?action=open&userid=${client.id}m`).html(`<img src='${addonUrl}/templates/images/new-ticket.png' />`);
        $(html_notifBox).append(
            $("<div class='simotelBtns'>")
                .append($("<div class='simotelBtn' title='مشاده تیکت ها'>").append(html_tickets))
                .append($("<div class='simotelBtn' title='تیکت جدید'>").append(html_newTicket))
        )

        $(html_notifBox).append($("<div class='clrfx'>"))

        // $(html_notifBox).append($("<div class='countDownProgress'>").append($("<span>")))


        let ab = $.notify({
            body: html_notifBox,
        }, {
            style: 'simotel-caller-id',
        });
        // setTimeout(x=>{$(ab.body[0]).parents(".notifyjs-wrapper").slideUp(5000);},1000)


    } else {
        html_notifBox = $("<div>");
        clientName = "بدون نام";
        html_tell = $("<a>").attr("href", "tel:" + callData.participant).html(callData.participant);
        $(html_notifBox).append($("<div>").append(clientName))
        $(html_notifBox).append($("<div>").append(html_tell))

        $.notify({
            body: html_notifBox,
        }, {
            style: 'simotel-caller-id',
        });
    }
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
    clickToHide: false,
    // whether to auto-hide the notification
    autoHide: true,
    // if autoHide, hide after milliseconds
    autoHideDelay: popUpTimeMiliSeconds,
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
    gap: 5
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
$('p ,td').each(function () {
    if ($(this).find("textarea,input,a").length > 0) return null;
    let newContent = $(this).html().replaceAll(phoneNumberRegx, makeBalloon)
    $(this).html(newContent);
});
$("input,textarea").each(function () {
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
    $.get(rootWebUrl + "/index.php?m=simotel&action=simotelCall&callee=" + $(this).data("number"))
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
        $(statusElement).removeClass("error");
        $(statusElement).removeClass("pending");
        $(statusElement).removeClass("success");
        $(statusElement).addClass(status);

    }

})

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

function addProfilesRow(name="", address="", user="", pass="") {
    let index = $("form.module-configs table tbody tr").length;
    let row = `
            <tr>
              <td>${index+1}</td>
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
    $("form.module-configs table").on("click"," a.delete-row",function(e){
        $(this).parents("tr").remove();


        $("form.module-configs table tbody").find("tr").each(function(index,val){
            $(this).find("td:first").html(index+1);
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

