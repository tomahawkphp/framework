<?php

namespace Tomahawk\HttpCore;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Closure;

class ResponseBuilder implements ResponseBuilderInterface
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function content($content = '', $status = 200, $headers = array())
    {
        $response = new Response($content, $status, $headers);
        return $response;
    }

    /**
     * @param $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, $headers = array())
    {
        $response = new RedirectResponse($url, $status, $headers);
        return $response;
    }

    /**
     * @param callable $callback
     * @param int $status
     * @param array $headers
     * @return StreamedResponse
     */
    public function stream(Closure $callback, $status = 302, $headers = array())
    {
        $response = new StreamedResponse($callback, $status, $headers);
        return $response;
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function json(array $data, $status = 302, $headers = array())
    {
        $response = new JsonResponse($data, $status, $headers);
        return $response;
    }

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
    public function download($file, $status = 302, $headers = array(), $public = true, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
    {
        $response = new BinaryFileResponse($file, $status, $headers, $public, $contentDisposition, $autoEtag, $autoLastModified);
        return $response;
    }
}
