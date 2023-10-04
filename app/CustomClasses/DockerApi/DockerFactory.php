<?php

namespace App\CustomClasses\DockerApi;

class DockerFactory
{
    /** @param resource */
    private $curlClient;

    /** @param string */
    private $socketPath;

    /** @param string|null */
    private $curlError = null;

    /** @param string|null */
    private $httpResponseCode = null;

    /** @param string|null */
    private $message = null;

    /** @param string|null */
    private $Id = null;

    /** @param array|null */
    private $Warnings = null;

    private function generateRequestUri(string $requestPath)
    {
        /* Please note that Curl doesn't use http+unix:// or any other mechanism for
         *  specifying Unix Sockets; once the CURLOPT_UNIX_SOCKET_PATH option is set,
         *  Curl will simply ignore the domain of the request. Hence why this works,
         *  despite looking as though it should attempt to connect to a host found at
         *  the domain "unixsocket". See L14 where this is set.
         *
         *  @see Client.php:L14
         *  @see https://github.com/curl/curl/issues/1338
         */
        return sprintf("http://unixsocket%s", $requestPath);
    }


    /**
     * Dispatches a command - via Curl - to Commander's Unix Socket.
     *
     * @param  string Docker Engine socketPath to hit.
     * @param  string Docker Engine requestType.
     * @param  string Docker Engine endpoint to hit.
     * @param  string  queryParameters to post to $endpoint.
     * @param array  bodyParameters to post to $endpoint.
     * @return array  The response from the Docker Engine.
     */
    public function dispatchCommand(
        string $socketPath,
        string $requestType = 'GET',
        string $endpoint,
        string $queryParameters = null,
        array $bodyParameters = null
    ): array {

        $this->curlClient = curl_init();
        $this->socketPath = $socketPath;
        $this->requestType = $requestType;

        if (is_null($bodyParameters)) {
            $bodyParameters = array();
        }

        curl_setopt($this->curlClient, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($this->curlClient, CURLOPT_UNIX_SOCKET_PATH, $socketPath);
        curl_setopt($this->curlClient, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlClient, CURLOPT_URL, $this->generateRequestUri($endpoint) . '?' . $queryParameters);

        if ($this->requestType == 'POST') {
            curl_setopt($this->curlClient, CURLOPT_POST, true);
            curl_setopt($this->curlClient, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($this->curlClient, CURLOPT_POSTFIELDS, json_encode($bodyParameters));
        }

        $result = curl_exec($this->curlClient);

        $this->httpResponseCode = curl_getinfo($this->curlClient)['http_code'];

        // If Curl returns FALSE, then there was a curl error. Other responsed like 404 are not considered errors.
        if ($result === FALSE) {
            $this->curlError = curl_error($this->curlClient);
            $result = '';
        }

        curl_close($this->curlClient);

        $array_result = json_decode($result, true);
        if (is_null($array_result)) {
            return array();
        } else {
            return $array_result;
        }
    }

    /**
     * create: 
     *  API requests.
     *
     * @param string
     */
    public static function create(
        string $containerName,
        string $containerImage,
        string $cmd = null,
        bool $autoRemove = false,
        array $binds = null,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'POST',
    ) {
        $obj = new static();

        $queryParameters = array(
            'name' => $containerName
        );

        $bodyParameters = array(
            'Image' => $containerImage,
            'Cmd' => array(
                '/bin/sh',
                '-c',
                $cmd
            ),

            'HostConfig' => array(
                'Binds' => $binds,
                'AutoRemove' => $autoRemove
            )
        );

        $resposne = $obj->dispatchCommand(
            $socketPath,
            $requestType,
            '/containers/create',
            http_build_query($queryParameters),
            $bodyParameters
        );

        if (isset($resposne['message'])) {
            $obj->message = $resposne['message'];
        }

        if (isset($resposne['Id'])) {
            $obj->Id = $resposne['Id'];
            if (!empty($resposne['Warnings'])) {
                $obj->Warnings = $resposne['Warnings'];
            }
        }

        return $obj;
    }

    /**
     * kill: 
     *  API requests.
     *
     * @param string
     */
    public static function kill(
        string $id = null,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'POST'
    ) {
        $obj = new static();
        $obj->setId($id);

        if (!is_null($obj->Id)) {
            $resposne = $obj->dispatchCommand(
                $socketPath,
                $requestType,
                '/containers/' . $obj->Id . '/kill'
            );

            if (isset($resposne['message'])) {
                $obj->message = $resposne['message'];
            }
        }

        return $obj;
    }

    /**
     * start: 
     *  API requests.
     *
     * @param string
     */
    public static function start(
        string $id = null,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'POST'
    ) {
        $obj = new static();
        $obj->setId($id);

        if (!is_null($obj->Id)) {
            $resposne = $obj->dispatchCommand(
                $socketPath,
                $requestType,
                '/containers/' . $obj->Id . '/start'
            );

            if (isset($resposne['message'])) {
                $obj->message = $resposne['message'];
            }
        }

        return $obj;
    }

    /**
     * pause: 
     *  API requests.
     *
     * @param string
     */
    public static function pause(
        string $id = null,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'POST'
    ) {
        $obj = new static();
        $obj->setId($id);

        if (!is_null($obj->Id)) {
            $resposne = $obj->dispatchCommand(
                $socketPath,
                $requestType,
                '/containers/' . $obj->Id . '/pause'
            );

            if (isset($resposne['message'])) {
                $obj->message = $resposne['message'];
            }
        }

        return $obj;
    }

    /**
     * unpause: 
     *  API requests.
     *
     * @param string
     */
    public static function unpause(
        string $id = null,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'POST'
    ) {
        $obj = new static();
        $obj->setId($id);

        if (!is_null($obj->Id)) {
            $resposne = $obj->dispatchCommand(
                $socketPath,
                $requestType,
                '/containers/' . $obj->Id . '/unpause'
            );

            if (isset($resposne['message'])) {
                $obj->message = $resposne['message'];
            }
        }

        return $obj;
    }

    /**
     * remove: 
     *  API requests.
     *
     * @param string
     */
    public static function remove(
        string $id = null,
        bool $force = false,
        bool $v = false,
        string $socketPath = '/var/run/docker.sock',
        string $requestType = 'DELETE'
    ) {
        $obj = new static();
        $obj->setId($id);

        if (!is_null($obj->Id)) {

            $queryParameters = array(
                'v' => $force,
                'force' => $v
            );

            $resposne = $obj->dispatchCommand(
                $socketPath,
                $requestType,
                '/containers/' . $obj->Id,
                http_build_query($queryParameters),
            );

            if (isset($resposne['message'])) {
                $obj->message = $resposne['message'];
            }
        }

        return $obj;
    }

    /**
     * Returns a human readable string from Curl in the event of an error.
     *
     * @return bool|string 
     */
    public function getCurlError()
    {
        return is_null($this->curlError) ? false : $this->curlError;
    }

    /**
     * Returns the http response code from Curl.
     *
     * @return bool|string 
     */
    public function getHttpResponseCode()
    {
        return is_null($this->httpResponseCode) ? false : $this->httpResponseCode;
    }

    /**
     * Returns the message from docker API create command.
     *
     * @return bool|string 
     */
    public function getMessage()
    {
        return is_null($this->message) ? false : $this->message;
    }

    /**
     * Returns the Id from docker API create command, in a successful event.
     *
     * @return bool|string 
     */
    public function getId()
    {
        return is_null($this->Id) ? false : $this->Id;
    }

    /**
     * Sets the Id.
     *
     * @return bool|string 
     */
    public function setId(string $id)
    {
        $this->Id = $id;
    }

    /**
     * Returns the Warnings from docker API create command, in a successful event.
     *
     * @return bool|string 
     */
    public function getWarnings()
    {
        return is_null($this->Warnings) ? false : $this->Warnings;
    }
}
