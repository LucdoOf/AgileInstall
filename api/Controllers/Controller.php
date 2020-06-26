<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileAPI\ErrorHandler;
use AgileCore\Models\Administrator;
use AgileCore\Utils\Dbg;
use Exception;

class Controller {

    protected $requireAuth = true;

    public function __construct() {
        if($this->requireAuth) $this->checkAuthentication();

        if (AgileAPI::getInstance()->method == "POST" && empty(AgileAPI::getInstance()->getPayload()) && !AgileAPI::getInstance()->hasError()) {
            AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_BAD_REQUEST, "Invalid payload");
        }

        if (AgileAPI::getInstance()->hasError()) {
            throw new Exception(AgileAPI::getInstance()->error->getMessage(), AgileAPI::getInstance()->error->getStatus());
        }
    }

    public function checkAuthentication() {
        if (!empty(AgileAPI::getInstance()->authenticationToken)) {
            $dataDecoded = base64_decode(AgileAPI::getInstance()->authenticationToken);
            if($dataDecoded) {
                $dataArray = explode(":",$dataDecoded);
                if($dataArray && count($dataArray) == 2) {
                    $user = $dataArray[0];
                    $password = $dataArray[1];
                    $administrator = Administrator::select(["name" => $user]);
                    if($administrator->exist()){
                        if ($password === $administrator->password){
                            // Dbg::success("Administrator " . $user . " logged from ip " . getIpAddress());
                            return true;
                        }
                    }
                }
            }
        }
        AgileAPI::getInstance()->error->responseError(ErrorHandler::HTTP_UNAUTHORIZED, "Veuillez vous identifier");
        return false;
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
            foreach (AgileAPI::getInstance()->getPayload()["filters"] as $k => $v) {
                if (!is_null($v)) $filters[$k] = $v;
            }
        }
        return $filters;
    }

    public function getSortKey(){
        return AgileAPI::getInstance()->getPayload()["sort"];
    }

    public function payload(){
        return AgileAPI::getInstance()->getPayload();
    }

}
