<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017-2021 Daniel Bannert
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/Kekos/http-emitter
 */

namespace Kekos\HttpEmitter\Tests;

/*
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

use Kekos\HttpEmitter\Contract\RuntimeException;
use Laminas\Diactoros\Response;
use Kekos\HttpEmitter\SapiEmitter;
use Kekos\HttpEmitter\Tests\Helper\HeaderStack;
use Psr\Http\Message\StreamInterface;

use function ob_end_clean;
use function ob_start;

/**
 * @internal
 *
 * @medium
 * @covers \Kekos\HttpEmitter\SapiEmitter
 */
final class SapiEmitterTest extends AbstractEmitterTestCase
{
    protected function setUp(): void
    {
        HeaderStack::reset();

        HeaderStack::$headersSent = false;
        HeaderStack::$headersFile = null;
        HeaderStack::$headersLine = null;

        $this->emitter = new SapiEmitter();
    }

    public function testDoesNotInjectContentLengthHeaderIfStreamSizeIsUnknown(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->method('__toString')
            ->willReturn('Content!');
        $stream
            ->method('getSize')
            ->willReturn(null);

        $response = (new Response())
            ->withStatus(200)
            ->withBody($stream);

        ob_start();

        $this->emitter->emit($response);

        if (false === ob_end_clean()) {
            throw new RuntimeException('Failed to clear output buffer');
        }

        foreach (HeaderStack::stack() as $header) {
            self::assertStringNotContainsStringIgnoringCase('Content-Length:', (string) $header['header']);
        }
    }
}
