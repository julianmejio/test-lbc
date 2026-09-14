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
}
