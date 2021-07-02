
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
            .done(x => {
                if (x.success == true)
                    alert("ذخیره شد.")
            })
            .always(function () {
                $("#saveModuleConfigsBtn").attr("disabled", false)
            })
    })

})
