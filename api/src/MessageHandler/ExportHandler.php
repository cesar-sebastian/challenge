<?php


namespace App\MessageHandler;

use App\Entity\Product;
use App\Message\Export;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Filesystem\Filesystem;


class ExportHandler implements MessageHandlerInterface
{
    private Serializer $serializer;
    private Filesystem $filesystem;
    private EntityManagerInterface $entityManager;

    /**
     * ExportHandler constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $encoders = [new CsvEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->filesystem = new Filesystem();
        $this->entityManager = $entityManager;

        gc_enable();
    }

    public function __invoke(Export $export)
    {
        $data = $this->serializer->serialize($export->getProducts(), 'csv');

        $this->filesystem->dumpFile(sprintf('./public/files/export/challenge_%s.csv', (string) time()), $data);

        $q = $this->entityManager->createQuery('UPDATE App\Entity\Product p SET p.status = ?1 WHERE p.id IN (?2)');
        $q->setParameter(1,Product::STATUS_PROCESSED);
        $q->setParameter(2, $export->getProductsIds(), \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        $q->execute();

        gc_collect_cycles();
    }
}
