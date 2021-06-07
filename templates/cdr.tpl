{$pagination}
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
                <th>Direction</th>
                <th>Status</th>
                <th >UId</th>
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
                <td >{$call->direction}</td>
                <td >{$call->status}</td>
                <td >{$call->unique_id}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
