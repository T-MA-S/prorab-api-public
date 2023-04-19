<?php

use Codeception\Util\HttpCode;
use Faker\Factory;
use Codeception\Util\Fixtures;

class UserCest
{
    protected $token;
    protected $phone;
    protected $password;

    public function _before(ApiTester $I)
    {
        $I->clearTable('user');
        $I->clearTable('account');
        foreach (Fixtures::get('users') as $item) {
            $I->haveInDatabase('user', $item);
        }
        if(Fixtures::exists('accounts')){
            foreach (Fixtures::get('accounts') as $item) {
                $I->haveInDatabase('account', $item);
            }
        }
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
    }

    public function createNewUser(ApiTester $I)
    {
        $faker = Factory::create();

        $I->sendPost(
            'user',
            [
                'name'     => $faker->name,
                'phone'    => $faker->phoneNumber,
                'email'    => $faker->email,
                'password' => $faker->password,
                'telegram' => '',
                'whatsapp' => '',
                'viber'    => '',
            ]
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'id'       => 'integer',
                'name'     => 'string',
                'phone'    => 'string',
                'email'    => 'string',
                'avatar'   => 'string|null',
                'telegram' => 'string|null',
                'whatsapp' => 'string|null',
                'viber'    => 'string|null',
            ]
        );
    }

    public function loginUser(ApiTester $I)
    {
        $I->sendPost('user/login', [
            'login'    => 1111,
            'password' => 111
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType(
            [
                'phone'        => 'string',
                'email'        => 'string',
                'role'         => 'string',
                'access_token' => 'string',
            ]
        );
    }

    public function getIdentity(ApiTester $I)
    {
        $access_token = $I->grabFromDatabase('account', 'access_token', array('user_id' => 2));
        $I->amBearerAuthenticated($access_token);
        $I->sendGet('user/identity');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'id'       => 'integer',
                'name'     => 'string',
                'phone'    => 'string',
                'email'    => 'string',
                'avatar'   => 'string|null',
                'telegram' => 'string|null',
                'whatsapp' => 'string|null',
                'viber'    => 'string|null',
            ]
        );
    }

    public function getUser(ApiTester $I)
    {
        $access_token = $I->grabFromDatabase('account', 'access_token', array('user_id' => 2));
        $I->amBearerAuthenticated($access_token);
        $I->sendGet('user/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'id'       => 'integer',
                'name'     => 'string',
                'phone'    => 'string',
                'email'    => 'string',
                'avatar'   => 'string|null',
                'telegram' => 'string|null',
                'whatsapp' => 'string|null',
                'viber'    => 'string|null',
            ]
        );
    }

    public function updateUser(ApiTester $I)
    {

        $access_token = $I->grabFromDatabase('account', 'access_token', array('user_id' => 1));
        $I->amBearerAuthenticated($access_token);

        $faker = Factory::create();

        $I->sendPut(
            'user/2',
            [
                'name'     => $faker->name,
                'phone'    => $faker->phoneNumber,
                'email'    => $faker->email,
                'telegram' => '',
                'whatsapp' => '',
                'viber'    => '',
            ]
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'id'       => 'integer',
                'name'     => 'string',
                'phone'    => 'string',
                'email'    => 'string',
                'avatar'   => 'string|null',
                'telegram' => 'string|null',
                'whatsapp' => 'string|null',
                'viber'    => 'string|null',
            ]
        );
    }

    public function getAllUsers(ApiTester $I)
    {
        $access_token = $I->grabFromDatabase('account', 'access_token', array('user_id' => 1));
        $I->amBearerAuthenticated($access_token);
        $I->sendGet('user');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchemaString('{"type":"array"}');
        $validResponseJsonSchema = json_encode(
            [
                'properties' => [
                    'id'       => ['type' => 'integer'],
                    'name'     => ['type' => 'string'],
                    'phone'    => ['type' => 'string'],
                    'avatar'   => ['type' => 'string'],
                    'telegram' => ['type' => 'string'],
                    'whatsapp' => ['type' => 'string'],
                    'viber'    => ['type' => 'string'],
                ]
            ]
        );
        $I->seeResponseIsValidOnJsonSchemaString($validResponseJsonSchema);
    }

    public function getPasswordResetCode(ApiTester $I)
    {
        $I->sendGet(
            'user/get-password-reset-code',
            [
                'email' => 'test@test.com'
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'username'            => 'string',
                'password_reset_code' => 'string',
            ]
        );
    }

    public function resetPassword(ApiTester $I)
    {
        $I->sendGet(
            'user/get-password-reset-code',
            [
                'email' => 'test@test.com'
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'username'            => 'string',
                'password_reset_code' => 'string',
            ]
        );
    }
}
