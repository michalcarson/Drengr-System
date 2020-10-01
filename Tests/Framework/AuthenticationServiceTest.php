<?php

namespace Drengr\Tests\Framework;

use Drengr\Framework\AuthenticationService;
use Drengr\Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    /** @var AuthenticationService */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = $this->get(AuthenticationService::class);
    }

    public function testGenerateUserKey()
    {
        $this->assertFalse(empty($this->service->generateUserKey()));
    }

    public function testEncode64()
    {
        $this->assertEquals(
            'SSBhbSBhIHRlYXBvdC4',
            $this->service->encode64('I am a teapot.')
        );
    }

    public function testDecode64()
    {
        $this->assertEquals(
            'I am a teapot.',
            $this->service->decode64('SSBhbSBhIHRlYXBvdC4')
        );
    }

    public function testEncode64Decode64()
    {
        $randomString = $this->service->generateUserKey();

        $this->assertEquals(
            $randomString,
            $this->service->decode64($this->service->encode64($randomString))
        );
    }

    public function testToken()
    {
        $user = $this->factory->user->create_and_get();
        $this->assertTrue($user instanceof \WP_User);

        $token = $this->service->token($user);
        $this->assertFalse(empty($token));
    }

    public function testValidateSignature()
    {
        $message = 'adsflkjadfslkajsdflkjasdfljasdflkjasdflkjasdflkjasdf';
        $key = sodium_crypto_auth_keygen();

        $signature = $this->service->signature($message, $key);
        $this->assertTrue(
            $this->service->validateSignature($message, $signature, $key)
        );
    }

    public function testValidateToken()
    {
        $user1 = $this->factory->user->create_and_get();
        $this->assertTrue($user1 instanceof \WP_User);

        $token = $this->service->token($user1);
        $this->assertFalse(empty($token));

        $user2 = $this->service->validateToken($token);

        $this->assertTrue(is_object($user2));
        $this->assertEquals($user1->ID, $user2->ID);
    }
}
