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

/**
 * Table Definition for networks
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Networks extends DB_DataObjectCommon
{
    var $onDeleteCascade = true;
    var $dalModelName = 'Networks';
    var $usernameField = 'networkusername';
    var $passwordField = 'networkpassword';
    var $refreshUpdatedFieldIfExists = true;

    /**
     * Defines networks types
     */
    const NETWORK_TYPE_DEFAULT = 0;
    const NETWORK_TYPE_MARKET = 1;

    /**
     * BC-compatible user details
     *
     * @todo Please remove later
     */
    var $networkusername;
    var $networkpassword;

    /**
     * Autogenerated
     */
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'networks';                         // table name
    public $networkid;                       // MEDIUMINT(9) => openads_mediumint => 129
    public $agencyid;                        // MEDIUMINT(9) => openads_mediumint => 129
    public $name;                            // VARCHAR(255) => openads_varchar => 130
    public $revenueshare;                    // VARCHAR(255) => openads_varchar => 2
    public $account_id;                      // MEDIUMINT(9) => openads_mediumint => 1
    public $type;                            // TINYINT(4)   => openads_tinyint => 129

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGetFromClassName('DataObjects_Networks',$k,$v); }

    var $defaultValues = array(
    'agencyid' => 0,
    'name' => '',
    'revenueshare' => '',
    'type' => NETWORK_TYPE_DEFAULT,
    );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    /**
     * Returns networkid.
     *
     * @return string
     */
    function getUserId()
    {
        return $this->networkid;
    }

    function _auditEnabled()
    {
        return true;
    }

    function _getContextId()
    {
        return $this->networkid;
    }

    function _getContext()
    {
        return 'Network';
    }

    /**
     * A method to return the ID of the manager account
     * that "owns" this network account.
     *
     * @return integer The account ID of the "owning"
     *                 manager account. Returns the
     *                 admin account ID if no owning
     *                 manager account can be found.
     */
    function getOwningManagerId()
    {
        $doAgency = OA_Dal::factoryDO('agency');
        $doAgency->agencyid = $this->agencyid;
        $doAgency->find();
        if ($doAgency->getRowCount() == 1) {
            $doAgency->fetch();
            return $doAgency->account_id;
        } else {
            // Could not find the owning manager
            // account ID, return the ID of the
            // admin account instead
            return OA_Dal_ApplicationVariables::get('admin_account_id');
        }
    }

    /**
     * Handle all necessary operations when new network is created
     *
     * @see DB_DataObject::insert()
     */
    function insert()
    {
        // Create account first
        $result = $this->createAccount(OA_ACCOUNT_NETWORK, $this->name);
        if (!$result) {
            return $result;
        }

        // Store data to create a user
        if (!empty($this->networkusername) && !empty($this->networkpassword)) {
            $aUser = array(
            'username' => $this->networkusername,
            'password' => $this->networkpassword,
            'default_account_id' => $this->account_id
            );
        }

        $networkId = parent::insert();
        if (!$networkId) {
            return $networkId;
        }

        // Create user if needed
        if (!empty($aUser)) {
            $this->createUser($aUser);
        }

        return $networkId;
    }

    /**
     * Handle all necessary operations when an network is updated
     *
     * @see DB_DataObject::update()
     */
    function update($dataObject = false)
    {
        // Store data to create a user
        if (!empty($this->networkusername) && !empty($this->networkpassword)) {
            $aUser = array(
            'username' => $this->networkusername,
            'password' => $this->networkpassword,
            'default_account_id' => $this->account_id
            );
        }

        $ret = parent::update($dataObject);
        if (!$ret) {
            return $ret;
        }

        // Create user if needed
        if (!empty($aUser)) {
            $this->createUser($aUser);
        }

        $this->updateAccountName($this->name);

        return $ret;
    }

    /**
     * Handle all necessary operations when an network is deleted
     *
     * @see DB_DataObject::delete()
     */
    function delete($useWhere = false, $cascade = true, $parentid = null)
    {
        $result =  parent::delete($useWhere, $cascade, $parentid);
        if ($result) {
            $this->deleteAccount();
        }

        return $result;
    }

    /**
     * build a network specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        $aAuditFields['key_desc']   = $this->name;
    }

}

?>