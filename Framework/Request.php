<?php

namespace Drengr\Framework;

class Request
{
    public const GETONLY = 'get';
    public const POSTONLY = 'post';
    public const GETANDPOST = 'both';

    protected $get;
    protected $cookie;
    protected $post;
    protected $env;
    protected $server;
    protected $content;

    /** @var Validator */
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Must be called after the class is constructed so that class variables
     * are properly initialized.
     *
     * @return $this
     */
    public function initialize()
    {
        $this->get = $_GET;
        $this->cookie = $_COOKIE;
        $this->post = $_POST;
        $this->env = $_ENV;
        $this->server = $_SERVER;

        if (empty($this->post)) {
            $this->post = $this->parseContent();
        }

        return $this;
    }

    /**
     * Return a value from the query string.
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }

    /**
     * Return all the values from the query string and the request body.
     * Results can be limited by passing one of the class constants in the
     * `$type` parameter.
     *
     * @param string $type
     * @return array
     */
    public function getAll($type = self::GETANDPOST)
    {
        switch ($type) {
            case self::GETONLY:
                return $this->get;
            case self::POSTONLY:
                return $this->post;
            case self::GETANDPOST:
            default:
                return array_merge($this->get, $this->post);
        }
    }

    /**
     * Return the parameters that control pagination.
     *
     * @return array
     */
    public function getPageParameters()
    {
        $page = isset($this->get['page']) ? $this->get['page'] : 0;
        $perPage = isset($this->get['perPage']) ? $this->get['perPage'] : 10;

        return compact('page', 'perPage');
    }

    /**
     * Validates the request parameters according to rules defined in `setRules`
     * and then returns the sanitized array of parameters. There must be a rule
     * defined for a parameter or it will not be included in the results.
     *
     * @return array
     */
    public function getValidatedParameters()
    {
        $this->setRules($this->validator);

        if ($this->validator->valid($this->getAll())) {
            return $this->validator->sanitize($this->getAll());
        }

        return [];
    }

    /**
     * Define the rules for validation of request parameters.
     *
     * @param Validator $validator
     */
    protected function setRules(Validator $validator)
    {
        // should be defined in child class
    }

    /**
     * Get the body of the request.
     *
     * @return false|string
     */
    public function getContent()
    {
        if (isset($this->content)) {
            return $this->content;
        }

        return $this->content = file_get_contents('php://input');
    }

    /**
     * Parse the body of the request according to the apparent Content_Type.
     *
     * @return array|mixed
     */
    protected function parseContent()
    {
        $data = [];
        if ($this->IsFormEncoded()) {
            parse_str($this->getContent(), $data);
        }

        if ($this->isJson()) {
            $data = json_decode($this->getContent(), true);
        }

        return $data;
    }

    /**
     * Return true if the body is form url-encoded according to the Content_Type header.
     *
     * @return bool
     */
    public function isFormEncoded()
    {
        return isset($this->server['CONTENT_TYPE'])
            && $this->server['CONTENT_TYPE'] === 'application/x-www-form-urlencoded';
    }

    /**
     * Return true if the body is JSON according to the Content_Type header.
     *
     * @return bool
     */
    public function isJson()
    {
        return isset($this->server['CONTENT_TYPE'])
            && $this->server['CONTENT_TYPE'] === 'application/json';
    }

    /**
     * Return the HTTP method used to make the request.
     *
     * @return string
     */
    public function getMethod()
    {
        return isset($this->server['REQUEST_METHOD'])
            ? strtolower($this->server['REQUEST_METHOD'])
            : 'get';
    }
}
