<?php

namespace AppBundle\Test;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as Loader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @codeCoverageIgnore
 */
abstract class WebTestCase extends SymfonyWebTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $environment = 'test';
        if (getenv('TEST_TOKEN') !== false) {
            $environment = 'test'.getenv('TEST_TOKEN');
        }
        if (empty($this->container)) {
            $kernel = static::createKernel(['environment' => $environment]);
            $kernel->boot();
            $this->container = $kernel->getContainer();
            $this->em = $this->container->get('doctrine.orm.entity_manager');
        }
        if (empty(static::$admin)) {
            $this->client = static::createClient(['environment' => $environment]);
        } else {
            $this->client = static::createClient(['environment' => $environment], [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => $this->container->getParameter('admin_password'),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->em->getConnection()->close();
        parent::tearDown();
    }

    /**
     * Salva l'output della richiesta e lo mostra nel browser
     *
     * @param Client  $client
     * @param string  $browser
     * @param boolean $delete
     */
    protected function saveOutput(Client $client = null, $browser = '/usr/bin/firefox', $delete = true)
    {
        if (is_null($client)) {
            $client = $this->client;
        }
        $file = $this->container->get('kernel')->getRootDir().'/../web/test.html';
        $url = $this->container->getParameter('domain').'/test.html';
        if (false !== $profile = $client->getProfile()) {
            $url .= '?'.$profile->getToken();
        }
        file_put_contents($file, $client->getResponse()->getContent());
        $process = new Process($browser.' '.$url);
        $process->start();
        sleep(3);
        if ($delete) {
            unlink($file);
        }
    }

    /**
     * Login
     * See http://blog.bee-lab.net/login-automatico-con-fosuserbundle/
     *
     * @param string $username
     * @param string $firewall XXX check security.yml
     */
    protected function login($username = 'admin1@example.org', $firewall = 'main')
    {
        $userManager = $this->container->get('beelab_user.manager');
        $user = $userManager->find($username);
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session = $this->container->get('session');
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Get a file to be used in a form
     *
     * @param  int          $file
     * @return UploadedFile
     */
    protected function getImageFile($file = 0)
    {
        $dir = __DIR__.'/../../../app/Resources/public/images/';
        if ($file == 0) {
            $path = $dir.'image_for_test.jpeg';
        } else {
            copy($dir.'/image_for_test.jpeg', $dir.'/image_for_test'.$file.'.jpeg');
            $path = $dir.'/image_for_test'.$file.'.jpeg';
        }

        return new UploadedFile($path, 'photo.jpeg', 'image/jpeg', 12342);
    }

    /**
     * Carica le fixtures come array di "nomi"
     *
     * @param array  $fixtures  es. ['Utenti', 'Ordini']
     * @param string $namespace
     */
    protected function loadFixtures(array $fixtures, $namespace = 'AppBundle\\DataFixtures\\ORM\\')
    {
        $this->em->getConnection()->exec('SET foreign_key_checks = 0');
        $loader = new Loader($this->container);
        foreach ($fixtures as $fixture) {
            $this->loadFixtureClass($loader, $namespace.$fixture);
        }
        $executor = new ORMExecutor($this->em, new ORMPurger());
        $executor->execute($loader->getFixtures());
        $this->em->getConnection()->exec('SET foreign_key_checks = 1');
    }

    /**
     * Assert that $num mail has been sent
     * Need $this->client->enableProfiler() before calling
     *
     * @param integer $num
     * @param string  $message
     */
    protected function assertMailSent($num, $message = '')
    {
        if (false !== $profile = $this->client->getProfile()) {
            $collector = $profile->getCollector('swiftmailer');
            $this->assertEquals($num, $collector->getMessageCount(), $message);
        } else {
            $this->markTestSkipped('Profiler not enabled.');
        }
    }

    /**
     * Recupera il valore di un campo di un form, in base all'id.
     * Utile per fare POST
     *
     * @param  Crawler $crawler
     * @param  string  $field
     * @return string
     */
    protected function getFormValue(Crawler $crawler, $fieldId)
    {
        return $crawler->filter('#'.$fieldId)->attr('value');
    }

    /**
     * Carica una singola classe fixture
     * (con eventuali altre fixtures da cui dipende)
     *
     * @param Loader $loader
     * @param string $className
     */
    private function loadFixtureClass(Loader $loader, $className)
    {
        $fixture = new $className();
        if ($loader->hasFixture($fixture)) {
            unset($fixture);

            return;
        }
        $loader->addFixture($fixture);
        if ($fixture instanceof DependentFixtureInterface) {
            foreach ($fixture->getDependencies() as $dependency) {
                $this->loadFixtureClass($loader, $dependency);
            }
        }
    }
}
