<?php namespace hu\doxasoft\phpbackend\authentication;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class JWTService {
    const KEY = "EQ7e/P2kRUVS1JOf4qUW696BptbYp4FPU2dpj/7gjHN1MVkm951re+vuCjWf5yqJr40XBBfeLhbIArV1/2XyQg==";
    const TOKEN_ID = "Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=";

    /**
     * @param object $data
     * @return Token
     */
    public function getNewToken(&$data) {
        $now = time();
        $signer = new Sha256();
        $token = (new Builder())
            ->setIssuer(BACKEND) // Configures the issuer (iss claim)
            ->setAudience(FRONTEND) // Configures the audience (aud claim)
            ->setId(self::TOKEN_ID, true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt($now) // Configures the time that the token was issue (iat claim)
            ->setNotBefore($now) // Configures the time that the token can be used (nbf claim)
            ->setExpiration($now + 300) // Configures the expiration time of the token (nbf claim)
            ->set('data', $data) // Configures a new claim, called "uid"
            ->sign($signer, self::KEY)
            ->getToken();
        return $token;
    }

    /**
     * @param string $tokenString
     *
     * @return Token
     */
    public function parse($tokenString) {
        return (new Parser())->parse((string) $tokenString);
    }

    /**
     * @param string $tokenString JWT token string
     * @return bool TRUE if token is valid FALSE otherwise
     */
    public function jwtIsValid($tokenString) {
        return $this->tokenIsValid($this->parse($tokenString));
    }

    /**
     * @param Token $token JWT token
     * @return bool TRUE if token is valid FALSE otherwise
     */
    public function tokenIsValid(Token &$token) {
        $validator = new ValidationData();
        $validator->setIssuer(BACKEND);
        $validator->setAudience(FRONTEND);
        $validator->setId(self::TOKEN_ID);
        if (!$token->validate($validator)) return false;
        $signer = new Sha256();
        if (!$token->verify($signer, self::KEY)) return false;
        return true;
    }
}
