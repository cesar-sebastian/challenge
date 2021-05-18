<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

        $products = $this->em->getRepository(Product::class)->findAll();
        $this->assertCount(6, $products);
    }


//    public function consume($queue)
//    {
//        $kernel = static::createKernel();
//        $application = new Application($kernel);
//        $application->setAutoExit(false);
//
//        $input = new ArrayInput([
//            'command' => 'messenger:consume '
//        ]);
//
//        // You can use NullOutput() if you don't need the output
//        $output = new BufferedOutput();
//        $application->run($input, $output);
//
//        $content = $output->fetch();
//
//    }

}
