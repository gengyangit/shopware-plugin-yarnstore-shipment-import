<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Command;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yarnstore\ShipmentImport\Service\Client\HttpClientInterface;
use Yarnstore\ShipmentImport\Service\Manager\ShipmentQueueManagerInterface;
use Yarnstore\ShipmentImport\Service\Mapper\ShipmentQueueMapperInterface;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;

class ImportShipmentCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'yarnstore-shipment-import:import-shipments';

    /**
     * @var string
     */
    //protected const BASE_URI = 'https://testserver.schmeichelgarne.de/shop/api/bestellung.php';
    protected const BASE_URI = 'https://www.soul-wool.com/shop/api/bestellung.php';

    /**
     * @var string
     */
    protected const AUTHORIZATION = 'token 17bcf69733c7ef845575b1ed29a49465032c7f87acdfa838dd54d21927bb933e';

    /**
     * @var string
     */
    protected const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    protected const STATUS_PROCESSING = 'processing';

    /**
     * @var string
     */
    protected const STATUS_PROCESSED = 'processed';

    /**
     * @var string
     */
    protected const SUCCESS_STATUS_CODE = 200;

    /**
     * @var \Yarnstore\ShipmentImport\Service\Client\HttpClientInterface
     */
    protected $client;

    /**
     * @var \Yarnstore\ShipmentImport\Service\Manager\ShipmentQueueManagerInterface
     */
    protected $shipmentQueueManager;

    /**
     * @var \Yarnstore\ShipmentImport\Service\Mapper\ShipmentQueueMapperInterface
     */
    protected $shipmentQueueMapper;

    /**
     * @var \Yarnstore\ShipmentImport\Service\Writer\Queue\OrderQueueWriterInterface
     */
    protected $orderQueueWriter;

    /**
     * Constructor 
     * 
     * @param \Yarnstore\ShipmentImport\Service\Client\HttpClientInterface $client
     * @param \Yarnstore\ShipmentImport\Service\Manager\ShipmentQueueManagerInterface $shipmentQueueManager
     * @param \Yarnstore\OrderExport\Service\Writer\Queue\OrderQueueWriterInterface $orderQueueWriter
     * @param \Yarnstore\OrderExport\Service\Mapper\OrderMapperInterface $orderMapper
     * @param \Yarnstore\OrderExport\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        HttpClientInterface $client,
        ShipmentQueueManagerInterface $shipmentQueueManager,
        ShipmentQueueMapperInterface $shipmentQueueMapper,
        //LoggerInterface $logger,
    ) {
        $this->client = $client;
        $this->shipmentQueueManager = $shipmentQueueManager;
        $this->shipmentQueueMapper = $shipmentQueueMapper;

        parent::__construct();
    }

     /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Import Shipment');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Input\InputInterface $output
     * 
     * @return int
     */
    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {
        $output->writeln('Start execute command!');

        $shipmentCollection = $this->getShipments();

        if (count($shipmentCollection) == 0) {
            return static::SUCCESS_STATUS_CODE;    
        }

        
        $client = $this->client->create(
            [
                'Content-Type' => 'application/json',
                'Authorization' => static::AUTHORIZATION
            ]
        );


        /** @var \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity $shipmentEntity */
        foreach ($shipmentCollection as $shipmentEntity) {
           $shipment = $this->getShipment($shipmentEntity);

           if (!array_key_exists('data', $shipment) 
                || !isset($shipment['data'])
            ) {
                continue;
            }

           $shipmentQueueData = $this->shipmentQueueMapper
                ->mapApiShipmentDataToShipmentQueueData($shipment['data'][0]);

           print_r($shipmentQueueData); exit('4');

           $this->saveShipment($shipmentEntity->getOrderNumber(), $shipment);
        }

        return static::SUCCESS_STATUS_CODE;
    }   
    
    /**
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection
     */
    protected function getShipments(): ShipmentQueueCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('status', [static::STATUS_PENDING, static::STATUS_PROCESSING]));

        return $this->shipmentQueueManager->getCollection($criteria);       
    }

    /**
     * 
     */
    protected function getShipment(ShipmentQueueEntity $shipmentEntity)
    {
        $response = $this->client->get(
            static::BASE_URI,
            ["AuftragID" => $shipmentEntity->getExternOrderNumber()]
        );

        $body = json_decode((string)$response->getBody(), true);

        return $body;
    }

    /**
     * 
     */
    protected function saveShipment(string $orderNumber, array $shipment) 
    {
        $this->shipmentQueueManager->save(
            [
                'order_number' => $orderNumber,
                'data' => $shipment,
                'status' => static::STATUS_PROCESSING
            ]
        );
    }
}
