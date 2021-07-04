<form class="simotelConfigs" id="adminUserConfigs"
      action="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminPanelUrl("/addonmodules.php?module=simotel&action=storeMyConfigs")}"
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
                    <h4 class="h4 legend">اطلاعات سیموتل</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="simotel_profile"> سرور سیموتل</label>
                        <input type="text"
                               value="{if WHMCS\Module\Addon\Simotel\WhmcsOperations::getCurrentAdminServerProfile()}{WHMCS\Module\Addon\Simotel\WhmcsOperations::getCurrentAdminServerProfile()}{else}در انتظار ادمین{/if}"
                               class="form-control" disabled>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="exten">شماره داخلی سیموتل </label>
                        <input type="text" name="exten" id="exten"
                               value="{if WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten()}{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten()}{else}در انتظار ادمین{/if}"
                               class="form-control" disabled>
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
<div class="m-5">&nbsp</div>
<form action="" id="clickToDial" class="simotelConfigs">
    <fieldset>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">ارسال تماس (کلیک تو دایل)</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="dst" id="dst" class="form-control"
                               placeholder="شماره تماس">
                        <span class="input-group-btn">
                 <button class="btn btn-warning" type="submit">ارسال تماس</button>
             </span>
                    </div><!-- /input-group -->
                    <small id="clickToDialStatus"></small>
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
            let url = "{$configs["WebRootUrl"] }/panel/index.php?m=simotel&event_name=TestEvent&exten=" + exten + "&participant=" + participant
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
        $("#clickToDial").submit(function (e) {
            e.preventDefault();
            let dst = $("#dst").val();
            $.get(panelWebUrl + "/addonmodules.php?module=simotel&action=simotelCall&callee=" + dst)
                .done(x => {
                    if (x.success) {
                        $("#clickToDialStatus").html("با موفقیت ارسال شد.");
                        $("#clickToDialStatus").addClass("text-success")
                    } else {
                        $("#clickToDialStatus").html("خطا در ارسال تماس:" + result.message);
                        $("#clickToDialStatus").addClass("text-danger")
                    }
                })
                .fail(x => {
                    setStatus("error", "خطا در ارسال تماس.");
                })


        })
    })
    /**/
</script>
