<?php

namespace Tests\Controller;

use Beelab\TestBundle\Test\WebTestCase;

/**
 * @group functional
 */
final class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        self::$client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isOk());
    }
}
