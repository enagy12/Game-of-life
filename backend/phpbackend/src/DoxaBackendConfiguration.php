<?php

namespace hu\doxasoft\phpbackend;

use hu\doxasoft\phpbackend\exceptions\NoRequiredDataException;

/**
 * Class DoxaBackendConfiguration
 * @package hu\doxasoft\phpbackend
 */
class DoxaBackendConfiguration {
    const DEFAULT_APP_BASE = 'api';

    private $checksum = 0;
    private $locked = false;

    function __construct(
        $frontend = null,
        $backend = null,
        $base = null,
        $db_host = null,
        $db_name = null,
        $db_user = null,
        $db_pass = null
    ) {
        if ($frontend !== null) {
            $this->frontend($frontend);
        }
        if ($backend !== null) {
            $this->backend($backend);
        }
        if ($base !== null) {
            $this->base($base);
        }
        if ($db_host !== null) {
            $this->db_host($db_host);
        }
        if ($db_name !== null) {
            $this->db_name($db_name);
        }
        if ($db_user !== null) {
            $this->db_user($db_user);
        }
        if ($db_pass !== null) {
            $this->db_pass($db_pass);
        }
    }

    public function check() {
        if ($this->locked && ($this->checksum === 200 || $this->checksum === 204)) {
            return true;
        }
        throw new NoRequiredDataException('REQUIRED CONFIGURATIONS ARE MISSING!!!');
    }

    public function ready() {
        if (!defined('BASE')) {
            $this->base(self::DEFAULT_APP_BASE);
        }
        $this->locked = true;
        return $this;
    }

    public function frontend($frontend) {
        if (!defined('FRONTEND') && is_string($frontend)) {
            define('FRONTEND', $frontend);
            $this->checksum += 100;
        }
        return $this;
    }

    public function backend($backend) {
        if (!defined('BACKEND') && is_string($backend)) {
            define('BACKEND', $backend);
            $this->checksum += 100;
        }
        return $this;
    }

    public function base($base) {
        if (!defined('BASE') && is_string($base) && !empty($base)) {
            define('BASE', $base);
        }
        return $this;
    }

    public function db_host($db_host) {
        if (!defined('DB_HOST') && is_string($db_host)) {
            define('DB_HOST', $db_host);
            $this->checksum += 1;
        }
        return $this;
    }

    public function db_name($db_name) {
        if (!defined('DB_NAME') && is_string($db_name)) {
            define('DB_NAME', $db_name);
            $this->checksum += 1;
        }
        return $this;
    }

    public function db_user($db_user) {
        if (!defined('DB_USER') && is_string($db_user)) {
            define('DB_USER', $db_user);
            $this->checksum += 1;
        }
        return $this;
    }

    public function db_pass($db_pass) {
        if (!defined('DB_PASS') && is_string($db_pass)) {
            define('DB_PASS', $db_pass);
            $this->checksum += 1;
        }
        return $this;
    }
}
