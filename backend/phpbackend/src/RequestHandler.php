<?php namespace hu\doxasoft\phpbackend;

use hu\doxasoft\phpbackend\authentication\Requester;
use hu\doxasoft\phpbackend\exceptions\MethodNotAllowedException;
use hu\doxasoft\phpbackend\exceptions\NoRequiredDataException;
use hu\doxasoft\phpbackend\exceptions\UnauthorizedException;

/**
 * Class RequestHandler
 * @author manuelfodor
 *
 * @property Requester $requester
 * @property Request $request
 */
abstract class RequestHandler {
    protected $requester;
    protected $request;

    function __construct(Requester &$requester, Request &$req) {
        $this->requester = $requester;
        $this->request = $req;
    }

    abstract function handleRequest();

    /**
     * Checks if the request has valid authorization header and throws an error if not.
     * @throws UnauthorizedException
     */
    protected function hasToBeAuthorized() {
        if (!$this->requester->isLogged()) {
            throw new UnauthorizedException();
        }
    }

    protected function hasToBeGet() {
        if (!$this->request->isGet()) {
            throw new MethodNotAllowedException();
        }
    }

    protected function hasToBePost() {
        if (!$this->request->isPost()) {
            throw new MethodNotAllowedException();
        }
    }

    protected function hasToHavePayload() {
        if (!$this->request->hasPayload()) {
            throw new NoRequiredDataException();
        }
    }

}
