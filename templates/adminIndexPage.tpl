<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
<script type="text/javascript">
    {literal} window.channelName=  "{/literal}{$channelName}{literal}";{/literal}
</script>
<script>
    var options = {
        cluster: 'mt1',
        wsHost: "pusher.hsy.ir",
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        authEndpoint: "/admin/addonmodules.php?module=simotel&action=authorizeChannel"
    }
    var pusher = new Pusher("asdasd", options);

    var channel = pusher.subscribe(channelName);

    channel.bind("newCall", (data) => {
        logCall(data);

    });

    function logCall(callData){
        var newNode=document.createElement("div");
        $(newNode).addClass("history-item");
        $(newNode).html("<div>Caller Id: "+callData.participant+"</div>");
        if(callData.client_id){
            $(newNode).append("<div>Client:"+callData.client.fullname+"</div>");
            $(newNode).append("<div>Client Profile: "+"<a href='/admin/clientssummary.php?userid="+callData.client.id+"'>Go To Profile</a></div>");
            $(newNode).append("<div>Active Tickets: "+"<a href='/admin/client/"+callData.client.id+"/tickets'>Go To Tickets</a></div>");
            $(newNode).append("<div>Open New Ticket: "+"<a href='/admin/supporttickets.php?action=open&userid="+callData.client.id+"'>New Ticket</a></div>");
            $(newNode).append("<div>Notes: "+callData.client.notes+"</div>");
        }
        $("#history").prepend(newNode);
    }

</script>
<h3 class="h5">مرکز تماس سیموتل</h3>

<h6>تاریخچه تماس ها:</h6>
<div id="history" dir="ltr"></div>
<style>
    .history-item {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px #aaa solid;
    }
</style>
