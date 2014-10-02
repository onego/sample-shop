Sample Shop
===========

The purpose of this sample e-shop is to demonstrate OneGo JavaScript SDK and
PHP SDK usage in action.

No framework nor persistence was used intentionally to avoid any unnecessary
distractions. All logic is in `/src/SampleShop.php` and is fairly commented.
After following it you should have a pretty good understanding on how to
integrate your e-shop with OneGo.

[Live Demo!][demo]

Setup
-----

- Download source  and place it somewhere under your localhost DOCUMENT_ROOT.
- Rename `/src/config.php.dist` to `/src/config.php`.
- Copy & paste your OneGo API key and secret to `/src/config.php` from developer
  environment.

Note: View code uses PHP `<?=` tags. Since PHP 5.4 they are not part of short
tags and are always enabled. If your are using PHP version pre-5.4, you may have
to enable [short_open_tag][].


OneGo Developer Environment
---------------------------

- Go to <https://developers.onego.com/env/>.
- Enter your email.
- Choose type "I am eShop API user".
- Click "Create".
- You will receive an email with a link to your developer environment.


Licence
-------

The MIT License (MIT)
Copyright © 2013 OneGo Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software
and associated documentation files (the “Software”), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or
substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF
OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.



--
[demo]: https://developers.onego.com/sample-shop/ "Live Demo"
[short_open_tag]: http://php.net/manual/en/language.basic-syntax.phptags.php "PHP Tags"
