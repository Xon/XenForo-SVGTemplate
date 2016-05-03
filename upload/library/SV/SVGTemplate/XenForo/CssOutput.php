<?php

class SV_SVGTemplate_XenForo_CssOutput extends XFCP_SV_SVGTemplate_XenForo_CssOutput
{
    protected function _prepareForOutput()
    {
        parent::_prepareForOutput();
        SV_SVGTemplate_StyleProperties::injectStylePropertyBits();
    }
}