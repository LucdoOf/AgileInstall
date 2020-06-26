<?php

namespace AgileAPI;

class AgileAPI {

    /** @var AgileAPI */
    private static $instance;

    var $uri;
    var $method;
    var $body;
    var $error;
    var $payload;
    var $response;
    var $httpStatus;
    var $headers;
    var $ip;
    var $authenticationToken;

    /**
     * AgileAPI constructor.
     */
    public function __construct() {
        $this->error = new ErrorHandler();
        $this->authenticationToken = $this->getAuthenticationToken();
    }

    /**
     * @return AgileAPI
     */
    public static function getInstance() {
        if(is_null(self::$instance)) self::$instance = new AgileAPI();
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        if(!$this->payload) {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $this->payload = json_decode($input, true);
            } else {
                $this->payload =  $_REQUEST;
            }
        }
        return $this->payload;
    }


    /**
     * @return bool
     */
    public function hasError() {
        return $this->error->getStatus() !== null && !in_array($this->error->getStatus(), [
                ErrorHandler::HTTP_FOUND,
                ErrorHandler::HTTP_CREATED,
            ]);
    }

    /**
     * Get header Authorization
     * */
    private function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
        }

        return $headers;
    }

    /**
     * Get access token from header "Bearer" or "API-Key"
     *
     * @param string $keyName
     * @return string|null
     */
    private function getAuthenticationToken($keyName = 'Basic') {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/' . $keyName . '\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }



}
