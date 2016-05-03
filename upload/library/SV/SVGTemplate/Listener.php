<?php

class styleProps extends XenForo_Template_Helper_Core
{
    public static function getStyleProperties()
    {
        return XenForo_Template_Helper_Core::$_styleProperties;
    }
}

class SV_SVGTemplate_Listener
{
    public static function injectStylePropertyBits()
    {
        $properties = styleProps::getStyleProperties();
        $changes = false;
        foreach($properties as $key => &$property)
        {
            if (is_array($property))
            {
                foreach($property as $key2 => &$component)
                {
                    $data = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array(__class__, '_handleSvgHelperReplacement'), $component);
                    if ($data != $component)
                    {
                        $changes = true;
                        $component = $data;
                    }
                }
            }
            else
            {
                $data = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array(__class__, '_handleSvgHelperReplacement'), $property);
                if ($data != $property)
                {
                    $changes = true;
                    $property = $data;
                }

            }
        }
        if($changes)
        {
            XenForo_Template_Helper_Core::setStyleProperties($properties);
        }
    }

    public static function _handleSvgHelperReplacement(array $match)
    {
         return SV_SVGTemplate_Helpers::helperSvg($match[1]);
    }

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