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
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

// Register input variables
phpAds_registerGlobal ('returnurl');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);

// CVE-2013-5954 - see OA_Permission::checkSessionToken() method for details
OA_Permission::checkSessionToken();

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if (!empty($networkid)) {
    $ids = explode(',', $networkid);
    while (list(,$networkid) = each($ids)) {

        // Security check
        OA_Permission::enforceAccessToObject('networks', $networkid, false, OA_Permission::OPERATION_DELETE);

        $doNetworks = OA_Dal::factoryDO('networks');
        $doNetworks->networkid = $networkid;
        if ($doNetworks->get($networkid)) {
            $aNetwork = $doNetworks->toArray();
        }

        $doNetworks->delete();
    }

    // Queue confirmation message
    $translation = new OX_Translation ();

    if (count($ids) == 1) {
        $translated_message = $translation->translate ($GLOBALS['strNetworkHasBeenDeleted'], array(
        htmlspecialchars($aNetwork['name'])
        ));
    } else {
        $translated_message = $translation->translate ($GLOBALS['strNetworksHaveBeenDeleted']);
    }

    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
}

// Run the Maintenance Priority Engine process
OA_Maintenance_Priority::scheduleRun();

// Rebuild cache
// require_once MAX_PATH . '/lib/max/deliverycache/cache-'.$conf['delivery']['cache'].'.inc.php';
// phpAds_cacheDelete();


/*-------------------------------------------------------*/
/* Return to the index page                              */
/*-------------------------------------------------------*/

if (!isset($returnurl) && $returnurl == '') {
    $returnurl = 'network-index.php';
}

header("Location: ".$returnurl);

?>