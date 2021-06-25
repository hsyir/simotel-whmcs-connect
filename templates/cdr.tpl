<h3 class="mt-5">ریز مکالمات</h3>
<form class="simotelConfigs" id="cdrFilterForm"
      action="{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminPanelUrl()}/addonmodules.php?module=simotel&action=cdrReport"
      method="get">
    <fieldset>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="h4 legend">فیلتر تماس ها</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="src">مبدا</label>
                        <input type="text" name="src" id="src"
                               value="{$request->src}"
                               class="form-control">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="dst">مقصد</label>
                        <input type="text" name="dst" id="dst"
                               value="{$request->dst}"
                               class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <input type="submit" value="فیلتر" class="btn btn-success btn-sm " id="saveBtn">
                    <a class="btn btn-warning btn-sm" href="#" id="clearFilter">فراموشی فیلتر</a>
                </div>
            </div>
        </div>
    </fieldset>
    <div class="container-fluid">
    </div>

</form>
<div style="margin: 20px 0">
    {$pagination}
</div>
<div class="container-fluid mt-5">
    <table class="table cdrTable">
        <thead>
        <tr>
            <th></th>
            <th>مبدا</th>
            <th>مقصد</th>
            <th>زمان</th>
            <th>مدت تماس</th>
            <th>وضعیت</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$calls key=key item=call}
            <tr >
                <td><img src="{$call->direction_icon_url}" alt="{$call->direction}" title="{$call->direction}"
                         width="15"></td>
                {if $call->direction=="in"}
                    <td class="client">
                        <span class="number" dir="ltr">{$call["src"]}</span>&nbsp;
                        <a class="clientName" href="{$call->client->profile_url}">{$call->client->fullname_p}</a>
                    </td>
                    <td class="admin">
                        <span class="number" dir="ltr">{$call["dst"]}</span>&nbsp;
                        <span class="name">{$call->admin->fullname_p}</span>
                    </td>
                {else}
                    <td class="admin">
                        <span class="number" dir="ltr">{$call["src"]}</span>&nbsp;
                        <span class="name"> {$call->admin->fullname_p}</span>
                    </td>
                    <td class="client">
                        <span class="number" dir="ltr">{$call["dst"]}</span>&nbsp;
                        <a href="{$call->client->profile_url}" class="clientName">{$call->client->fullname_p}</a>
                    </td>
                {/if}
                <td>{$call->created_at_fa}</td>
                <td title="{$call->billsec_minutes}">{$call->billsec_short}</td>
                <td><img src="{$call->status_icon_url}" alt="{$call->status}" title="{$call->status}" width="20"></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
<div class="m-5">
    {$pagination}
</div>
<script>
    $("document").ready(function () {
        $("form#cdrFilterForm").submit(function (e) {
            e.preventDefault();
            window.location = $(this).attr("action") + "&" + $(this).serialize()
        })
        $("#clearFilter").click(function (e) {
            e.preventDefault();
            window.location = $("form#cdrFilterForm").attr("action")
        })
    })
</script>
<style>
    .cdrTable span.number {
    }
    .cdrTable .admin {
    }

    .cdrTable .admin span.name {
        color: #b85c5c;
    }

    .cdrTable .client {
    }

    .cdrTable .client a {
    }
</style>
