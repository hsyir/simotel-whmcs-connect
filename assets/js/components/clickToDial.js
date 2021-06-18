//--- simotel click to dial --------------------------------------
// find numbers in page and attach the ClickToDIal Balloon
const phoneNumberRegx = window.phoneNumberRegx;
const adminPanelUrl = window.panelWebUrl;
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
