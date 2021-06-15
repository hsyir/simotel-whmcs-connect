require("./notif")
require("./CallerId")
require("./pusher");
require("./clickToDIal");

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

