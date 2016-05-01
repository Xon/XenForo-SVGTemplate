<?php

class SV_SVGTemplate_Helpers
{
    static $useFriendlyUrls = null;
    static $boardUrl = null;

    public static function helperSvg($templateName, array $visitorLanguage, array $visitorStyle)
    {
        if (empty($templateName))
        {
            throw new Exception('$templateName is required');
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
            $url = "/data/svg/{$visitorStyle['style_id']}/{$visitorLanguage['language_id']}/{$visitorLanguage['text_direction']}/{$visitorStyle['last_modified_date']}/{$templateName}.svg";
        }
        else
        {
            $url = "/svg.php?svg={$templateName}&style={$visitorStyle['style_id']}&language={$visitorLanguage['language_id']}&dir={$visitorLanguage['text_direction']}&d={$visitorStyle['last_modified_date']}";
        }

        return self::$boardUrl . $url;
    }
}