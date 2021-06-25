export default class Html {

    constructor(config) {
        this.config = config;
    }

    CopyMe(TextToCopy) {
        let TempText = document.createElement("input");
        TempText.value = TextToCopy;
        document.body.appendChild(TempText);
        TempText.select();
        document.execCommand("copy");
        document.body.removeChild(TempText);
        return true;
    }

    getAllButtons(callData) {
        let client = callData.client;
        if (!client)
            return {
                create_client: {
                    url: this.adminUrl(`/clientsadd.php?phonenumber=${callData.participant}`),
                    caption: "ایجاد مشتری جدید"
                },
            };

        return {
            view_profile: {
                url: this.adminUrl(`/clientssummary.php?userid=${client.id}`),
                caption: "مشاهده پروفایل"
            },
            edit_profile: {
                url: this.adminUrl(`/clientsprofile.php?userid=${client.id}`),
                caption: "ویرایش پروفایل"
            },
            services: {
                url: this.adminUrl(`/clientsservices.php?userid=${client.id}`),
                caption: "سرویس ها"
            },
            domains: {
                url: this.adminUrl(`/clientsdomains.php?userid=${client.id}`),
                caption: "دامنه ها"
            },
            notes: {
                url: this.adminUrl(`/clientsnotes.php?userid=${client.id}`),
                caption: "یادداشت ها"
            },
            tickets: {
                url: this.adminUrl(`/client/${client.id}/tickets`),
                caption: "تیکت ها"
            },
            transactions: {
                url: this.adminUrl(`/clientstransactions.php?userid=${client.id}`),
                caption: "تراکنش ها"
            },
            factors: {
                url: this.adminUrl(`/clientsinvoices.php?userid=${client.id}`),
                caption: "فاکتور ها"
            },
            pre_factors: {
                url: this.adminUrl(`/clientsquotes.php?userid=${client.id}`),
                caption: "پیش فاکتور ها"
            },
            create_ticket: {
                url: this.adminUrl(`/supporttickets.php?action=open&userid=${client.id}`),
                caption: "ایجاد تیکت"
            },
            create_factor: {
                url: this.adminUrl(`/clientssummary.php?userid=${client.id}`),
                caption: "ایجاد فاکتور"
            },
            create_pre_factor: {
                url: this.adminUrl(`/quotes.php?action=manage&userid=${client.id}`),
                caption: "ایجاد پیش فاکتور"
            },
            view_bill: {
                url: this.adminUrl(`/reports.php?report=client_statement&userid=${client.id}`),
                caption: "صورت حساب"
            },
        };
    }

    adminUrl(url) {
        return this.config.adminPanelUrl + url;
    }

    addon(url) {
        return this.config.adminPanelUrl + url;
    }

    clientNotes(notes, minChCount = 30) {

        if (!notes)
            return "";

        let clientNotes;

        if (notes.length < minChCount) {
            clientNotes = $("<div>").html(`<strong>یادداشت: </strong>${notes}`);
            return $("<div class='clientNotes'>").append(clientNotes)
        }

        let readMoreBtn = $("<span>").click(function () {
            $(this).html(notes)
        }).html(notes.slice(0, minChCount)
            + "<a class='readMoreDots'> <span class='bracket'>[</span>...<span class='bracket'>]</span> </a>")
        clientNotes = $("<div>").html(`<strong>یادداشت: </strong>`).append(readMoreBtn);
        return $("<div class='clientNotes'>").append(clientNotes)

    }

    htmlClientName(client) {
        if (!client)
            return $("<div class='clientName'>").append("بدون نام");

        let htmlClientName;
        htmlClientName = $("<a>").attr("href", this.adminUrl(`/clientssummary.php?userid=${client.id}`));
        htmlClientName.html($("<strong>").append(client.fullname));
        return $("<div class='clientName'>").append(htmlClientName);
    }

    companyName(client) {
        if (!client) return "";
        let clientSummaryUrl = this.adminUrl(`/clientssummary.php?userid=${client.id}`);
        let companyName = $("<a>").attr("href", clientSummaryUrl);
        companyName.html(` ${client.companyname}`)
        if (client.hasOwnProperty("companyname"))
            return $("<div class='clientCompany'>").append(companyName);
    }

    clientTell(callData) {

        let htmlTell;
        htmlTell = $("<a>").attr("href", "tel:" + callData.participant).html(callData.participant);
        let thisPopup = this;
        let copyBtn = $("<a class='copyBtn'>").attr("href", "#")
            .click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                thisPopup.CopyMe(callData.participant);
            })
        $(copyBtn).append($("<img>").attr("src", addonUrl + "/templates/images/copy.png").css({
            width: "15px",
            margin: "0 5px"
        }).attr("title", "کپی"));
        return $(htmlTell).append(copyBtn);
    }

    popupActions(callData) {
        let popUpButtons = this.getAllButtons(callData);

        let selectedBtns = callData.client ? this.config.selectedPopUpButtons : ["create_client"];

        let html = $("<div class='simotelBtns'>");
        selectedBtns.forEach(btn => {
            if (popUpButtons.hasOwnProperty(btn)) {
                let btnToAppend = $("<a>").attr("href", popUpButtons[btn].url).html(popUpButtons[btn].caption)
                $(html).append(btnToAppend)
            }
        })
        return html;
    }
}
