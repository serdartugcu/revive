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

$oRequest = new stdClass();

$oRequest->what     = 'zone:1';
$oRequest->target   = '';
$oRequest->source   = '';
$oRequest->withText = false;
$oRequest->context  = 0;
$oRequest->richMedia = true;
$oRequest->ct0      = '';
$oRequest->loc      = 'http://www.beccati.com';
$oRequest->referer  = '';
$oRequest->requests = 99;

if (version_compare(phpversion(), '5.0.0', '>='))
	$oRequest2 = clone $oRequest;
else
	$oRequest2 = $oRequest;

$oRequest2->loc      = 'http://www.beccati.com/matteo.php';
$oRequest2->requests = 1;

$aIterations[1]['request_objects'][1]   = $oRequest;
$aIterations[1]['request_objects'][2]   = $oRequest2;
$aIterations[1]['precise_requests']     = true;
$aIterations[1]['max_requests']         = 40;

for($i=2;$i<25;$i++)
{
    $aIterations[$i] = $aIterations[1];
}

$iterations = count($aIterations);

$precis=    '<pre>'."Chris Scenario:

Zones (2400 impressions):
1.	100 hourly impressions, 1 matching C2 target (2376 matching, 24 not matching total)

Campaigns:
1.	ROS no targeting	2160 impressions target
2.	Targeted to small section	requires 48 to hit target (2x over booked section)
3.	Low priority
".'</pre>';

$precis.=    print_r($oRequest, true);

$GLOBALS['_MAX']['CONF']['sim']['precis']       = $precis;
$GLOBALS['_MAX']['CONF']['sim']['iterations']   = $iterations;
$GLOBALS['_MAX']['CONF']['sim']['oRequest']     = $oRequest;
$GLOBALS['_MAX']['CONF']['sim']['aIterations']  = $aIterations;
$GLOBALS['_MAX']['CONF']['sim']['startdate']    = "2007-02-06 00:00:00";
$GLOBALS['_MAX']['CONF']['sim']['startday']     = "2007-02-06";
$GLOBALS['_MAX']['CONF']['sim']['starthour']    = "0";
?>