<?php

class SV_SVGTemplate_StyleProps extends XenForo_Template_Helper_Core
{
    public static function getStyleProperties()
    {
        return XenForo_Template_Helper_Core::$_styleProperties;
    }
}

class SV_SVGTemplate_StyleProperties
{
    static $changesDetected = true;
    public static function injectStylePropertyBits()
    {
        $properties = SV_SVGTemplate_StyleProps::getStyleProperties();
        $changes = true;
        foreach($properties as $key => &$property)
        {
            if (is_array($property))
            {
                foreach($property as $key2 => &$component)
                {
                    self::$changesDetected = false;
                    $data = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array(__class__, '_handleSvgHelperReplacement'), $component);
                    if ($data !== null && self::$changesDetected)
                    {
                        $changes = true;
                        $component = $data;
                    }
                }
            }
            else
            {
                self::$changesDetected = false;
                $data = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array(__class__, '_handleSvgHelperReplacement'), $property);
                if ($data !== null && self::$changesDetected)
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
        $output = SV_SVGTemplate_Helpers::helperSvg($match[1]);
        self::$changesDetected = ($output != $match[1]);
        return $output;
    }
}