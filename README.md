# XenForo-SVGTemplate

Allows SVG (Scalable Vector Graphics) images to be stored as templates.

This defines a svg.php file, and some options to support content redirection.

To include in a template:

{xen:helper svg, 'tempate.svg' }
{xen:helper svg, 'tempate.svg', {$_styleId}, {$visitorLanguage.language_id}, {$visitorLanguage.text_direction}, {$visitorStyle.last_modified_date} }