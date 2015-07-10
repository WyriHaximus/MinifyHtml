MinifyHtml
==========

CakePHP 3, HTML Minify Plugin

### Installation ###

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require wyrihaximus/minify-html 
```

## Bootstrap ##

Add the following to your `config/bootstrap.php` to load the plugin.

```php
Plugin::load('WyriHaximus/MinifyHtml', ['bootstrap' => true]);
```

### Usage ###

After loading this plugin in your `bootstrap.php` the helper can be enabled in any controller by adding `WyriHaximus/MinifyHtml.MinifyHtml` to the helpers array like the example below:

```php
<?php

class AppController extends Controller
{
    public $helpers = [
        'WyriHaximus/MinifyHtml.MinifyHtml',
    ];
}
```

### Usage in other plugins ###

##### [dereuromark/cakephp-cache](https://github.com/dereuromark/cakephp-cache) #####

To use MinifyHtml instead of `dereuromark/cakephp-cache`'s own HTML minifier. Set the [`compress` configuration option](https://github.com/dereuromark/cakephp-cache#component-configuration) to: `\WyriHaximus\MinifyHtml\compress`

### Configuration ###

All configuration is namespaced, just as this plugin into `WyriHaximus.MinifyHtml`. The following options are available:

`debugOverride` (bool) Defaults to `false`. Everwrite debug and minify when debug it on. 
`factory` (string) Defaults to `WyriHaximus\HtmlCompress\Factory::constructFastest`. Speficy a parser factory, `constructFastest`, `construct`, and `constructSmallest` are build in.

### License ###

Copyright 2015 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
