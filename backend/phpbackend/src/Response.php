<?php namespace hu\doxasoft\phpbackend;

/**
 * Class Response
 */
class Response {
    public static $CONTINUE = 100;
    public static $SWITCHING_PROTOCOLS = 101;
    public static $PROCESSING = 102;

    public static $OK = 200;
    public static $CREATED = 201;
    public static $ACCEPTED = 202;
    public static $NON_AUTHORITATIVE_INFORMATION = 203;
    public static $NO_CONTENT = 204;
    public static $RESET_CONTENT = 205;
    public static $PARTIAL_CONTENT = 206;
    public static $MULTI_STATUS = 207;
    public static $ALREADY_REPORTED = 208;
    public static $IM_USED = 226;

    public static $MULTIPLE_CHOICES = 300;
    public static $MOVED_PERMANENTLY = 301;
    public static $FOUND = 302;
    public static $SEE_OTHER = 303;
    public static $NOT_MODIFIED = 304;
    public static $USE_PROXY = 305;
    public static $SWITCH_PROXY = 306;
    public static $TEMPORARY_REDIRECT = 307;
    public static $PERMANENT_REDIRECT = 308;

    public static $BAD_REQUEST = 400;
    public static $UNAUTHORIZED = 401;
    public static $PAYMENT_REQUIRED = 402;
    public static $FORBIDDEN = 403;
    public static $NOT_FOUND = 404;
    public static $METHOD_NOT_ALLOWED = 405;
    public static $NOT_ACCEPTABLE = 406;
    public static $PROXY_AUTHENTICATION_REQUIRED = 407;
    public static $REQUEST_TIMEOUT = 408;
    public static $CONFLICT = 409;
    public static $GONE = 410;
    public static $LENGTH_REQUIRED = 411;
    public static $PRECONDITION_FAILED = 412;
    public static $PAYLOAD_TOO_LARGE = 413;
    public static $URI_TOO_LONG = 414;
    public static $UNSUPPORTED_MEDIA_TYPE = 415;
    public static $RANGE_NOT_SATISFIABLE = 416;
    public static $EXPECTATION_FAILED = 417;
    public static $I_M_A_TEAPOT = 418;
    public static $MISDIRECTED_REQUEST = 421;
    public static $UNPROCESSABLE_ENTITY = 422;
    public static $LOCKED = 423;
    public static $FAILED_DEPENDENCY = 424;
    public static $UPGRADE_REQUIRED = 426;
    public static $PRECONDITION_REQUIRED = 428;
    public static $TOO_MANY_REQUESTS = 429;
    public static $REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public static $UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    public static $INTERNAL_SERVER_ERROR = 500;
    public static $NOT_IMPLEMENTED = 501;
    public static $BAD_GATEWAY = 502;
    public static $SERVICE_UNAVAILABLE = 503;
    public static $GATEWAY_TIMEOUT = 504;
    public static $HTTP_VERSION_NOT_SUPPORTED = 505;
    public static $VARIANT_ALSO_NEGOTIATES = 506;
    public static $INSUFFICIENT_STORAGE = 507;
    public static $LOOP_DETECTED = 508;
    public static $NOT_EXTENDED = 510;
    public static $NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * Response constructor.
     */
    function __construct() {
        $this->setHeaders();
    }

    public function ok($data) {
        http_response_code(self::$OK);
        echo json_encode($data);
        exit();
    }

    public function okEmpty() {
        http_response_code(self::$OK);
    }

    public function unauthorized($message = "You are not authorized!") {
        $this->error(self::$UNAUTHORIZED, $message);
    }

    public function forbidden($message = "Forbidden!") {
        $this->error(self::$FORBIDDEN, $message);
    }

    public function badRequest($message = "Bad request!") {
        $this->error(self::$BAD_REQUEST, $message);
    }

    public function notFound($message = "Not a dedicated API url!") {
        $this->error(self::$NOT_FOUND, $message);
    }

    public function methodNotAllowed($message = "This method is not allowed for this path!") {
        $this->error(self::$METHOD_NOT_ALLOWED, $message);
    }

    public function unavailable($message = "Service not available!") {
        $this->error(self::$SERVICE_UNAVAILABLE, $message);
    }

    public function notImplemented($message = "Not implemented yet!") {
        $this->error(self::$NOT_IMPLEMENTED, $message);
    }

    public function error($code = 500, $message = "Unknown error!") {
        http_response_code($code);
        echo $message;
        exit();
    }

    private function setHeaders() {
        header('Access-Control-Allow-Origin: '.FRONTEND);
        header('Access-Control-Allow-Methods: OPTIONS, GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization");
        header('Content-Type: application/json; charset=utf-8');
    }
}
