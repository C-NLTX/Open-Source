<?php

/**
 * Sample HTML page with display of global settings
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// we want to have access to events, the autoloader and our module bootstrap so we include globals here
require_once "../../../../globals.php";
use OpenEMR\Modules\jBPMGenericCallout\Bootstrap;

use OpenEMR\Common\Session\SessionUtil;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;

$twig = new TwigContainer(null, $GLOBALS['kernel']);

// Note we have to grab the event dispatcher from the globals kernel which is instantiated in globals.php
$bootstrap = new Bootstrap($GLOBALS['kernel']->getEventDispatcher());
$globalsConfig = $bootstrap->getGlobalConfig();

$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/kie-server/services/rest/server/containers/project_1.0.0-SNAPSHOT/processes/stroke-train/instances");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "content-type: application/json"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$filename = "./stroke.txt";
$params = file($filename); 
curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \"iterations\": { \"com.discovery.project.Iterations\": { \"number\": $params[0] } },".
" \"workdir\": { \"com.discovery.project.Workdir\": { \"path\": $params[1] } },".
" \"dropcols\": { \"com.discovery.project.Dropcols\": { \"droplist\": $params[2] } },".
" \"samplestruct\": { \"com.discovery.project.Samplestruct\": { \"balancing\": $params[3] } },".
" \"manage\": { \"com.discovery.project.Manage\": { \"publish\": $params[4] } },".
" \"train\": { \"com.discovery.project.Train\": { \"training\": $params[5], \"folds\": $params[11], \"bagfrac\": $params[12], \"weights\": $params[13], \"weightsaccval\": $params[14], \"weightsacctst\": $params[15] } },".
" \"test\": { \"com.discovery.project.Test\": { \"preddraw\": $params[6], \"predfrac\": $params[9] } },".
" \"costfunc\": { \"com.discovery.project.Costfunc\": { \"costoferror\": $params[7], \"costofchecking\": $params[8] } },".
" \"validate\": { \"com.discovery.project.Validate\": { \"testdraws\": $params[10] } } }");
$result = curl_exec ($ch);
$response = json_decode($result);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

?>
<html>
<title>Stroke Prediction</title>
<head>
    <?php
    Header::setupHeader(['common','utility']);
    require_once("$srcdir/options.js.php");
    ?>
</head>
<body>
<ul>
	<li>Response ID: <?php echo $response; ?></li>
	<li>Response code: <?php echo $status_code; ?></li>
</ul>
<img alt="STROKE031.png" src="STROKE031.png" width="320" height="240"/>
<img alt="STROKE032.png" src="STROKE032.png" width="320" height="240"/>
<img alt="STROKE033.png" src="STROKE033.png" width="320" height="240"/>
<img alt="STROKE034.png" src="STROKE034.png" width="320" height="240"/>
<img alt="STROKE011.png" src="STROKE011.png" width="320" height="240"/>
<img alt="STROKE011.png" src="STROKE013.png" width="320" height="240"/>
<img alt="STROKE011.png" src="STROKE021.png" width="320" height="240"/>
<img alt="STROKE011.png" src="STROKE023.png" width="320" height="240"/>
<br>
<br>
<a href="sample-index.php">Back to catalog</a>
</body>
</html>
