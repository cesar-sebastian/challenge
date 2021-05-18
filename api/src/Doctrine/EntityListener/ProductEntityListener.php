<?php
namespace App\Doctrine\EntityListener;

use App\Entity\Product;
use App\Message\Export;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductEntityListener
{
    private MessageBusInterface $messageBus;

    /**
     * ProductEntityListener constructor.
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     * @return void
     */
    public function onFlush(OnFlushEventArgs $eventArgs) {

        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

//        $export = new Export();
//
//        //Insertions
//        foreach ($uow->getScheduledEntityInsertions() as $entity) {
//            if ($entity instanceof Product){
//                $export->addProduct($entity);
//            }
//        }
//
//        //Updates
//        foreach ($uow->getScheduledEntityUpdates() as $entity) {
//            if ($entity instanceof Product){
//                $export->addProduct($entity);
//            }
//        }
//
//        if(count($export->getProducts()) > 0)
//        {
//            $this->messageBus->dispatch($export);
//        }
    }


}
