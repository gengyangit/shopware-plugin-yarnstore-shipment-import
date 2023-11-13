<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Mapper;

interface ShipmentQueueMapperInterface
{
    /**
     * @param array<string, mixed> $data
     * 
     * @return array <string, mixed>
     */
    public function mapApiShipmentDataToShipmentQueueData(array $data): array;
}