<?php

namespace DieSchittigs;

class PooolClient{
    
    private $client;
    private $clientOpts = [
        'base_uri' => 'https://app.poool.cc/api/1/',
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]
    ];
    private $authFile;
    
    function __construct($email, $password, $authFile = null){
        $this->authFile = $authFile ? $authFile : sys_get_temp_dir() . '/.poool_auth.json';
        $this->client = new \GuzzleHttp\Client($this->clientOpts);
        if(!$this->loadAuth($email)){
            $this->login($email, $password);
        }
    }

    private function loadAuth($email){
        if(!is_file($this->authFile)) return false;
        $auth = json_decode(
            file_get_contents($this->authFile)
        );
        if(!$auth || !isset($auth->token) || !isset($auth->lifeTime)) return false;
        if($auth->email != $email) return false;
        if(time() - filemtime($this->authFile) > 120) return false;
        $this->clientOpts['headers']['P-Authorization'] = $auth->token;
        $this->clientOpts['headers']['P-Instance'] = $auth->activeInstanceId;
        return true;
    }

    private function login($email, $password){
        $res = $this->client->request('POST', 'authenticate', [
            'json' => compact('email', 'password')
        ]);
        file_put_contents($this->authFile, $res->getBody());
        $this->loadAuth($email);
    }

    public function get($url){
        $res = $this->client->request('GET', $url, $this->clientOpts);
        return $this->parseResult($res);
    }

    public function post($url, $payload){
        if(is_string($payload)) $payload = json_decode($payload);
        $opts = $this->clientOpts;
        $opts['json'] = $payload;
        $res = $this->client->request('POST', $url, $opts);
        return $this->parseResult($res);
    }

    private function parseResult($res){
        $result = (string) $res->getBody();
        if(substr($result, 0, 1) == '{' || substr($result, 0, 1) == '['){
            try{
                $result = json_decode($result);
            } catch (Exception $e){}
        }
        return $result;
    }

}
