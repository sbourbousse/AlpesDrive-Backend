<?php

class Response {
    private $response ;

    public function __construct(){
        $this->response = [];
    }

    function setNew($status, $message){
        $this->response['new']['status'] = $status;
        $this->response['new']['message'] = $message;
    }

    function setAuth($status, $message){
        $this->response['auth']['status'] = $status;
        $this->response['auth']['message'] = $message;
    }

    function setDb($status, $message){
        $this->response['db']['status'] = $status;
        $this->response['db']['message'] = $message;
    }

    function setData($tab) {
        $this->response["data"] = $tab;
    }

    function setDelete($status, $message) {
        $this->response['delete']['status'] = $status;
        $this->response['delete']['message'] = $message;
    }

    function setUpdate($status, $message) {
        $this->response['update']['status'] = $status;
        $this->response['update']['message'] = $message;
    }
    function getResponse(){
        return $this->response;
    }

    function printResponseJSON() {
        echo json_encode($this->response);
    }
}
