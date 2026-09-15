<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetFizzBuzzControllerTest extends WebTestCase
{
    public function testOriginalList(): void
    {
        $expected = '1,2,fizz,4,buzz,fizz,7,8,fizz,buzz,11,fizz,13,14,fizzbuzz,16';
        $client = static::createClient();
        $client->request('GET', '/fizzbuzz?int1=3&int2=5&limit=16&str1=fizz&str2=buzz');
        self::assertResponseIsSuccessful();
        $this->assertEquals($expected, (string) $client->getResponse()->getContent());
    }

    public function testCase1(): void
    {
        $expected =  'foo,foobar,foo,foobar,foo,foobar,foo,foobar,foo,foobar';
        $client = static::createClient();
        $client->request('GET', '/fizzbuzz?int1=1&int2=2&limit=10&str1=foo&str2=bar');
        self::assertResponseIsSuccessful();
        $this->assertEquals($expected, (string) $client->getResponse()->getContent());
    }

    public function testMissingArgsIsError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fizzbuzz?int2=2&limit=10&str1=foo&str2=bar');
        self::assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testLimitBeyondAllowed(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fizzbuzz?int1=1&int2=10&str1=A&limit=100000000000&str2=B');
        $responseJson = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('violations', $responseJson);
        $this->assertArrayHasKey('limit', $responseJson['violations']);
    }

    public function testOverflowAsException(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fizzbuzz?int1=3037000500&int2=3037000500000&str1=A&limit=100&str2=B');
        $responseJson = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('error', $responseJson);
        $this->assertEquals('int1 and int2 cannot be used to generate a fizzbuzz list. Select lower numbers than those ones.', $responseJson['error']);
    }
}
