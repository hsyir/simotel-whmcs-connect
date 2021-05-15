<form class="configs" action="{$configs["AdminWebUrl"] }/addonmodules.php?module=simotel&action=storeMyConfigs"
      method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="simotel_profile">انتخاب سرور سیموتل</label>
                <select name="simotel_profile" id="simotel_profile" class="form-control">
                    {foreach from=$simotelServers key=key item=profile}
                        <option value="{$profile->profile_name}"
                                {if $selectedSimotelProfileName eq $profile->profile_name}
                                    selected
                                {/if}>
                            {$profile->profile_name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exten">شماره اکستن سیموتل </label>
                <input type="text" name="exten" id="exten"
                       value="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten()}" class="form-control">
            </div>
        </div>
    </div>
    <input type="submit" value="ذخیره" class="btn btn-primary" id="saveBtn">
    <script>

        $("document").ready(function () {
            $("form.configs").submit(function (e) {
                e.preventDefault();
                $("#saveBtn").attr("disabled","disabled")
                $.post($(this).attr("action"),$(this).serialize())
                    .success(x=> {
                        if(x.success == true)
                            alert("ذخیره شد.")
                    })
                    .done(function(){
                        $("#saveBtn").attr("disabled",false)
                    })
            })
        })

    </script>
</form>

