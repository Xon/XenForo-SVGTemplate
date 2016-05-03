<?php

class SV_SVGTemplate_ViewRendererWrapper extends XenForo_ViewRenderer_Abstract
{
    protected $viewRender = null;

    public function __construct($viewRender)
    {
        $this->viewRender = $viewRender;
    }

    public function addJsonParams(array $params)
    {
        return $this->viewRender->addJsonParams($params);
    }

    public function renderView($viewName, array $params = array(), $templateName = '', XenForo_ControllerResponse_View $subView = null)
    {
        SV_SVGTemplate_StyleProperties::injectStylePropertyBits();
        return $this->viewRender->renderView($viewName, $params, $templateName, $subView);
    }

    public function renderError($errorText)
    {
        return $this->viewRender->renderError($errorText);
    }

    public function renderMessage($message)
    {
        return $this->viewRender->renderMessage($message);
    }

    public function renderContainer($contents, array $params = array())
    {
        return $this->viewRender->renderContainer($contents, $params);
    }

    public function renderRedirect($redirectType, $redirectTarget, $redirectMessage = null, array $redirectParams = array())
    {
        return $this->viewRender->renderRedirect($redirectType, $redirectTarget, $redirectMessage, $redirectParams);
    }

    public function replaceRequiredExternalPlaceholders(XenForo_Template_Abstract $template, $rendered)
    {
        return $this->viewRender->replaceRequiredExternalPlaceholders($template, $rendered);
    }

    public function renderUnrepresentable()
    {
        return $this->viewRender->renderUnrepresentable();
    }

    public function renderViewObject($class, $responseType, array &$params = array(), &$templateName = '')
    {
        return $this->viewRender->renderViewObject($class, $responseType, $params, $templateName);
    }

    public function renderSubView(XenForo_ControllerResponse_View $subView)
    {
        return $this->viewRender->renderSubView($subView);
    }

    public function createTemplateObject($templateName, array $params = array())
    {
        return $this->viewRender->createTemplateObject($templateName, $params);
    }

    public function getNeedsContainer()
    {
        return $this->viewRender->getNeedsContainer();
    }

    public function setNeedsContainer($required)
    {
        return $this->viewRender->setNeedsContainer($required);
    }

    public function getRequest()
    {
        return $this->viewRender->getRequest();
    }

    public function getDependencyHandler()
    {
        return $this->viewRender->getDependencyHandler();
    }
}