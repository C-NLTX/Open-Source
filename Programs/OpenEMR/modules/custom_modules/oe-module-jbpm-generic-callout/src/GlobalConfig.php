<?php

/**
 * Bootstrap custom module skeleton.  This file is an example custom module that can be used
 * to create modules that can be utilized inside the OpenEMR system.  It is NOT intended for
 * production and is intended to serve as the barebone requirements you need to get started
 * writing modules that can be installed and used in OpenEMR.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\jBPMGenericCallout;

use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Services\Globals\GlobalSetting;

class GlobalConfig
{
    public const CONFIG_OPTION_TEXT = 'oe_jbpm_generic_callout_config_option_text';
    public const CONFIG_OPTION_ENCRYPTED = 'oe_jbpm_generic_callout_config_option_encrypted';
    public const CONFIG_OVERRIDE_TEMPLATES = "oe_jbpm_generic_callout_override_twig_templates";
    public const CONFIG_ENABLE_MENU = "oe_jbpm_generic_callout_add_menu_button";
    public const CONFIG_ENABLE_BODY_FOOTER = "oe_jbpm_generic_callout_add_body_footer";
    public const CONFIG_ENABLE_FHIR_API = "oe_jbpm_generic_callout_enable_fhir_api";

    private $globalsArray;

    /**
     * @var CryptoGen
     */
    private $cryptoGen;

    public function __construct(array $globalsArray)
    {
        $this->globalsArray = $globalsArray;
        $this->cryptoGen = new CryptoGen();
    }

    /**
     * Returns true if all of the settings have been configured.  Otherwise it returns false.
     * @return bool
     */
    public function isConfigured()
    {
        $keys = [self::CONFIG_OPTION_TEXT, self::CONFIG_OPTION_ENCRYPTED];
        foreach ($keys as $key) {
            $value = $this->getGlobalSetting($key);
            if (empty($value)) {
                return false;
            }
        }
        return true;
    }

    public function getTextOption()
    {
        return $this->getGlobalSetting(self::CONFIG_OPTION_TEXT);
    }

    /**
     * Returns our decrypted value if we have one, or false if the value could not be decrypted or is empty.
     * @return bool|string
     */
    public function getEncryptedOption()
    {
        $encryptedValue = $this->getGlobalSetting(self::CONFIG_OPTION_ENCRYPTED);
        return $this->cryptoGen->decryptStandard($encryptedValue);
    }

    public function getGlobalSetting($settingKey)
    {
        return $this->globalsArray[$settingKey] ?? null;
    }

    public function getGlobalSettingSectionConfiguration()
    {
        $settings = [
            self::CONFIG_OPTION_TEXT => [
                'title' => 'jBPM Generic Callout Text Option'
                ,'description' => 'Example global config option with text'
                ,'type' => GlobalSetting::DATA_TYPE_TEXT
                ,'default' => ''
            ]
            ,self::CONFIG_OPTION_ENCRYPTED => [
                'title' => 'jBPM Generic Callout Encrypted Option (Encrypted)'
                ,'description' => 'Example of adding an encrypted global configuration value for your module.  Used for sensitive data'
                ,'type' => GlobalSetting::DATA_TYPE_ENCRYPTED
                ,'default' => ''
            ]
            ,self::CONFIG_OVERRIDE_TEMPLATES => [
                'title' => 'jBPM Generic Callout enable overriding twig files'
                ,'description' => 'Shows example of overriding a twig file'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_MENU => [
                'title' => 'jBPM Generic Callout add module menu item'
                ,'description' => 'Shows example of adding a menu item to the system (requires logging out and logging in again)'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_BODY_FOOTER => [
                'title' => 'jBPM Generic Callout Enable Body Footer example.'
                ,'description' => 'Shows example of adding a menu item to the system (requires logging out and logging in again)'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
            ,self::CONFIG_ENABLE_FHIR_API => [
                'title' => 'jBPM Generic Callout Enable FHIR API Extension example.'
                ,'description' => 'Shows example of extending the FHIR api with the skeleton module.'
                ,'type' => GlobalSetting::DATA_TYPE_BOOL
                ,'default' => ''
            ]
        ];
        return $settings;
    }
}
