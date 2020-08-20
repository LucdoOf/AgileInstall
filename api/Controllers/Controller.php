<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileAPI\ErrorHandler;
use AgileCore\Models\Administrator;
use AgileCore\Utils\Dbg;
use DateTime;
use Exception;

class Controller {

    protected $requireAuth = true;

    public function __construct() {
        if($this->requireAuth) {
            $this->checkAuthentication();
            if (!$this->isAuth() && AgileAPI::getInstance()->method !== "GET") {
                AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_FORBIDDEN, "Impossible d'effectuer cela en tant qu'invité");
            }
        }

        if (AgileAPI::getInstance()->hasError()) {
            throw new Exception(AgileAPI::getInstance()->error->getMessage(), AgileAPI::getInstance()->error->getStatus());
        }
    }

    public function checkAuthentication() {
        $admin = $this->getLoggedAdministrator();
        if(is_null($admin)) {
            if ((bool)getenv("ALLOW_GUESTS") !== true) {
                AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_UNAUTHORIZED, "Veuillez vous identifier");
                return false;
            }
        } else {
            $admin->last_seen = new DateTime();
            $admin->save();
        }
        return true;
    }

    public function getLoggedAdministrator() {
        if (!empty(AgileAPI::getInstance()->authenticationToken)) {
            $dataDecoded = base64_decode(AgileAPI::getInstance()->authenticationToken);
            if ($dataDecoded) {
                $dataArray = explode(":", $dataDecoded);
                if ($dataArray && count($dataArray) == 2) {
                    $user = $dataArray[0];
                    $password = $dataArray[1];
                    $administrator = Administrator::select(["name" => $user]);
                    if ($administrator->exist()) {
                        if ($password === $administrator->password) {
                            return $administrator;
                        }
                    }
                }
            }
        }
        return null;
    }

    public function isAuth() {
        return !is_null($this->getLoggedAdministrator());
    }

    public function error404($message){
        AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_NOT_FOUND, $message);
    }

    public function error403($message){
        AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_FORBIDDEN, $message);
    }

    public function error401($message){
        AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_UNAUTHORIZED, $message);
    }

    public function error400($message){
        AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_BAD_REQUEST, $message);
    }

    public function success($message, $data){
        $arr = $this->message($message);
        $arr["data"] = $data;
        return $arr;
    }

    public function message($msg){
        return ["message" => $msg];
    }

    public function getFilters(){
        $filters = [];
        if (isset(AgileAPI::getInstance()->getPayload()["filters"])) {
            foreach (is_array(AgileAPI::getInstance()->getPayload()["filters"]) ? AgileAPI::getInstance()->getPayload()["filters"] : json_decode(AgileAPI::getInstance()->getPayload()["filters"]) as $k => $v) {
                if (!is_null($v)) $filters[$k] = $v;
            }
        }
        return $filters;
    }

    public function getSortKey(){
        return isset(AgileAPI::getInstance()->getPayload()["sort"]) ? AgileAPI::getInstance()->getPayload()["sort"] : null;
    }

    public function payload($key = null, $default = null){
        if (is_null($key)) {
            return AgileAPI::getInstance()->getPayload();
        } else {
            return isset(AgileAPI::getInstance()->getPayload()[$key]) ? AgileAPI::getInstance()->getPayload()[$key] : $default;
        }
    }

}
