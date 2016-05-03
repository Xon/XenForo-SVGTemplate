<?php

class SV_SVGTemplate_Installer
{
    public static function install($existingAddOn, $addOnData)
    {
        $required = '5.5.0';
        $phpversion = phpversion();
        if (version_compare($phpversion, $required, '<'))
        {
            throw new XenForo_Exception("PHP {$required} or newer is required. {$phpversion} does not meet this requirement. Please ask your host to upgrade PHP", true);
        }
        if (XenForo_Application::$versionId < 1050370)
        {
            throw new XenForo_Exception("XenForo 1.5.3 or newer is required. {XenForo_Application::$version} does not meet this requirement.", true);
        }
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;

        XenForo_Application::defer('SV_SVGTemplate_Deferred_InstallHelper', array());

        return true;
    }
}
