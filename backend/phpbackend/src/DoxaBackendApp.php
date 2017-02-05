<?php namespace hu\doxasoft\phpbackend;

use Exception;
use hu\doxasoft\phpbackend\authentication\JWTService;
use hu\doxasoft\phpbackend\authentication\Requester;
use hu\doxasoft\phpbackend\exceptions\BadRequestExceptionInterface;
use hu\doxasoft\phpbackend\exceptions\ClassNotFoundException;
use hu\doxasoft\phpbackend\exceptions\MethodNotAllowedException;
use hu\doxasoft\phpbackend\exceptions\PathAlreadyDefinedException;
use hu\doxasoft\phpbackend\exceptions\UnauthorizedException;
use hu\doxasoft\phpbackend\exceptions\UnavailableExceptionInterface;

/**
 * Class App
 *
 * @property Requester $requester
 * @property Request $request
 * @property Response $response
 * @property array $services
 */
class DoxaBackendApp {
    private $requester;
    private $request;
    private $response;
    private $services;
    private $daos;

    /**
     * DoxaBackendApp constructor.
     * @param DoxaBackendConfiguration $config
     *
     */
    public function __construct(DoxaBackendConfiguration $config) {
        $config->check();
        $this->request = new Request();
        $this->response = new Response();
        $this->requester = new Requester(new JWTService(), $this->request->getAuthorizationToken());
        $this->services = array();
        $this->daos = array();
    }

    /**
     * Runs the backend service app
     */
    public function run() {
        $base = $this->request->getNextRoute();
        if ($base !== BASE) {
            $this->response->notFound();
        }
        $start = $this->request->getNextRoute();
        if ($rs = $this->getRs($start)) {
            if ($this->request->isOptions()) {
                $this->response->okEmpty();
                return;
            }
            try {
                $this->response->ok($rs->handleRequest());

            } catch (UnauthorizedException $e) {
                $this->response->unauthorized();

            } catch (MethodNotAllowedException $e) {
                $this->response->methodNotAllowed();

            } catch (BadRequestExceptionInterface $e) {
                $this->response->badRequest($e->getMessage());

            } catch (UnavailableExceptionInterface $e) {
                $this->response->unavailable($e->getMessage());

            } catch (Exception $e) {
                $this->response->unavailable($e->getMessage());
            }
        } else {
            $this->response->badRequest();
        }
    }

    /**
     * @param string $path The base path of the RequestHandler
     * @param string $class The RequestHandler subclass name
     * @throws ClassNotFoundException Thrown if class not exists or not a subclass of RequestHandler
     * @throws PathAlreadyDefinedException Thrown if path is not defined or not a string or it has been already added to services
     */
    public function addService($path, $class, $dao) {
        if (!class_exists($class) || is_a($class, RequestHandler::class)) {
            throw new ClassNotFoundException();
        }
        if (!is_string($path) || isset($this->services[$path])) {
            throw new PathAlreadyDefinedException('Path has to be a unique <b>string</b>!');
        }
        $this->services[ $path ] = $class;
        $this->daos[ $path ] = $dao;
    }

    /**
     * @param string $start
     * @return RequestHandler if there is no RequestHandler registered for path it returns NULL
     */
    private function getRs($start) {
        return isset($this->services[$start])
            ? new $this->services[$start]($this->requester, $this->request, new $this->daos[$start]())
            : null;
    }
}
