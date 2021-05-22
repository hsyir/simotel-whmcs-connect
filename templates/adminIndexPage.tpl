<form class="configs" action="{$configs["AdminWebUrl"] }/addonmodules.php?module=simotel&action=storeMyConfigs"
      method="post">

    <fieldset>
        <legend>تنظیمات کاربر ادمین</legend>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="checkbox">
                        <label><input type="checkbox" value="on" name="caller_id_pop_up"
                                      {if $adminOptions->callerIdPopUpActive eq true}checked{/if}>
                            پاپ آپ شماره تماس گیرنده
                        </label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" value="on" name="click_to_dial"
                                      {if $adminOptions->clickToDialActive eq true}checked{/if}>
                            کلیک تو دایل
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="simotel_profile">انتخاب سرور سیموتل</label>
                    <select name="simotel_profile" id="simotel_profile" class="form-control">
                        {foreach from=$simotelServers key=key item=profile}
                            <option value="{$profile->profile_name}"
                                    {if $adminOptions->simotelProfileName eq $profile->profile_name}
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
    </fieldset>

    <fieldset>
        <h4 class="h4">کلید های پاپ آپ کالر آی دی</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[view_profile]"
                               {if isset($popUpButtons->view_profile)}checked{/if}>
                        مشاهده پروفایل
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[edit_profile]"
                               {if isset($popUpButtons->edit_profile)}checked{/if}>
                        ویرایش پروفایل
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[services]"
                               {if isset($popUpButtons->services)}checked{/if}>
                        سرویس ها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[domains]"
                               {if isset($popUpButtons->domains)}checked{/if}>
                        دامنه ها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[notes]"
                               {if isset($popUpButtons->notes)}checked{/if}>
                        یادداشت ها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[tickets]"
                               {if isset($popUpButtons->tickets)}checked{/if}>
                        تیکت ها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[transactions]"
                               {if isset($popUpButtons->transactions)}checked{/if}>
                        تراکنش ها
                    </label>
                </div>
            </div>
            <div class="col-md-6">

                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[factors]"
                               {if isset($popUpButtons->factors)}checked{/if}>
                        فاکتورها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[pre_factors]"
                               {if isset($popUpButtons->pre_factors)}checked{/if}>
                        پیش فاکتورها
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[create_ticket]"
                               {if isset($popUpButtons->create_ticket)}checked{/if}>
                        تیکت جدید
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[create_factor]"
                               {if isset($popUpButtons->create_factor)}checked{/if}>
                        ایجاد فاکتور
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[create_pre_factor]"
                               {if isset($popUpButtons->create_pre_factor)}checked{/if}>
                        ایجاد پیش فاکتور
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="on" name="popup_buttons[view_bill]"
                               {if isset($popUpButtons->view_bill)}checked{/if}>
                        مشاهده صورتحساب
                    </label>
                </div>
            </div>
        </div>
    </fieldset>
    <div class="row">
        <div class="col-md-12 my-5">
            <input type="submit" value="ذخیره" class="btn btn-primary btn-sm" id="saveBtn">
        </div>
    </div>

</form>
<div class="m-5">&nbsp</div>
<form action="" id="popupTest">
    <fieldset class="mt-5">
        <legend>شبیه ساز کالر آی دی</legend>

        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <input  type="text" name="participant" id="participant" class="form-control" placeholder="شماره تماس مخاطب">
                    <span class="input-group-btn">
                 <button class="btn btn-warning" type="submit">تست!</button>
             </span>
                </div><!-- /input-group -->
            </div>
        </div>


    </fieldset>
</form>
{*
<form action="" id="clickToDialTest">
    <fieldset class="mt-5">
        <legend>شبیه ساز کلیک تو دایل</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <input  type="text" name="participant" id="participant" class="form-control" placeholder="شماره تماس مخاطب">
                    <span class="input-group-btn">
                 <button class="btn btn-warning" type="submit">ارسال تماس!</button>
             </span>
                </div><!-- /input-group -->
            </div>
        </div>


    </fieldset>
</form>*}

<script>
    $("document").ready(function () {
        $("form.configs").submit(function (e) {
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

        $("#popupTest").submit(function (e) {
            e.preventDefault();
            let participant = $("#participant").val();
            let exten = $("#exten").val();
            let url = "{$configs["WebRootUrl"] }/panel/index.php?m=simotel&state=Ringing&exten=" + exten + "&participant=" + participant
            $.get(url);
        })
    })

</script>
