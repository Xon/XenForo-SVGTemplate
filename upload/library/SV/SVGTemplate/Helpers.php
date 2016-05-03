<?php

class SV_SVGTemplate_TemplateCracker extends XenForo_Template_Public
{
    public static function getStyleId()
    {
        return XenForo_Template_Public::$_styleId;
    }
    public static function getLanguageId()
    {
        return XenForo_Template_Abstract::$_languageId;
    }
}

class SV_SVGTemplate_Helpers
{
    static $useFriendlyUrls = null;
    static $boardUrl = null;

    public static function helperSvg($templateName, $style_id = null, $language_id = null)
    {
        if (empty($templateName))
        {
            throw new Exception('$templateName is required');
        }

        if ($language_id === null)
        {
            $language_id = SV_SVGTemplate_TemplateCracker::getLanguageId();
        }

        if ($style_id === null)
        {
            $style_id = SV_SVGTemplate_TemplateCracker::getStyleId();
        }

        $styles = XenForo_Application::get('styles');
        if ($style_id && isset($styles[$style_id]))
        {
            $style = $styles[$style_id];
        }
        else
        {
            $style = reset($styles);
        }
        if (empty($style))
        {
            return $templateName;
        }

        $parts = pathinfo($templateName);
        if (($parts['extension'] != 'svg' && $parts['extension'] != '') || ($parts['dirname'] != '' && $parts['dirname'] != '.'))
        {
            return $templateName;
        }
        $templateName = $parts['filename'];
        $templateName = urlencode($templateName);

        if (self::$useFriendlyUrls === null)
        {
            $xenOptions = XenForo_Application::getOptions();
            self::$useFriendlyUrls = $xenOptions->useFriendlyUrls;
            self::$boardUrl = $xenOptions->boardUrl;
        }
        if (self::$useFriendlyUrls)
        {
            $url = "/data/svg/{$style['style_id']}/{$language_id}/{$style['last_modified_date']}/{$templateName}.svg";
        }
        else
        {
            $url = "/svg.php?svg={$templateName}&style={$style['style_id']}&language={$language_id}&d={$style['last_modified_date']}";
        }

        return self::$boardUrl . $url;
    }
}