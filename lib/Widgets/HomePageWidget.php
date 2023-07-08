<?php

namespace WHMCS\Module\Addon\Simotel\Widgets;

use \WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 * Sample Widget example
 */
class HomePageWidget extends \WHMCS\Module\AbstractWidget
{
    /**
     * @type string The title of the widget
     */
    protected $title = 'سیموتل';

    /**
     * @type string A description/purpose of the widget
     */
    protected $description = '';

    /**
     * @type int The sort weighting that determines the output position on the page
     */
    protected $weight = 150;

    /**
     * @type int The number of columns the widget should span (1, 2 or 3)
     */
    protected $columns = 2;

    /**
     * @type bool Set true to enable data caching
     */
    protected $cache = false;

    /**
     * @type int The length of time to cache data for (in seconds)
     */
    protected $cacheExpiry = 120;

    /**
     * @type string The access control permission required to view this widget. Leave blank for no permission.
     * @see Permissions section below.
     */
    protected $requiredPermission = '';

    /**
     * Get Data.
     *
     * Obtain data required to render the widget.
     *
     * We recommend executing queries and API calls within this function to enable
     * you to take advantage of the built-in caching functionality for improved performance.
     *
     * When caching is enabled, this method will be called when the cache is due for
     * a refresh or when the user invokes it.
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'myExten' => WhmcsOperations::getCurrentAdminExten() ?: "تنظیم نشده است",
            'serverProfile' => WhmcsOperations::getCurrentAdminServerProfile() ?: "تنظیم نشده است",
            "lastCalls" => $this->renderCalls($this->getMyLastCalls()),
        );
    }

    /**
     * Generate Output.
     *
     * Generate and return the body output for the widget.
     *
     * @param array $data The data returned by the getData method.
     *
     * @return string
     */
    public function generateOutput($data)
    {
        return <<<EOF
        <div class="widget-content-padded">
            <div style="padding: 0 5px">
                 <table class="table" >
                    <thead>
                    <tr>
                        <th></th>
                        <th>مخاطب</th>
                        <th></th>
                        <th>تاریخ</th>
                        <th>مدت</th>
                        <th>وضعیت</th>
                    </tr>
                    </thead>
                    <tbody>
                        {$data["lastCalls"]}
                    </tbody>
                 </table>
<!--            <h5 style="font-size: 12px;color: #888888;padding-top: 5px">آخرین تماس های من</h5>-->
            </div>
            <div style="font-size: 12px;margin-top: 15px">
                <span id="" style="margin-left: 30px"><strong>شماره داخلی سیموتل من: </strong>{$data["myExten"]}</span>
                <span id=""><strong> سرور: </strong>{$data["serverProfile"]}</span>
            </div>
        </div>
EOF;
    }

    private function getMyLastCalls()
    {
        return Call::whereAdminId(WhmcsOperations::getCurrentAdminId())
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item["contact"] = $item->direction == "in" ? $item->src : $item->dst;
                return $item;
            });

    }

    private function renderCalls($myLastCalls)
    {
        if (count($myLastCalls) < 1)
            return "<tr><td colspan='5'>تاریخچه تماس وجود ندارد</td></tr>";

        $html = "";
        foreach ($myLastCalls as $call) {
            $html .= "<tr>";
            $html .= "
                <td><img src='{$call->direction_icon_url}' alt='{$call->direction}' title='{$call->direction}'
                         width='15'></td>";
            $html .= "<td>$call->contact</td>";
            $html .= "<td><a href='{$call->client->profile_url}' class='clientName'>{$call->client->fullname_p}</a></td>";
            $html .= "<td>$call->created_at_fa</td>";
            $html .= "<td title='{$call->billsec_minutes}'>{$call->billsec_short}</td>
                <td><img src='{$call->status_icon_url}' alt='{$call->status}' title='{$call->status}' width='20'></td>";
            $html .= "</tr>";
        }
        return $html;
    }

}
