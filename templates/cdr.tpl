{$pagination}
<div class="container-fluid mt-5">
    <table class="table">
        <thead>

        </thead>
        <tbody>
        {foreach from=$admins key=key item=admin}
            <tr>
                <td>{$admin["firstname"]} {$admin["lastname"]}</td>
                <td>{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten($admin["id"])}</td>
                <td>{$admin["options"]->simotelProfileName}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

<h3 class="mt-5"> تماس ها</h3>

<div class="container-fluid mt-5">
    <table class="table">
        <thead>
            <tr>
                <th>Src</th>
                <th>Dst</th>
                <th>Admin</th>
                <th>Client</th>
                <th>Created At</th>
                <th>Billsec</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$calls key=key item=call}
            <tr >
                <td>{$call["src"]}</td>
                <td>{$call["dst"]}</td>
                <td>{$call->admin->fullname}</td>
                <td>{$call->client->fullname}</td>
                <td dir="ltr" class="text-center">{$call->created_at_fa}</td>
                <td dir="ltr" class="text-center">{$call->billsec_minutes}</td>
                <td >{$call->status}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
