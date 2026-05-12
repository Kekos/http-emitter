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

use Kekos\HttpEmitter\Contract\RuntimeException;
use Laminas\Diactoros\Response;
use Kekos\HttpEmitter\SapiEmitter;
use Kekos\HttpEmitter\Tests\Helper\HeaderStack;
use Kekos\HttpEmitter\Util;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 *
 * @medium
 * @covers \Kekos\HttpEmitter\Util
 */
final class UtilTest extends TestCase
{
    private SapiEmitter $emitter;

    protected function setUp(): void
    {
        parent::setUp();

        HeaderStack::reset();

        HeaderStack::$headersSent = false;
        HeaderStack::$headersFile = null;
        HeaderStack::$headersLine = null;

        $this->emitter = new SapiEmitter();
    }

    public function testEmitsResponseHeaders(): void
    {
        $response = (new Response())
            ->withStatus(200)
            ->withAddedHeader('Content-Type', 'text/plain');
        $response->getBody()->write('Content!');

        \ob_start();

        $this->emitter->emit(Util::injectContentLength($response));

        if (false === \ob_end_clean()) {
            throw new RuntimeException('Failed to clear output buffer');
        }

        self::assertTrue(HeaderStack::has('HTTP/1.1 200 OK'));
        self::assertTrue(HeaderStack::has('Content-Type: text/plain'));
        self::assertTrue(HeaderStack::has('Content-Length: 8'));
    }

    public function testDoesNotInjectContentLengthHeaderIfStreamSizeIsUnknown(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('Content!');
        $stream->expects($this->any())
            ->method('getSize')
            ->willReturn(null);

        $response = (new Response())
            ->withStatus(200)
            ->withBody($stream);

        $response = Util::injectContentLength($response);

        \ob_start();

        $this->emitter->emit($response);

        if (false === \ob_end_clean()) {
            throw new RuntimeException('Failed to clear output buffer');
        }

        foreach (HeaderStack::stack() as $header) {
            self::assertStringNotContainsStringIgnoringCase('Content-Length:', (string) $header['header']);
        }
    }

    /**
     * @
     */
    public function testCloseOutputBuffersWithFlush(): void
    {
        $response = new Response();
        $response
            ->withStatus(200)
            ->withAddedHeader('Content-Type', 'text/plain');
        $response->getBody()->write('Content!');

        \ob_start();

        $this->emitter->emit($response);

        self::assertSame(2, \ob_get_level());
        // flush
        Util::closeOutputBuffers(1, true);

        self::assertSame(1, \ob_get_level());
    }

    public function testCloseOutputBuffersWithClean(): void
    {
        $response = new Response();
        $response
            ->withStatus(200)
            ->withAddedHeader('Content-Type', 'text/plain');
        $response->getBody()->write('Content!');

        \ob_start();

        $this->emitter->emit($response);

        $content = \ob_get_contents(); // 'Content!'

        // clear
        Util::closeOutputBuffers(1, false);

        self::assertNotSame(\ob_get_contents(), $content);
    }
}
