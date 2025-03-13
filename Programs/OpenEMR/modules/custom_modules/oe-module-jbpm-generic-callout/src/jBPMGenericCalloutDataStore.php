<?php

/**
 * Represents a very basic in memory data store used for illustrating the FHIR API
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2022 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\jBPMGenericCallout;

class jBPMGenericCalloutDataStore
{
    /**
     * Returns a custom skeleton record for the given id, or null if none found
     * @param $id
     * @return array|null
     */
    public function getById($id)
    {
        return $this->getByField('_id', $id)[0] ?? null;
    }

    /**
     * Returns all of the custom skeleton records that match the given patientId passed in.
     * @param $patientId
     * @return array
     */
    public function getByResponse($responseId)
    {
        return $this->getByField('_response_id', $responseId);
    }

    protected function getByField($field, $value)
    {
        $dataStore = $this->getResourceDataStore();
        $result = [];
        foreach ($dataStore as $record)
        {
            if ($record[$field] == $value)
            {
                $result[] = $record;
            }
        }
        return $result;
    }

    /**
     * Returns the entire data store of custom skeleton records.
     * @return array
     */
    public function getResourceDataStore()
    {
        $resources = [
            ['_id' => 1 ,'_message' => 'This is resource 1', '_response_id' => 1]
            ,['_id' => 2 ,'_message' => 'This is resource 2', '_response_id' => 2]
            ,['_id' => 3, '_message' => 'This is resource 3', '_response_id' => 3]
        ];
        return $resources;
    }
}
