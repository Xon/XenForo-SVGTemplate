<?php

/**
 * Class to output SVG data quickly for public facing pages. This class
 * is not designed to be used with the MVC structure; this allows us to
 * significantly reduce the amount of overhead in a request.
 *
 * This class is entirely self sufficient. It handles parsing the input,
 * getting the data, rendering it, and manipulating HTTP headers.
 */
class SV_SVGTemplate_SvgOutput
{
    /**
     * Style ID the SVG will be retrieved from.
     *
     * @var integer
     */
    protected $_styleId = 0;

    /**
     * Language ID the SVG will be retrieved from.
     *
     * @var integer
     */
    protected $_languageId = 0;

    /**
     * SVG template that has been requested. This will have ".svg" appended
     * to it and requested as a template.
     *
     * @var array
     */
    protected $_svgRequested = '';

    /**
     * The timestamp of the last modification, according to the input. (Used to compare
     * to If-Modified-Since header.)
     *
     * @var integer
     */
    protected $_inputModifiedDate = 0;

    /**
     * The direction in which text should be rendered. Either ltr or rtl.
     *
     * @var string
     */
    protected $_textDirection = 'LTR';

    /**
     * Date of the last modification to the style. Used to output Last-Modified header.
     *
     * @var integer
     */
    protected $_styleModifiedDate = 0;

    /**
     * Constructor.
     *
     * @param array $input Array of input. Style, language and SVG will be
     * pulled from this.
     */
    public function __construct(array $input)
    {
        $this->parseInput($input);
    }

    /**
     * Parses the style ID, language ID and the SVG out of the specified array
     * of input. The style ID will be found in "style", language ID will be
     * found in "language" and SVG in "svg".
     *
     * @param array $input
     */
    public function parseInput(array $input)
    {
        $this->_styleId = isset($input['style']) ? intval($input['style']) : 0;

        $this->_languageId = isset($input['language']) ? intval($input['language']) : 0;

        $this->_svgRequested = strval($input['svg']);

        if (!empty($input['d']))
        {
            $this->_inputModifiedDate = intval($input['d']);
        }
    }

    public function handleIfModifiedSinceHeader(array $server)
    {
        $outputSvg = true;
        if (isset($server['HTTP_IF_MODIFIED_SINCE']))
        {
            $modDate = strtotime($server['HTTP_IF_MODIFIED_SINCE']);
            if ($modDate !== false && $this->_inputModifiedDate <= $modDate)
            {
                header('Content-type: image/svg+xml; charset=utf-8', true, 304);
                $outputSvg = false;
            }
        }

        return $outputSvg;
    }

    /**
     * Does any preparations necessary for outputting to be done.
     */
    protected function _prepareForOutput()
    {
        $styles = XenForo_Application::get('styles');

        if ($this->_styleId && isset($styles[$this->_styleId]))
        {
            $style = $styles[$this->_styleId];
        }
        else
        {
            $style = reset($styles);
        }

        if ($style)
        {
            $properties = XenForo_Helper_Php::safeUnserialize($style['properties']);

            $this->_styleId = $style['style_id'];
            $this->_styleModifiedDate = $style['last_modified_date'];
        }
        else
        {
            $properties = array();

            $this->_styleId = 0;
        }

        $languages = XenForo_Application::get('languages');

        if ($this->_languageId && isset($languages[$this->_languageId]))
        {
            $language = $languages[$this->_languageId];
        }
        else
        {
            $language = reset($languages);
        }

        if ($language)
        {
            $this->_textDirection = $language['text_direction'];
            $this->_languageId = $language['language_id'];
        }
        else
        {
            $this->_textDirection = 'LTR';
            $this->_languageId = 0;
        }

        $defaultProperties = XenForo_Application::get('defaultStyleProperties');

        XenForo_Template_Helper_Core::setStyleProperties(XenForo_Application::mapMerge($defaultProperties, $properties), false);
        XenForo_Template_Public::setStyleId($this->_styleId);
        XenForo_Template_Abstract::setLanguageId($this->_languageId);
    }

    public function getCacheId()
    {
        return 'xfSvgCache_' . sha1(
            'style=' . $this->_styleId .
            'language=' . $this->_languageId .
            'svg=' . $this->_svgRequested .
            'd=' . $this->_inputModifiedDate .
            'dir=' . $this->_textDirection .
            (XenForo_Application::debugMode() ? 'debug' : '')
            );
    }

