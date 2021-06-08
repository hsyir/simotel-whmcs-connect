<div class="container-fluid mt-5">
    <table class="table">
        <thead>

        </thead>
        <tbody>
        {foreach from=$admins key=key item=admin}
            <tr>
                <td>{$admin['id']}</td>
                <td>{$admin["firstname"]} {$admin["lastname"]}</td>
                <td>{WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminExten($admin["id"])}</td>
                <td>{$admin["options"]->simotelProfileName}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
