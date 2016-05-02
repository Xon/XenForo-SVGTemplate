<?php

class SV_SVGTemplate_XenForo_CssOutput extends XFCP_SV_SVGTemplate_XenForo_CssOutput
{
    public function renderCss()
    {
        $output = parent::renderCss();
        $output = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array('self', '_handleSvgHelperReplacement'), $output);
        return $output;
    }

    public static function _handleSvgHelperReplacement(array $match)
    {
         return SV_SVGTemplate_Helpers::helperSvg($match[1]);
    }
}