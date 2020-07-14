<?php

namespace AgileAPI;

class ErrorHandler {

    private $status;
    private $message;

    const HTTP_FOUND = 200;
    const HTTP_CREATED = 201;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_REQUEST_LIMIT = 429;

    /**
     * @param int $status
     * @param string $message
     * @return ErrorHandler
     */
    public function responseError($status, $message = '') {
        $this->setStatus($status);
        $this->message = !empty($message) ? $message : self::defaultErrorMessage($status);

        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getResponse() {
        return [
            'message' => $this->message
        ];
    }

    public function setErrorHeader() {
        if (!headers_sent()) {
            http_response_code($this->status);
        }
    }

    /**
     * @param $status
     * @return string
     */
    public static function defaultErrorMessage($status) {

        $messages = [
            self::HTTP_BAD_REQUEST           => 'Bad request',
            self::HTTP_INTERNAL_SERVER_ERROR => 'Internal server error',
            self::HTTP_NOT_FOUND             => 'Not found',
            self::HTTP_UNAUTHORIZED          => 'Unauthorized',
            self::HTTP_FORBIDDEN             => 'Forbidden',
            self::HTTP_REQUEST_LIMIT         => 'Too many requests',
        ];

        return isset($messages[$status]) ? $messages[$status] : 'Unknown error';
    }

    public function __toString() {
        return (string)$this->message;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getStatus() {
        return $this->status;
    }
}
