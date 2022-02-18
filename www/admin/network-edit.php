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
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/RV/Admin/Languages.php';
require_once MAX_PATH . '/lib/OA/Admin/Menu.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/model/InventoryPageHeaderModelBuilder.php';

// Register input variables
phpAds_registerGlobalUnslashed(
'errormessage'
,'name'
,'contact'
,'comments'
,'email'
,'reportlastdate'
,'advertiser_limitation'
,'reportprevious'
,'reportdeactivate'
,'report'
,'reportinterval'
,'submit'
);


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
OA_Permission::enforceAccessToObject('networks', $networkid, true);


/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['networkid'] = $networkid;
phpAds_SessionDataStore();

/*-------------------------------------------------------*/
/* Initialise data                                    */
/*-------------------------------------------------------*/
if ($networkid != "") {
    if (!isset($aNetwork)) {
        $doNetworks = OA_Dal::factoryDO('networks');
        if ($doNetworks->get($networkid)) {
            $aNetwork = $doNetworks->toArray();
        }
    }
}
else {
    if (!isset($aNetwork)) {
        $aNetwork['name']             = '';
        $aNetwork['revenueshare']     = '';
    }
}
/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
//build network form
$networkForm = buildNetworkForm($aNetwork);

if ($networkForm->validate()) {
    //process submitted values
    processForm($aNetwork, $networkForm);
}
else { //either validation failed or form was not submitted, display the form
    displayPage($aNetwork, $networkForm);
}

/*-------------------------------------------------------*/
/* Build form                                            */
/*-------------------------------------------------------*/
function buildNetworkForm($aNetwork)
{
    $form = new OA_Admin_UI_Component_Form("networkform", "POST", $_SERVER['SCRIPT_NAME']);
    $form->forceClientValidation(true);

    $form->addElement('hidden', 'networkid', $aNetwork['networkid']);
    $form->addElement('header', 'header_basic', $GLOBALS['strBasicInformation']);

    $nameElem = $form->createElement('text', 'name', $GLOBALS['strName']);
    if (!OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $nameElem->freeze();
    }
    $form->addElement($nameElem);
    $form->addElement('text', 'revenueshare', $GLOBALS['strRevenueShare']);

    //we want submit to be the last element in its own separate section
    $form->addElement('controls', 'form-controls');
    $form->addElement('submit', 'submit', $GLOBALS['strSaveChanges']);

    //Form validation rules
    $translation = new OX_Translation();
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $nameRequiredMsg = $translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strName']));
        $form->addRule('name', $nameRequiredMsg, 'required');
    }


    $revenueRequiredMsg = $translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strRevenueShare']));
    $form->addRule('revenueshare', $revenueRequiredMsg, 'required');

    //set form  values
    $form->setDefaults($aNetwork);
    return $form;
}


/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/
function processForm($aNetwork, $form)
{
    $aFields = $form->exportValues();

    // Name
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER) ) {
        $aNetwork['name'] = $aFields['name'];
    }
    // Default fields
    $aNetwork['revenueshare']  = $aFields['revenueshare'];

    if (empty($aNetwork['networkid'])) {
        // Set agency ID
        $aNetwork['agencyid'] = OA_Permission::getAgencyId();

        $doNetworks = OA_Dal::factoryDO('networks');
        $doNetworks->setFrom($aNetwork);
        $doNetworks->updated = OA::getNow();

        // Insert
        $aNetwork['networkid'] = $doNetworks->insert();

        // Queue confirmation message
        $translation = new OX_Translation ();
        $translated_message = $translation->translate ( $GLOBALS['strNetworkHasBeenAdded'], array(
        MAX::constructURL(MAX_URL_ADMIN, 'network-edit.php?networkid=' .  $aNetwork['networkid']),
        htmlspecialchars($aNetwork['name']),
        MAX::constructURL(MAX_URL_ADMIN, 'affiliate-edit.php?networkid=' .  $aNetwork['networkid']),
        ));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

        // Go to next page
        OX_Admin_Redirect::redirect("network-index.php");
    }
    else {
        $doNetworks = OA_Dal::factoryDO('networks');
        $doNetworks->get($aNetwork['networkid']);
        $doNetworks->setFrom($aNetwork);
        $doNetworks->updated = OA::getNow();
        $doNetworks->update();

        // Queue confirmation message
        $translation = new OX_Translation ();
        $translated_message = $translation->translate ( $GLOBALS['strNetworkHasBeenUpdated'],
        array(
        MAX::constructURL(MAX_URL_ADMIN, 'network-edit.php?networkid=' .  $aNetwork['networkid']),
        htmlspecialchars($aNetwork['name'])
        ));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
        OX_Admin_Redirect::redirect('network-edit.php?networkid=' .  $aNetwork['networkid']);
    }
    exit;
}

/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($aNetwork, $form)
{
    //header and breadcrumbs
    $oHeaderModel = buildNetworkHeaderModel($aNetwork);
    if ($aNetwork['networkid'] != "") {
        if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN) || OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
            OA_Admin_Menu::setNetworkPageContext($aNetwork['networkid'], 'network-index.php');
            addNetworkPageToolsAndShortcuts($aNetwork['networkid']);
            phpAds_PageHeader(null, $oHeaderModel);
        }
        else {
            phpAds_PageHeader(null, $oHeaderModel);
        }
    }
    else { //new Network
        phpAds_PageHeader('network-edit_new', $oHeaderModel);
    }

    //get template and display form
    $oTpl = new OA_Admin_Template('network-edit.html');

    $oTpl->assign('networkid',  $aNetwork['networkid']);
    $oTpl->assign('form', $form->serialize());
    $oTpl->display();

    //footer
    phpAds_PageFooter();
}

?>
