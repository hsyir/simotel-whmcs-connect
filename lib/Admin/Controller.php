<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use Hsy\Simotel\Simotel;
use League\Container\Container;
use WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PBX\Connectors\SimotelConnector;
use WHMCS\Module\Addon\Simotel\PBX\Pbx;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\Request;
use WHMCS\Module\Addon\Simotel\Smarty;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;
use WHMCS\Database\Capsule;


/**
 * Admin Area Controller
 */
class Controller
{
    private $request;

    public function __construct()
    {
        $this->request = new Request;
    }

    public function index($vars)
    {
        return $this->cdrReport();
    }

    public function userConfigs()
    {
        $adminId = WhmcsOperations::getCurrentAdminId();

        $options = new Options();
        $adminOptions = $options->getAdminOptions($adminId);
        $popUpButtons = $adminOptions->selectedPopUpButtons;
        $exten = WhmcsOperations::getCurrentAdminExten();

        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);

        return Smarty::render("adminUserOptions", compact("simotelServers", 'adminOptions', 'popUpButtons', "exten"));
    }

    public function cdrReport()
    {

        list($calls, $pagination) = $this->getCalls();

        return Smarty::render("cdr", compact("calls", "pagination"));
    }

    public function storeAdminsExtens()
    {
        $extens = $this->request->adminExten;
        $profiles = $this->request->profiles;

        $options = new Options;
        foreach ($extens as $adminId => $exten) {
            $options->set("exten", $exten, $adminId);
        }

        foreach ($profiles as $adminId => $profile) {
            $options->set("serverProfile", $profile, $adminId);
        }

        $this->echoResponse(["success" => true]);
    }

    public function adminsList()
    {
        if (!WhmcsOperations::adminCanConfigureModuleConfigs())
            return "Unauthorized";


        $adminsOptions = new Options();
        $options = $adminsOptions->getAllAdminsOptions()->keyBy("admin_id")->map(function ($item) {
            return json_decode($item->value);
        })->toArray();

        $admins = Capsule::table('tbladmins')
            ->get(["firstname", "lastname", "username", "id"])
            ->map(function ($item) use ($options) {
                $item = (array)$item;
                $item["options"] = ($options[$item["id"]]);
                return $item;
            })->toArray();


        $opts = new Options();
        $simotelServerProfiles = $opts->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);

        return Smarty::render("allAdminsConfigs", compact("admins", "simotelServers"));
    }

    public function authorizeChannel()
    {
        echo (new PushNotification())->authorize("whmcsChannel");
        exit;
    }

    public function storeMyConfigs()
    {
        $adminId = WhmcsOperations::getCurrentAdminId();
        $callerIdPopUpActive = $_REQUEST["caller_id_pop_up"] == "on";
        $clickToDialActive = $_REQUEST["click_to_dial"] == "on";

        $popUpButtons = $_REQUEST["popup_buttons"] ?? [];
        $selectedPopUpButtons = [];
        foreach ($popUpButtons as $btn => $status) {
            $selectedPopUpButtons[$btn] = true;
        }
        $optionValues = compact("callerIdPopUpActive", "clickToDialActive", "selectedPopUpButtons");

        $options = new Options();
        $options->setAdminOptions($adminId, $optionValues);
        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }

    public function moduleConfigForm()
    {

        if (!WhmcsOperations::adminCanConfigureModuleConfigs())
            return "Unauthorized";

        list($calls, $HTMLpagePagination) = $this->getCalls();

        $options = new Options();
        $simotelServers = $options->get("simotelServerProfiles", null, []);
        $simotelServers = json_decode($simotelServers);

        return Smarty::render("moduleConfigs", compact("simotelServers"));
    }

    public function storeModuleConfigs()
    {
        $profiles = $_REQUEST["simotelServerProfile"];
        $profiles = json_encode($profiles);

        $options = new Options();
        $options->set("simotelServerProfiles", $profiles);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }

    // --------------------------------------------------------------------
    // ---- Click To Dial -------------------------------------------------
    // --------------------------------------------------------------------
    public function simotelCall($vars)
    {
        $callee = $_REQUEST["callee"];
        if (!$callee) $this->returnCallError("شماره مقصد نامشخص است");

        $pbx = new Pbx();
        $result = $pbx->sendCall($callee);

        header('Content-Type: application/json');
        if ($pbx->fails()) {
            echo json_encode(["success" => false, "message" => $pbx->errors()]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;

    }

    /**
     * @param $record_count
     * @param int $offset
     * @param int $page
     * @param string $modulelink
     * @param float $offsets
     * @return string
     */
    private function paginate($record_count, int $offset, int $page, string $modulelink, float $offsets): string
    {
        $HTMLpagePagination = "";
        if ($record_count < $offset + 1) {
            $HTMLpagePagination .= '<div class="clearfix">';
            $HTMLpagePagination .= '<div class="hint-text pull-left">Showing <b>' . $record_count . '</b> out of <b>' . $record_count . '</b> pages</div>';
            $HTMLpagePagination .= '</div>';
        }
        if ($record_count > $offset) {
            $HTMLpagePagination .= '<div class="clearfix">';
            if ($page * $offset < $record_count) {
                $HTMLpagePagination .= '<div class="hint-text pull-left">Showing <b>' . $page * $offset . '</b> out of <b>' . $record_count . '</b> records</div>';
            } else {
                $HTMLpagePagination .= '<div class="hint-text pull-left">Showing <b>' . $record_count . '</b> out of <b>' . $record_count . '</b> records</div>';
            }
            $HTMLpagePagination .= '<ul style="margin:0px 0px" class="pagination pull-right">';
            if ($page < 2) {
                $HTMLpagePagination .= '<li class="page-item disabled"><a href="#">Previous</a></li>';
            } else {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page - 1) . '">Previous</a></li>';
            }
            if ($page - 2 > 0) {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page - 2) . '" class="page-link">' . ($page - 2) . '</a></li>';
            }
            if ($page - 1 > 0) {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page - 1) . '" class="page-link">' . ($page - 1) . '</a></li>';
            }
            $HTMLpagePagination .= '<li class="page-item active"><a href="' . $modulelink . '&page=' . ($page) . '" class="page-link">' . ($page) . '</a></li>';
            if ($page + 1 < $offsets + 1) {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page + 1) . '" class="page-link">' . ($page + 1) . '</a></li>';
            }
            if ($page + 2 < $offsets + 1) {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page + 2) . '" class="page-link">' . ($page + 2) . '</a></li>';
            }
            if ($page + 1 < $offsets + 1) {
                $HTMLpagePagination .= '<li class="page-item"><a href="' . $modulelink . '&page=' . ($page + 1) . '" class="page-link">Next</a></li>';
            } else {
                $HTMLpagePagination .= '<li class="page-item disabled"><a href="#" class="page-link">Next</a></li>';
            }
            $HTMLpagePagination .= '</ul>';
            $HTMLpagePagination .= '</div>';
        }
        return $HTMLpagePagination;
    }

    /**
     * @return array
     */
    private function getCalls(): array
    {
        $clientId = $this->request->client_id;
        $src = $this->request->src;
        $dst = $this->request->dst;
        $callQuery = Call
            ::when($src, function ($q) use ($src) {
                return $q->where("src", "like", "%$src%");
            })
            ->when($dst, function ($q) use ($dst) {
                return $q->where("dst", "like", "%$dst%");
            })
            ->when($clientId, function ($q) use ($clientId) {
                return $q->whereClientId($clientId);
            })
            ->when(!WhmcsOperations::adminCanConfigureModuleConfigs(), function ($q) {
                return $q->whereAdminId(WhmcsOperations::getCurrentAdminId());
            });
        $record_count = $callQuery->count();
        $offset = 50;
        $offsets = $record_count / $offset;
        $page = $this->request->page ?? 1;
        if ($record_count != null) {
            $calls = $callQuery
                ->with("admin", "client")
                ->offset(($page - 1) * $offset)
                ->limit($offset)
                ->orderBy("id", "DESC")
                ->get();
        }
        $queryString = ($dst ? "&dst=$dst" : "") . ($src ? "&src=$src" : "");
        $modulelink = adminUrl("/addonmodules.php?module=simotel&action=cdrReport{$queryString}");
        $HTMLpagePagination = $this->paginate($record_count, $offset, $page, $modulelink, $offsets);
        return array($calls, $HTMLpagePagination);
    }

    private function echoResponse($result)
    {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }


    public function downloadAudio()
    {
        if (!WhmcsOperations::adminCanConfigureModuleConfigs())
            return "Unauthorized";

        $callId = $this->request->call_id;
        $call = Call::find($callId);
        $simotel = new SimotelConnector();
        $simotel->downloadAudio($call->record);
    }
}
