<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class DataEditException
 */
class DataEditException extends \Exception implements UnavailableExceptionInterface {
    function __construct($message = 'data_edit_exception') {
        parent::__construct($message);
    }
}
