<?php
/**
 * Created by PhpStorm.
 * User: mrshes
 * Date: 27.10.2017
 * Time: 18:52
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Log;

class Curl
{
    protected $url;
    protected $method;
    protected $data;
    protected $httpheader;
    protected $status;


    public function __construct($url = '#', $data = [], $httpheader = [])
    {
        $this->url = $url;
        $this->data = $data;
        $this->httpheader = $httpheader;
    }
    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return strtolower($this->method);
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $httpheader
     */
    public function setHttpheader($httpheader)
    {
        $this->httpheader = array_merge($this->httpheader, $httpheader);
    }

    /**
     * @return array
     */
    public function getHttpheader()
    {
        return $this->httpheader;
    }


    protected function createCURL()
    {
        $url = $this->url;
        $data = $this->data;
        $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init();
        $httpheader = $this->httpheader;


        if ($this->getMethod() == 'get') {
            $data = urldecode(http_build_query($data));
            $url .= "?$data";
        } elseif ($this->getMethod() == 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        elseif ($this->getMethod() == 'postJson') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl);


        if ($result === false) {
            Log::error('php_curl error', [curl_error($curl)]);
        }
        $this->setStatus(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        $response = json_decode($result, true);
        return $response;
    }

    public function get()
    {
        $this->setMethod('get');
        return $this->createCURL();
    }

    public function post()
    {
        $this->setMethod('post');
        return $this->createCURL();
    }

    public function postJson()
    {
        $this->setMethod('postJson');
        return $this->createCURL();
    }

}