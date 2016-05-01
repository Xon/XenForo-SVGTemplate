<?php

class SV_SVGTemplate_Listener
{
    public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        XenForo_Template_Helper_Core::$helperCallbacks['svg'] = array('SV_SVGTemplate_Helpers', 'helperSvg');
    }
}