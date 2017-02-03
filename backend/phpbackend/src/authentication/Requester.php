<?php namespace hu\doxasoft\phpbackend\authentication;

use hu\doxasoft\phpbackend\exceptions\UnauthorizedException;

/**
 * Class Requester
 * @package hu\doxasoft\phpbackend\authentication
 *
 * @property JWTService jwtService
 */
class Requester {
    /* @var bool $logged */
    private $logged;
    private $user;

    /**
     * Requester constructor.
     * @param JWTService $jwtService
     * @param string $token
     */
    function __construct(JWTService &$jwtService, $token) {
        $this->jwtService = $jwtService;
        $this->parseToken($token);
    }

    /**
     * @return bool TRUE if the token was present and valid FALSE otherwise
     */
    public function isLogged() {
        return !!$this->logged;
    }

    /**
     * @param string $class A user class to construct
     * @return object
     * @throws UnauthorizedException
     */
    public function getUser($class = null) {
        if (!$this->logged) throw new UnauthorizedException();
        return class_exists($class)
            ? new $class($this->user)
            : $this->user;
    }

    /**
     * @param string $tokenString
     */
    private function parseToken($tokenString) {
        try {
            $token = $this->jwtService->parse($tokenString);
            $this->logged = $this->jwtService->tokenIsValid($token);
            $this->user = !$this->logged ? null : $token->getClaim('data');
        } catch (\Exception $e) {
            $this->logged = false;
            $this->user = null;
        }
    }
}
