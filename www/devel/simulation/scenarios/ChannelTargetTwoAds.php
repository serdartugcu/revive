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

require_once SIM_PATH . 'SimulationScenario.php';

/**
 * A class for simulating maintenance/delivery scenarios
 *
 * @package
 * @subpackage
 */
class ChannelTargetTwoAds extends SimulationScenario
{

    /**
     * The constructor method.
     */
    function __construct()
    {
        $this->init("ChannelTargetTwoAds");
    }

    function run()
    {
        $this->newTables();
        $this->loadDataset("ChannelTargetTwoAds.xml");
        $this->printPrecis();
        for($i=1;$i<=$this->scenarioConfig['iterations'];$i++)
        {
            $this->makeRequests($i);
            $this->runPriority();
        }
        //$this->runMaintenance();
        $this->printPostSummary();
        $this->printSummaryData();
    }

}

?>