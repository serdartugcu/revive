<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/lib/OX/Util/Utils.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/ViewHooks.php';

// Register input variables
phpAds_registerGlobal('hideinactive', 'listorder', 'orderdirection');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader(null, buildHeaderModel());


/*-------------------------------------------------------*/
/* Get preferences                                       */
/*-------------------------------------------------------*/

if (!isset($hideinactive)) {
    if (isset($session['prefs']['network-index.php']['hideinactive'])) {
        $hideinactive = $session['prefs']['network-index.php']['hideinactive'];
    } else {
        $pref = &$GLOBALS['_MAX']['PREF'];
        $hideinactive = ($pref['ui_hide_inactive'] == true);
    }
}

if (!isset($listorder)) {
    if (isset($session['prefs']['network-index.php']['listorder'])) {
        $listorder = $session['prefs']['network-index.php']['listorder'];
    } else {
        $listorder = '';
    }
}

if (!isset($orderdirection)) {
    if (isset($session['prefs']['network-index.php']['orderdirection'])) {
        $orderdirection = $session['prefs']['network-index.php']['orderdirection'];
    } else {
        $orderdirection = '';
    }
}


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('network-index.html');

// Get networks & affiliates and build the tree
// XXX: Now that the two are next to each other, some silliness
//      is quite visible -- retrieving all items /then/ retrieving a count.
// TODO: This looks like a perfect candidate for object "polymorphism"
$dalNetworks = OA_Dal::factoryDAL('networks');
$dalAffiliates = OA_Dal::factoryDAL('affiliates');

$affiliates = array();

if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN)) {
    $networks = $dalNetworks->getAllNetworks($listorder, $orderdirection);
    if ($hideinactive) {
        $affiliates = $dalAffiliates->getAllAffiliates($listorder, $orderdirection);
    }
}
elseif (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
    $agency_id = OA_Permission::getEntityId();
    $networks = $dalNetworks->getAllNetworksForAgency($agency_id, $listorder, $orderdirection);
    if ($hideinactive) {
        $affiliates = $dalAffiliates->getAllAffiliatesUnderAgency($agency_id, $listorder, $orderdirection);
    }
}

$aCount = array(
    'networks'        => count($networks),
    'networks_hidden' => 0
);


if ($hideinactive && !empty($networks) && !empty($affiliates)) {

    // Build Tree
    foreach ($affiliates as $ckey => $affiliates) {
        if ((OA_ENTITY_STATUS_RUNNING == $affiliate['status'])) {

            $networks[$affiliate['networkid']]['has_active_affiliates'] =
            true;
        }
    }

    foreach (array_keys($networks) as $networkid) {
        $network = &$networks[$networkid];

        if (!array_key_exists('has_active_affiliates', $network)
        // we do not hide the Market advertiser
        && $network['type'] != DataObjects_Clients::ADVERTISER_TYPE_MARKET) {
            unset($networks[$networkid]);
            $aCount['networks_hidden']++;
        }
    }
}

$itemsPerPage = 250;
$oPager = OX_buildPager($networks, $itemsPerPage);
$oTopPager = OX_buildPager($networks, $itemsPerPage, false);
list($itemsFrom, $itemsTo) = $oPager->getOffsetByPageId();
$networks =  array_slice($networks, $itemsFrom - 1, $itemsPerPage, true);

$oTpl->assign('pager', $oPager);
$oTpl->assign('topPager', $oTopPager);

$oTpl->assign('aNetworks', $networks);
$oTpl->assign('aCount', $aCount);
$oTpl->assign('hideinactive', $hideinactive);
$oTpl->assign('listorder', $listorder);
$oTpl->assign('orderdirection', $orderdirection);
$oTpl->assign('MARKET_TYPE', DataObjects_Networks::NETWORK_TYPE_MARKET);



/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['network-index.php']['hideinactive'] = $hideinactive;
$session['prefs']['network-index.php']['listorder'] = $listorder;
$session['prefs']['network-index.php']['orderdirection'] = $orderdirection;
phpAds_SessionDataStore();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/
OX_Admin_UI_ViewHooks::registerPageView($oTpl, 'network-index');

$oTpl->display();
phpAds_PageFooter();


function buildHeaderModel()
{
    $builder = new OA_Admin_UI_Model_InventoryPageHeaderModelBuilder();
    return $builder->buildEntityHeader(array(), 'networks', 'list');
}

?>
