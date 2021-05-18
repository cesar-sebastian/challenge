<?php


namespace App\MessageHandler;


use App\Entity\ImportProduct;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\UploadProducts;
use App\Message\Export;
use App\Message\Import;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ImportHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private Serializer $serializer;
    private MessageBusInterface $messageBus;
    private int $batchSize;

    /**
     * ImportHandler constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;

        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $this->messageBus = $messageBus;
        $this->batchSize = $_ENV["BATCH_SIZE"];

        gc_enable();
    }

    public function __invoke(Import $import)
    {
        $products = $this->serializer->deserialize($import->getData(), 'App\Entity\Product[]', 'json');


        $i = 1;

        $export = new Export();

        /** @var Product $newProduct */
        foreach ($products as $newProduct)
        {

            $product = $this->entityManager->getRepository(Product::class)->findOneBy([
                'styleNumber' => $newProduct->getStyleNumber()
            ]);

            if(!$product) {
                $this->entityManager->persist($newProduct);
                $export->addProduct($newProduct);
            } else {
                /** @var Product $product */
                $product->setImages($newProduct->getImages())
                        ->setPrice($newProduct->getPrice())
                        ->setName($newProduct->getName());

                $uow = $this->entityManager->getUnitOfWork();

                $uow->computeChangeSet(
                    $this->entityManager->getClassMetadata(Product::class),
                    $product
                );

                if($uow->isEntityScheduled($product)){
                    $product->setStatus(Product::STATUS_PENDING);
                    $export->addProduct($newProduct);
                }
            }

            if (($i % $this->batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
                if (count($export->getProducts()) > 0)
                    $this->messageBus->dispatch($export);
            }

            $i++;
        }

        $this->entityManager->flush(); //Persist objects that did not make up an entire batch
        $this->entityManager->clear();
        if (count($export->getProducts()) > 0)
            $this->messageBus->dispatch($export);

        gc_collect_cycles();
    }

}
