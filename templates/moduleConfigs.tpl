<form class="module-configs simotelConfigs"
      action="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminPanelUrl("/addonmodules.php?module=simotel&action=storeModuleConfigs")}"
      method="post">

    <fieldset>
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">پیکربندی تنظیمات اتصال به سیموتل</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان پروفایل</th>
                            <th>آدرس سرور</th>
                            <th>یوزر</th>
                            <th>رمز</th>
                            <th>کانتکست</th>
                        </tr>
                        </thead>
                        <tbody>
                        {assign var="index" value=1}

                        {foreach from=$simotelServers key=key item=profile}
                            <tr>
                                <td>{$index}</td>
                                <td width="20%"><input type="text" name="simotelServerProfile[{$index-1}][profile_name]"
                                                       value="{$profile->profile_name}" class="form-control">
                                </td>
                                <td width="30%"><input dir="ltr" type="text" name="simotelServerProfile[{$index-1}][server_address]"
                                                       value="{$profile->server_address}"
                                                       class="form-control"></td>
                                <td width="10%"><input dir="ltr" type="text" name="simotelServerProfile[{$index-1}][api_user]"
                                                       value="{$profile->api_user}"
                                                       class="form-control"></td>
                                <td width="10%"><input dir="ltr" type="password" name="simotelServerProfile[{$index-1}][api_pass]"
                                                       value="{$profile->api_pass}"
                                                       class="form-control"></td>
                                <td width="20%"><input dir="ltr" type="text" name="simotelServerProfile[{$index-1}][context]"
                                                       value="{$profile->context}"
                                                       class="form-control"></td>
                                <td><a href="#" class="btn btn-danger btn-sm delete-row w-100">حذف</a></td>
                            </tr>
                            {assign var="index" value=$index+1}
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5">
                            <td>
                            <td>
                                <a href="#" id="addProfile" class="btn btn-info">+ </a>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </fieldset>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-left">
                <input type="submit" value="ذخیره" class="btn btn-success" id="saveModuleConfigsBtn">
            </div>
        </div>

    </div>
</form>

