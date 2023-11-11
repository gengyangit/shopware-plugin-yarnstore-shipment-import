<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Manager;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;
use Yanduu\ShipmentImport\Service\Reader\Queue\ShipmentQueueReaderInterface;
use Yanduu\ShipmentImport\Service\Writer\Queue\ShipmentQueueWriterInterface;

class ShipmentQueueManager implements ShipmentQueueManagerInterface
{
    protected const STATUS_PENDING = "pending";

    /**
     * @var \Yanduu\ShipmentImport\Service\Writer\Queue\ShipmentQueueWriterInterface
     */
    protected ShipmentQueueWriterInterface $shipmentQueueWriter;

    /**
     * @var \Yanduu\ShipmentImport\Service\Reader\Queue\ShipmentQueueReaderInterface
     */
    protected ShipmentQueueReaderInterface $shipmentQueueReader;

    /**
     * Constructor 
     * 
     * @param \Yanduu\OrderExport\Service\Writer\OrderQueueWriterInterface $orderQueueWriter
     * @param \Yanduu\OrderExport\Service\Reader\Queue\OrderQueueReaderInterface $orderQueueReader
     * 
     */
    public function __construct(
        ShipmentQueueWriterInterface $shipmentQueueWriter,
        ShipmentQueueReaderInterface $shipmentQueueReader
    ) {
        $this->shipmentQueueReader = $shipmentQueueReader;
        $this->shipmentQueueWriter = $shipmentQueueWriter;
    }

     /**
     * @param array<string, mixed> $data
     * 
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection
     */
    public function getCollection(Criteria $criteria): ShipmentQueueCollection
    {
        return $this->shipmentQueueReader->getCollection($criteria);
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return \Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function save(array $data): EntityWrittenContainerEvent 
    {   
        $entity = $this->shipmentQueueReader->getEntityByOrderNumber($data['order_number']);

        if ($entity && $entity->getId()) {
            $params['id'] = $entity->getId();
            
            if (array_key_exists('data', $data)) {
                $params['data'] = $data['data'];
            }

            if (array_key_exists('status', $data)) {
                $params['status'] = $data['status'];
            }
            
            return $this->shipmentQueueWriter->update($params);
        }

        return $this->shipmentQueueWriter->create($data);
    }
}