<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpCore;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Closure;

interface ResponseBuilderInterface
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function content($content = '', $status = 200, $headers = array());

    /**
     * @param $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, $headers = array());

    /**
     * @param callable $callback
     * @param int $status
     * @param array $headers
     * @return StreamedResponse
     */
    public function stream(Closure $callback, $status = 302, $headers = array());

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function json(array $data, $status = 302, $headers = array());

    /**
     * @param $file
     * @param int $status
     * @param array $headers
     * @param bool $public
     * @param $contentDisposition
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return BinaryFileResponse
     */
    public function download($file, $status = 302, $headers = array(), $public = true, $contentDisposition, $autoEtag = false, $autoLastModified = true);
}
