# XenForo-SVGTemplate

Depending on configuration, this add-on requires webserver URL rewrite support!

Allows SVG (Scalable Vector Graphics) images to be stored as templates.

This defines a svg.php file, and some options to support content redirection.

To include in a template:

{xen:helper svg, 'tempate.svg', {$visitorLanguage}, {$visitorStyle} }

Under Board information, if "Use Full Friendly URLs" (useFriendlyUrls) is set the URL generated is:
```
/data/svg/<style_id>/<langauge_id>/<langauge_direction>/<style_last_modified>/<templateName>.svg
```
Otherwise
```
svg.php?svg=<templateName>&style=<style_id>&language=<langauge_id>&dir=<langauge_direction>&d=<style_last_modified>
```

For nginx, something like:
```
location ^~ /data/svg/ {
   access_log off;
   rewrite ^/data/svg/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^\.]+).svg$ /svg.php?svg=$5&style=$1&language=$2&dir=$3&d=$4 last;
   return 403;
}
```