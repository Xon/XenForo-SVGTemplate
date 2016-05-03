<?php

class SV_SVGTemplate_Listener
{
    public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        XenForo_Template_Helper_Core::$helperCallbacks['svg'] = array('SV_SVGTemplate_Helpers', 'helperSvg');
    }

    public static function front_controller_pre_view(XenForo_FrontController $fc, XenForo_ControllerResponse_Abstract &$controllerResponse, XenForo_ViewRenderer_Abstract &$viewRenderer, array &$containerParams)
    {
        if ($controllerResponse instanceof XenForo_ControllerResponse_View)
        {
            $viewRenderer = new SV_SVGTemplate_ViewRendererWrapper($viewRenderer);
        }
    }

    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_SVGTemplate_'.$class;
    }
}