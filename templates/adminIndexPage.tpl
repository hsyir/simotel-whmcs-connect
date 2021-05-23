<form class="simotelConfigs" id="adminUserConfigs"
      action="{$configs["AdminWebUrl"] }/addonmodules.php?module=simotel&action=storeMyConfigs"
      method="post">
    <fieldset>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">وضعیت فعال/غیر فعال کامپوننت ها</h4>
                </div>
            </div>
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
                <div class="col-md-12">
                    <h4 class="h4 legend">تنظیم اتصال به سیموتل</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
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
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="exten">شماره اکستن سیموتل </label>
                        <input type="text" name="exten" id="exten"
                               value="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten()}"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">انتخاب کلید های پاپ آپ</h4>
                </div>
            </div>

            {assign var="buttons" value=[
            "view_profile"=>"مشاهده پروفایل",
            "edit_profile"=>"ویرایش پروفایل",
            "services"=>"سرویس ها",
            "domains"=>"دامنه ها",
            "notes"=>"یادداشت ها",
            "tickets"=>"تیکت ها",
            "transactions"=>"تراکنش ها",
            "factors"=>"فاکتور ها",
            "pre_factors"=>"پیش فاکتور ها",
            "create_ticket"=>"ایجاد تیکت",
            "create_factor"=>"ایجاد فاکتور",
            "create_pre_factor"=>"ایجاد پیش فاکتور",
            "view_bill"=>"صورت حساب"
            ]}


            <div class="row">


                {foreach from=$buttons key=name item=caption}
                    <div class="col-xs-6 col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="on" name="popup_buttons[{$name}]"
                                       {if isset($popUpButtons->$name)}checked{/if}>
                                {$caption}
                            </label>
                        </div>
                    </div>
                {/foreach}

{*
                <div class="col-sm-6">
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
                <div class="col-sm-6">

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
                </div>*}
            </div>
        </div>

    </fieldset>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-left">
                <input type="submit" value="ذخیره تنظیمات" class="btn btn-success btn-sm " id="saveBtn">
            </div>
        </div>
    </div>

</form>
<div class="m-5">&nbsp</div>
<form action="" id="popupTest" class="simotelConfigs">
    <fieldset>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">تست پاپ آپ</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="participant" id="participant" class="form-control"
                               placeholder="شماره مخاطب تماس گیرنده">
                        <span class="input-group-btn">
                 <button class="btn btn-warning" type="submit">شبیه سازی تماس!</button>
             </span>
                    </div><!-- /input-group -->
                    <small id="popUpStatus"></small>
                </div>
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
        $("form#adminUserConfigs").submit(function (e) {
            e.preventDefault();
            $("#saveBtn").attr("disabled", "disabled")
            $.post($(this).attr("action"), $(this).serialize())
                .success(x => {
                    if (x.success == true) {
                        alert("ذخیره شد.");
                        location.reload();
                    }

                })
                .done(function () {
                    $("#saveBtn").attr("disabled", false)
                })
        })

        $("#popupTest").submit(function (e) {
            e.preventDefault();
            $("#popUpStatus").removeClass("text-danger text-success")
            $("#popUpStatus").html("در حال ارسال تماس آزمایشی");
            let participant = $("#participant").val();
            let exten = $("#exten").val();
            let url = "{$configs["WebRootUrl"] }/panel/index.php?m=simotel&state=Ringing&exten=" + exten + "&participant=" + participant
            $.get(url).success(result => {
                console.log(result)
                if (result.success) {
                    $("#popUpStatus").html("با موفقیت ارسال شد.");
                    $("#popUpStatus").addClass("text-success")
                } else {
                    $("#popUpStatus").html("خطا:" + result.message);
                    $("#popUpStatus").addClass("text-danger")
                }
            });
        })
    })

</script>
<style>
    .simotelConfigs fieldset {
        border: 1px solid #ddd;
        border-radius: 15px;
        margin-bottom: 20px;
        padding-bottom: 30px;
    }

    h4.legend {
        color: #aaa;
        font-weight: 600;
        font-size: 13px;
        margin: 10px 0;
        border-bottom: 1px #ddd solid;
        padding: 4px;
    }
</style>