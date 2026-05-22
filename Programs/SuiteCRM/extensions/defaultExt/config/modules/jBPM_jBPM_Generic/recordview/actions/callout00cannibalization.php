<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerBuilder $container): void {

    // 1. Retrieve the actions for jBPM callouts from the symfony container parameters
    $actions = $container->getParameter('module.recordview.actions') ?? [];
    $modules = $actions['modules'] ?? [];
    $jbpmcallouts = $modules['jBPM_jBPM_Generic'] ?? [];
    $recordActions = $jbpmcallouts['actions'] ?? [];


    // 2. Add the jBPM callout action definition
    $recordActions['callout-00-cannibalization'] = [
	  'actions' => [
	   'callout-00-cannibalization' => [
        'key' => 'callout-00-cannibalization',
        'labelKey' => 'LBL_CREATE_CALLOUT_00_CANNIBALIZATION', // New label, defined in public/legacy/custom/Extension/modules/jBPM_jBPM_Generic/Ext/Language
        'asyncProcess' => 'true',
        'modes' => ['detail', 'edit'],
        'acl' => ['view'],
        'aclModule' => 'jBPM_jBPM_Generic',
        'params' => [
            // Allow selecting a record from a modal before running action
            //'selectModal' => [
            //    'module' => 'Accounts'
            //]
            
            //MORE: Other available params

            // 'displayConfirmation' => true, // Enable to show a confirmation modal before running action
            // 'confirmationLabel' => 'NTC_DELETE_CONFIRMATION' // set the message to show on the confirmation modal

        ]
	   ]
	  ]
    ];

    // 3. Add back to the symfony container parameters
    $jbpmcallouts['actions'] = $recordActions;
    $modules['jBPM_jBPM_Generic'] = $jbpmcallouts;
    $actions['modules'] = $modules;
    $container->setParameter('module.recordview.actions', $actions);
};

