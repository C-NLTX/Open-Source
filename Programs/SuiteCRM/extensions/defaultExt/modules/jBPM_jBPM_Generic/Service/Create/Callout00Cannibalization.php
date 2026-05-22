<?php

namespace App\Extension\defaultExt\modules\jBPM_jBPM_Generic\Service\Create;

use App\Process\Entity\Process;
use App\Process\Service\ProcessHandlerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use App\Engine\LegacyHandler\LegacyHandler;

class Callout00Cannibalization extends LegacyHandler implements ProcessHandlerInterface
{
    protected const MSG_OPTIONS_NOT_FOUND = 'Process options are not defined';
    protected const MSG_INVALID_MODULE = 'Invalid Module';
    public const PROCESS_TYPE = 'callout-00-cannibalization';

    /**
     * @inheritDoc
     */
    public function getProcessType(): string
    {
        return self::PROCESS_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function getHandlerKey(): string
    {
        return self::PROCESS_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function requiredAuthRole(): string
    {
        return 'ROLE_USER';
    }

    /**
     * @inheritDoc
     */
    public function getRequiredACLs(Process $process): array
    {
        $options = $process->getOptions();
        $module = $options['module'] ?? '';

        return [
            $module => [
                [
                    'action' => 'edit',
                ]
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function configure(Process $process): void
    {
        //This process is synchronous
        //We aren't going to store a record on db
        //thus we will use process type as the id
        $process->setId(self::PROCESS_TYPE);
        $process->setAsync(false);
    }

    /**
     * @inheritDoc
     */
    public function validate(Process $process): void
    {
        if (empty($process->getOptions())) {
            throw new InvalidArgumentException(self::MSG_OPTIONS_NOT_FOUND);
        }

        $options = $process->getOptions();

        if (empty($options['module']) || $options['module'] !== 'jBPM_jBPM_Generic') {
            throw new InvalidArgumentException(self::MSG_INVALID_MODULE);
        }
    }

    /**
     * @inheritDoc
     */
    public function run(Process $process)
    {
        $this->init();
		
		global $current_user;
		
		$existingCallouts = \BeanFactory::getBean('jBPM_jBPM_Generic');
		$selectedCallouts = $existingCallouts->get_list('date_entered DESC', "assigned_user_id='$current_user->id'", 0, 1, 1, 0);
		//$selectedCallouts = $existingCallouts->get_list('date_entered DESC', "", 0, 1, 1, 0);
		$filename = "./cannibalization.".$current_user->user_name.".txt";

		foreach($selectedCallouts["list"] as $item)
		{
			$newData = "start"."\n".
			$newData = $current_user->id."\n".
			$newData = $current_user->user_name."\n".
			$newData = $selectedCallouts["row_count"]."\n".
			$newData = $selectedCallouts["current_offset"]."\n".
			$newData = $item->id."\n".
			$newData = $item->name."\n".
			$newData = $item->date_entered."\n".
			$newData = $item->date_modified."\n".
			$newData = $item->modified_user_id."\n".
			$newData = $item->created_by."\n".
			$newData = $item->description."\n".
			$newData = $item->deleted."\n".
			$newData = $item->assigned_user_id."\n".
			$newData = $item->response_status."\n".
			$newData = $item->response_id."\n".
			$newData = "end";
			file_put_contents($filename, $newData);
		}
		
		if(1 == 1)
		{
		
			foreach($selectedCallouts["list"] as $item)
			{
				$input_dimension = $item->input_dimension_c;
				$input_iterations = $item->input_iterations_c;
				$input_products = $item->input_products_c;
				$input_products_clean = str_replace('^,^', '*', $input_products);
				$input_products_clean = str_replace('^', '"', $input_products_clean);
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
			curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/kie-server/services/rest/server/containers/project_1.0.0-SNAPSHOT/processes/cannibalization/instances");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "content-type: application/json"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \"iterations\": { \"com.discovery.project.Iterations\": { \"number\": 7 } }}");
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{ \"dimension\": { \"com.discovery.project.Dimension\": { \"size\": {$input_dimension} } }, ".
			                                       "\"iterations\": { \"com.discovery.project.Iterations\": { \"number\": {$input_iterations} } }, ".
												   "\"products\": { \"com.discovery.project.Products\": { \"cannibals\": {$input_products_clean} } } }");
			$result = curl_exec ($ch);
			$response = json_decode($result);
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, "wbadmin:wbadmin");
			curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/kie-server/services/rest/server/queries/processes/instances/".$response."/variables/instances");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "content-type: application/json"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$cannibalization_result = curl_exec ($ch);
			$content = json_decode($cannibalization_result, true);
			$cannibalization_corrmat=$content['variable-instance'][7]['value'];
			curl_close($ch);

        	$options = $process->getOptions();

        	/** @var \aCase $newCase */
        	$newCallout = \BeanFactory::newBean('jBPM_jBPM_Generic');
        	$newCallout->name = 'Callout 00 Cannibalization';
			$newCallout->response_id = $response;
        	$newCallout->response_status = $status_code;
			$newCallout->input_dimension_c = $input_dimension;
			$newCallout->input_iterations_c = $input_iterations;
			$newCallout->input_products_c = $input_products;
			$newCallout->output_corrmat_c = $cannibalization_corrmat;
			$newCallout->save();
			
			//$selectedCallouts->response_id = $response;
        	//$selectedCallouts->response_status = $status_code;
			//$selectedCallouts->input_dimension_c = $input_dimension;
			//$selectedCallouts->input_iterations_c = $input_iterations;
			
			//$selectedCallouts->mark_deleted($id);
			//$selectedCallouts->save();
		
		}
		
		//called module
        //$module = 'jBPM_jBPM_Generic';
        //$action = 'edit';
        //$recordId = $selectedCallouts->id;
		
		//$responseData = [
        //    'handler' => 'redirect',
        //    'params' => [
        //        'route' => $module . '/'. $action. '/' . $recordId,
        //        'queryParams' => []
        //    ]
        //];

        $process->setStatus('success');
        $process->setMessages([]);
		$process->setData([]);
        //$process->setData($responseData);

        $this->close();
    }
}