<h2 align="center">Http Response Emitter</h2>
<h3 align="center">Emits a Response to the PHP Server API.</h3>

This is a fork of the abandoned project [narrowspark/http-emitter](https://github.com/narrowspark/http-emitter).

The available emitter implementations are.

    - `Kekos\HttpEmitter\SapiEmitter`
    - `Kekos\HttpEmitter\SapiStreamEmitter`.

> **Note:** each use the native PHP functions `header()` and ```echo``` to emit the response.

> **Note:** if headers have been sent, or the output buffer exists, and has a non-zero length, the emitters raise an exception, as mixed PSR-7 / output buffer content creates a blocking issue.
>
> If you are emitting content via `echo`, `print`, `var_dump`, etc., or not catching PHP errors / exceptions, you will need to either fix your app to always work with a PSR-7 response.
> Or provide your own emitters that allow mixed output mechanisms.

Installation
------------

```bash
composer require kekos/http-emitter
```

Use
------------

How to use the SapiEmitter:

```php
<?php

use Kekos\HttpEmitter\SapiEmitter;

$response = new \Response();
$response->getBody()->write("some content\n");

$emitter = new SapiEmitter();
$emitter->emit($response);
```

If you missing the ```Content-Length``` header you can use the `\Kekos\HttpEmitter\Util\Util::injectContentLength` static method.

```php
<?php

use Kekos\HttpEmitter\Util;

$response = new \Response();

$response = Util::injectContentLength($response);
```

## Versioning

This library follows semantic versioning, and additions to the code ruleset are performed in major releases.

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

If you would like to help take a look at the [list of issues](https://github.com/Kekos/http-emitter/issues).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).
