<form action="" method="post">
    <div class="container-fluid mt-5">
    </div>

</form>

<form class="simotelConfigs" id="adminsForm"
      action="{adminUrl("/addonmodules.php?module=simotel&action=storeAdminsExtens")}"
      method="get">
    <fieldset>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">کاربران ادمین</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>نام</th>
                            <th>داخلی سیموتل</th>
                            <th>سرور</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$admins key=key item=admin}
                            <tr>
                                <td>{$admin['id']}</td>
                                <td>{$admin["firstname"]} {$admin["lastname"]}</td>
                                <td dir="ltr" width="0100"><input type="text" name="adminExten[{$admin["id"]}]" class="form-control"
                                           value="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten($admin["id"])}">
                                </td>
                                <td>
                                    <select name="profiles[{$admin["id"]}]" id="profiles{$admin["id"]}" class="form-control">
                                        <option value="" >- - -</option>
                                        {foreach from=$simotelServers key=key item=profile}
                                            <option value="{$profile->profile_name}"
                                                    {if WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminServerProfile($admin["id"]) eq $profile->profile_name}
                                            selected
                                                    {/if}>
                                                {$profile->profile_name}</option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                        {/foreach}

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <input type="submit" value="ذخیره" class="btn btn-success btn-sm " id="saveBtn">
                </div>
            </div>
        </div>
    </fieldset>
</form>
<script>

    $("form#adminsForm").submit(function (e) {
        e.preventDefault();
        $("#saveBtn").attr("disabled", "disabled")
        $.post($(this).attr("action"), $(this).serialize())
            .success(x => {
                if (x.success == true)
                    alert("ذخیره شد.")
            })
            .done(function () {
                $("#saveBtn").attr("disabled", false)
            })
    })
</script>