    /**
     * Renders the SVG and returns it.
     *
     * @return string
     */
    public function renderSvg()
    {
        $cacheId = $this->getCacheId();

        if ($cacheObject = XenForo_Application::getCache())
        {
            if ($cacheSvg = $cacheObject->load($cacheId, true))
            {
                $this->_styleModifiedDate = $this->_inputModifiedDate;
                return $cacheSvg;
            }
        }

        $this->_prepareForOutput();

        $params = array(
            'xenOptions' => XenForo_Application::get('options')->getOptions(),
            'dir' => $this->_textDirection,
            'pageIsRtl' => ($this->_textDirection == 'RTL')
        );

        $svgName = trim($this->_svgRequested);
        if (!$svgName)
        {
            return;
        }

        $templateName = $svgName . '.svg';
        $template = new XenForo_Template_Public($templateName, $params);

        $svg = self::renderSvgFromObject($template, XenForo_Application::debugMode());

        if ($cacheObject)
        {
            $cacheObject->save($svg, $cacheId, array(), 86400);
        }

        return $svg;
    }

    /**
     * Renders the SVG from a Template object.
     *
     * @param XenForo_Template_Abstract $template
     * @param boolean $withDebug If true, output debug CSS when invalid properties are accessed
     *
     * @return string
     */
    public static function renderSvgFromObject(XenForo_Template_Abstract $template, $withDebug = false)
    {
        $templateName = $template->getTemplateName();
        $errors = array();
        $output = '';

        ob_start();

        if ($withDebug)
        {
            XenForo_Template_Helper_Core::resetInvalidStylePropertyAccessList();
        }

        $output = $template->render();
        $output = self::translateSvgStyleProperties($output);

        if ($withDebug)
        {
            $propertyError = self::createDebugErrorString(
                XenForo_Template_Helper_Core::getInvalidStylePropertyAccessList()
            );
            if ($propertyError)
            {
                $errors["$templateName"] = $propertyError;
            }
        }

        $phpErrors = ob_get_clean();
        if ($phpErrors)
        {
            $errors["PHP"] = $phpErrors;
        }

        if ($withDebug && $errors)
        {
            // TODO
            //$output .= self::getDebugErrorsAsCss($errors);
        }

        return $output;
    }
    
    /**
     * Creates the SVG property access debug string from a list of invalid style
     * propery accesses.
     *
     * @param array $invalidPropertyAccess Format: [group] => true ..OR.. [group][value] => true
     *
     * @return string
     */
    public static function createDebugErrorString(array $invalidPropertyAccess)
    {
        if (!$invalidPropertyAccess)
        {
            return '';
        }

        $invalidPropertyErrors = array();
        foreach ($invalidPropertyAccess AS $invalidGroup => $value)
        {
            if ($value === true)
            {
                $invalidPropertyErrors[] = "group: $invalidGroup";
            }
            else
            {
                foreach ($value AS $invalidProperty => $subValue)
                {
                    $invalidPropertyErrors[] = "property: $invalidGroup.$invalidProperty";
                }
            }
        }

        if ($invalidPropertyErrors)
        {
            return "Invalid Property Access: " . implode(', ', $invalidPropertyErrors);
        }
        else
        {
            return '';
        }
    }

    public static function translateSvgStyleProperties($output)
    {
        $output = preg_replace_callback("/{xen\:helper\s+svg\s*\,\s*'([^']+)'\s*}/siUx", array('self', '_handleSvgHelperReplacement'), $output);

        return $output;
    }

    public static function _handleSvgHelperReplacement(array $match)
    {
         return SV_SVGTemplate_Helpers::helperSvg($match[1]);
    }

    /**
     * Outputs the specified SVG. Also outputs the necessary HTTP headers.
     *
     * @param string $svg
     */
    public function displaySvg($svg)
    {
        if (!$this->_styleModifiedDate)
        {
            $this->_styleModifiedDate = time();
        }

        header('Content-type: image/svg+xml; charset=utf-8');
        header('Expires: Wed, 01 Jan 2020 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->_styleModifiedDate) . ' GMT');
        header('Cache-Control: public');

        $extraHeaders = XenForo_Application::gzipContentIfSupported($svg);
        foreach ($extraHeaders AS $extraHeader)
        {
            header("$extraHeader[0]: $extraHeader[1]", $extraHeader[2]);
        }

        if (is_string($svg) && $svg && !ob_get_level() && XenForo_Application::get('config')->enableContentLength)
        {
            header('Content-Length: ' . strlen($svg));
        }

        echo $svg;
    }

    /**
     * Static helper to execute a full request for SVG output. This will
     * instantiate the object, pull the data from $_REQUEST, and then output
     * the SVG.
     */
    public static function run()
    {
        $dependencies = new XenForo_Dependencies_Public();
        $dependencies->preLoadData();

        $class = XenForo_Application::resolveDynamicClass(__CLASS__);

        $svgOutput = new $class($_REQUEST);
        if ($svgOutput->handleIfModifiedSinceHeader($_SERVER))
        {
            $svgOutput->displaySvg($svgOutput->renderSvg());
        }
    }
}