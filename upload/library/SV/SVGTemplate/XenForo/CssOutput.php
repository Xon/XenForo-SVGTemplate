<?php

class SV_SVGTemplate_XenForo_CssOutput extends XFCP_SV_SVGTemplate_XenForo_CssOutput
{
    public function renderCss()
    {
        XenForo_CodeEvent::addListener('template_post_render', array(__class__, 'templateRender'));
        return parent::renderCss();
    }

    public static function _handleSvgHelperReplacement(array $match)
    {
         return SV_SVGTemplate_Helpers::helperSvg($match[1]);
    }

    public static function templateRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
    {
         $content = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array(__class__, '_handleSvgHelperReplacement'), $content);
    }
}