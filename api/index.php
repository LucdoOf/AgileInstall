<?php

use AgileAPI\Controllers\ProductsController;
use AgileAPI\AgileAPI;
use AgileAPI\ErrorHandler;
use AgileCore\Utils\Dbg;

require '../src/boot.php';

$dispatcher = include "endpoints.php";

$api = AgileAPI::getInstance();

try {
    // Fetch method and URI from somewhere
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $httpStatus = null;
    $uri = $_SERVER['REQUEST_URI'];

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    if ($httpMethod == 'OPTIONS') {
        die();
    }

    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }

    $relativeDir = getenv('RELATIVE_DIR_API');

    $uri = rawurldecode($uri);
    $uri = $relativeDir !== false && !empty($relativeDir) ? str_replace($relativeDir, '', $uri) : $uri;


    $api->uri = $uri;
    $api->method = strtoupper($httpMethod);
    $api->body = $api->getPayload();

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $data = $api->error->responseError(ErrorHandler::HTTP_NOT_FOUND)->getResponse();
            $httpStatus = ErrorHandler::HTTP_NOT_FOUND;
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $data = $api->error->responseError(ErrorHandler::HTTP_METHOD_NOT_ALLOWED, 'Unauthorized HTTP method ' . $httpMethod)->getResponse();
            $httpStatus = ErrorHandler::HTTP_METHOD_NOT_ALLOWED;
            break;
        case FastRoute\Dispatcher::FOUND:
            // Call method from defined route
            [$class, $method] = explode(".", $routeInfo[1], 2);
            $data = call_user_func_array([new $class(), $method], $routeInfo[2]);
            break;
    }

} catch (Exception $e) {
    Dbg::error($e->getMessage());
    $data = $api->error ? $api->error->getResponse() : null;
} catch (Error $e) {
    Dbg::critical($e);
    $data = $api->error->responseError(ErrorHandler::HTTP_INTERNAL_SERVER_ERROR)->getResponse();
}

if($api->hasError()) {
    $api->error->setErrorHeader();
    $data = $api->error->getResponse();
}

if (!$httpStatus) {
    $httpStatus = http_response_code();
}

ob_start(function ($pBuffer) {
    return $pBuffer;
});


echo json_encode($data);

header('Content-Length: ' . ob_get_length());
ob_end_flush();
flush();

if (function_exists("fastcgi_finish_request")) {
    fastcgi_finish_request();
}

$api->response = json_encode($data);
$api->headers = json_encode(apache_request_headers());
$api->httpStatus = $httpStatus;
$api->ip = getIpAddress();
//$api->insertLog();
