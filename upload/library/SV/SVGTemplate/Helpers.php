<?php

class SV_SVGTemplate_Helpers
{
    public static function helperSvg($templateName, array $visitorLanguage, array $visitorStyle)
    {        
        if (empty($templateName))
        {
            throw new Exception('$templateName is required');
        }
        $templateName = urlencode($templateName);

        return "svg.php?svg={$templateName}&style={$visitorStyle['style_id']}&language={$visitorLanguage['language_id']}&dir={$visitorLanguage['text_direction']}&d={$visitorStyle['last_modified_date']}";
    }
}