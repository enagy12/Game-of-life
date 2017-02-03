<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class DataDeleteException
 */
class DataDeleteException extends \Exception implements UnavailableExceptionInterface {
    function __construct($message = 'data_delete_exception') {
        parent::__construct($message);
    }
}
