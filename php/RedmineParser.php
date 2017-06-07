<?php
/**
 * Created by PhpStorm.
 * User: marcel
 * Date: 06.06.17
 * Time: 14:51
 */

namespace php;


class RedmineParser {
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var string
     */
    private $serverUrl;

    /**
     * RedmineParser constructor.
     *
     * @param string $serverUrl
     * @param string $apiKey
     *
     * @internal param string $apiKey
     */
    public function __construct ($serverUrl, $apiKey) {
        $this->apiKey = $apiKey;
        $this->serverUrl = $serverUrl;
    }

    /**
     * @return mixed[]
     */
    public function retreiveTicketAsArray () {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->serverUrl."/issues/5887.json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-Redmine-API-Key:'.$this->apiKey, 'Expect:', 'Content-Type: application/json']);

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result,true);
    }
}