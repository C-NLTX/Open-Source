<?php

/**
 * Sample HTML page
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../../../globals.php");

use OpenEMR\Common\Session\SessionUtil;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;

$twig = new TwigContainer(null, $GLOBALS['kernel']);

?>
<!DOCTYPE html>
<html>
<title>jBPM Generic Callout</title>
<head>
    <?php
    Header::setupHeader(['common','utility']);
    require_once("$srcdir/options.js.php");
    ?>
</head>
<body>
	<?php
	$response = "";
	if(isset($_POST['trainvalidatetest']))
	{
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
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
		curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/kie-server/services/rest/server/queries/processes/instances/".$response."/variables/instances");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "content-type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$stroke_result = curl_exec ($ch);
		$content = json_decode($stroke_result, true);
		$content_accuracy=$content['variable-instance'][26]['value'];
		$content_tpr=$content['variable-instance'][27]['value'];
		$content_tnr=$content['variable-instance'][28]['value'];
		$content_ppv=$content['variable-instance'][29]['value'];
		$content_npv=$content['variable-instance'][30]['value'];
		curl_close($ch);
	}
    ?>
	<span><b>Health AI Solution Catalog</b></span>
	<details>
	<summary>
		<span>Stroke Prediction</span>
	</summary>
	<form method="post" action="sample-index.php">
		<input type="submit" name="trainvalidatetest" value="Train/Validate/Test")>
	</form>
	<ul>
		<li>Response ID: <?php echo $response; ?></li>
		<li>Response code: <?php echo $status_code; ?></li>
		<li>Test accuracy: <?php echo $content_accuracy; ?></li>
		<li>Test TPR: <?php echo $content_tpr; ?></li>
		<li>Test TNR: <?php echo $content_tnr; ?></li>
		<li>Test PPV: <?php echo $content_ppv; ?></li>
		<li>Test NPV: <?php echo $content_npv; ?></li>
	</ul>
	<img alt="STROKE031.png" src="STROKE031.png" width="320" height="240"/>
	<img alt="STROKE032.png" src="STROKE032.png" width="320" height="240"/>
	<img alt="STROKE033.png" src="STROKE033.png" width="320" height="240"/>
	<img alt="STROKE034.png" src="STROKE034.png" width="320" height="240"/>
	<img alt="STROKE011.png" src="STROKE011.png" width="320" height="240"/>
	<img alt="STROKE011.png" src="STROKE013.png" width="320" height="240"/>
	<img alt="STROKE011.png" src="STROKE021.png" width="320" height="240"/>
	<img alt="STROKE011.png" src="STROKE023.png" width="320" height="240"/>
	<details>
	<summary>
		<span>Parameters</span>
	</summary>
<?php
if(isset($_POST['submit']))
{
	$filename = "./stroke.txt";
    $newData = $_POST['number']."\n".
	$_POST['path']."\n".
	$_POST['droplist']."\n".
	$_POST['balancing']."\n".
	$_POST['publish']."\n".
	$_POST['training']."\n".
	$_POST['preddraw']."\n".
	$_POST['costoferror']."\n".
	$_POST['costofchecking']."\n".
	$_POST['predfrac']."\n".
	$_POST['testdraws']."\n".
	$_POST['folds']."\n".
	$_POST['bagfrac']."\n".
	$_POST['weights']."\n".
	$_POST['weightsaccval']."\n".
	$_POST['weightsacctst'];
    file_put_contents($filename, $newData);
	echo "Input recorded";
}
?>

<form method="post" action="sample-index.php">
<input type="number" name="number" id="number" value="1">
<label for="number">- number of loop iterations</label>
<br><input type="path" name="path" id="path" value="&quot;C:/xampp/htdocs/OpenEMR-7.0.2/interface/modules/custom_modules/oe-module-jbpm-generic-callout/public&quot;">
<label for="path">- path to working directory</label>
<br><input type="droplist" name="droplist" id="droplist" value="&quot;['id','gender','ever_married','work_type','residence_type','smoking_status','stroke']&quot;">
<label for="droplist">- list of columns to ignore in models</label>
<br><input type="balancing" name="balancing" id="balancing" value="1">
<label for="balancing">- sample balancing ratio</label>
<br><input type="publish" name="publish" id="publish" value="1">
<label for="publish">- export modeling artefacts to files</label>
<br><input type="training" name="training" id="training" value="1">
<label for="training">- number of training runs</label>
<br><input type="preddraw" name="preddraw" id="preddraw" value="1">
<label for="preddraw">- number of testing runs</label>
<br><input type="costoferror" name="costoferror" id="costoferror" value="25.0">
<label for="costoferror">- average gain per detected target</label>
<br><input type="costofchecking" name="costofchecking" id="costofchecking" value="10.0">
<label for="costofchecking">- average loss per processed case</label>
<br><input type="predfrac" name="predfrac" id="predfrac" value="0.9">
<label for="predfrac">- sample training fraction</label>
<br><input type="testdraws" name="testdraws" id="testdraws" value="1">
<label for="testdraws">- training-coupled validation flag</label>
<br><input type="folds" name="folds" id="folds" value="5">
<label for="folds">- number of folds</label>
<br><input type="bagfrac" name="bagfrac" id="bagfrac" value="0.99">
<label for="bagfrac">- training sample bagging fraction</label>
<br><input type="weights" name="weights" id="weights" value="&quot&quot">
<label for="weights">- array of ensemble weight overrides</label>
<br><input type="weightsaccval" name="weightsaccval" id="weightsaccval" value="1">
<label for="weightsaccval">- training-based ensemble weights flag</label>
<br><input type="weightsacctst" name="weightsacctst" id="weightsacctst" value="0">
<label for="weightsacctst">- validation-based ensemble weights flag</label>
<br><input type="submit" name="submit" value="Submit">
</form>
</details>
</details>
</body>
</html>
