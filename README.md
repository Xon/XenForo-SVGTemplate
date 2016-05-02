# XenForo-SVGTemplate

Depending on configuration, this add-on requires webserver URL rewrite support!

Allows SVG (Scalable Vector Graphics) images to be stored as templates.

This defines a svg.php file, and some options to support content redirection.

To include in a template (or css templates INSIDE a style property):

{xen:helper svg, 'tempate.svg' }

Under Board information, if "Use Full Friendly URLs" (useFriendlyUrls) is set the URL generated is:
```
/data/svg/<style_id>/<langauge_id>/<style_last_modified>/<templateName>.svg
```
Otherwise
```
svg.php?svg=<templateName>&style=<style_id>&language=<langauge_id>&d=<style_last_modified>
```

For nginx, something like:
```
location ^~ /data/svg/ {
   access_log off;
   rewrite ^/data/svg/([^/]+)/([^/]+)/([^/]+)/([^\.]+).svg$ /svg.php?svg=$4&style=$1&language=$2&d=$3 last;
   return 403;
}
```