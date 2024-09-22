<?php

namespace Tests\Controller;

use Beelab\TestBundle\Test\WebTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;


#[Group('functional')]
final class DefaultControllerTest extends WebTestCase
{
    #[Test]
    public function homepage(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseIsSuccessful();
    }
}
