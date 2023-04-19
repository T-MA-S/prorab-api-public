<?php


namespace app\components\helpers;


use CoderCat\JWKToPEM\JWKConverter;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use linslin\yii2\curl;

class JwkHelper
{
    public static function getRequest($phone)
    {
        $clientId = \Yii::$app->params['mtsClientId'];
        $signer = new Sha256();
        $privateKey = InMemory::file(\Yii::getAlias('@app') . '/sig-private.pem');

        $token = ( new Builder(new JoseEncoder(), ChainedFormatter::default()))->issuedBy($clientId)
            ->withHeader( 'kid' , 'rsa1' )
            ->permittedFor('https://idgw.mobileid.mts.ru')
            ->withClaim( 'response_type' , 'mc_si_async_code')
            ->withClaim( 'client_id' , $clientId )
            ->withClaim( 'scope' , 'openid mc_authn' )
            ->withClaim( 'version' , 'mc_si_r2_v1.0' )
            ->withClaim( 'nonce' , \Ramsey\Uuid\Uuid::uuid4()->toString())
            ->withClaim( 'login_hint' , 'ENCR_MSISDN:RlwdG...VkIE1ETg' )
            ->withClaim( 'acr_values' , '3 2' )
            ->withClaim( 'correlation_id' , \Ramsey\Uuid\Uuid::uuid4()->toString())
            ->withClaim( 'client_notification_token' ,
                \Ramsey\Uuid\Uuid::uuid4()->toString())
            ->withClaim( 'notification_uri' , 'https://' . $_SERVER['HTTP_HOST']
                . '/notification_uri')
            ->getToken( $signer , $privateKey );
        print_r ($token->toString());
    }

    protected static function getLoginHint($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $encoder = new JoseEncoder();
        $jwkConverter = new JWKConverter();
        $jwkPublicKey = self::getJwkPublicKey();
        $publicKey = $jwkConverter->toPEM($jwkPublicKey);
        openssl_public_encrypt($phone, $encryptedMsisdn, $publicKey);
        return 'ENCR_MSISDN:' . $encoder->base64UrlEncode($encryptedMsisdn);
    }

    protected static function getJwkPublicKey()
    {
        $curl = new curl\Curl();
        $resultJson = $curl->setHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . \Yii::$app->params['mtsAuthKey']
        ])->post('https://idgw.mobileid.mts.ru/oidc/jwks');
        return json_decode($resultJson);
    }
}