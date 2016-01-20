<?php

namespace Tests\Controller;

use Beelab\TestBundle\Test\WebTestCase;

/**
 * @group functional
 */
class DefaultControllerTest extends WebTestCase
{
    public function testHomepage()
    {
        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isOk());
    }
}
