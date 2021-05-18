<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Worker;

class ProductTest extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to put it in a known state between every tests
    use RefreshDatabaseTrait;

    private Client $client;
    private EntityManager $em;

    protected function setup(): void
    {
        $this->client = static::createClient();
        $this->em = static::$container->get('doctrine')->getManager();
    }

    public function testImport()
    {
        $file = new UploadedFile('public/files/import/challenge.json', 'challenge.json');

        $this->client->request('POST', '/import_products', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $file,
                ],
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        //$this->consume('async_priority_high', 6);

        $products = $this->em->getRepository(Product::class)->findAll();
        $this->assertCount(6, $products);
    }

    public function consume($transport, $number)
    {
        $transport = static::$container->get(sprintf('messenger.transport.%s', $transport));
        $bus = static::$container->get(MessageBusInterface::class);
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener($number));

        $worker = new Worker([$transport], $bus, $eventDispatcher);
        $worker->run([
            'sleep' => 0,
        ]);
    }

}
