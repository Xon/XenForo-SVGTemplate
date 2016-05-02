<?php

class SV_SVGTemplate_Listener
{
    public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        XenForo_Template_Helper_Core::$helperCallbacks['svg'] = array('SV_SVGTemplate_Helpers', 'helperSvg');
    }

    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_SVGTemplate_'.$class;
    }
}