<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Mapper;

class ShipmentQueueMapper implements ShipmentQueueMapperInterface
{
    /**
     * @param array<string, mixed> $data
     * 
     * @return array <string, mixed>
     */
    public function mapApiShipmentDataToShipmentQueueData(array $data): array
    {
        return [
            "extern_order_number" => $data['AuftragID'],
            "customer" => $this->mapShipmentDataToCustomerData($data),
            "addresses" => $this->mapShipmentDataToDeliveriesData($data),
            "line_items" => $this->mapShipmentDataToLineItems($data)
        ];
    }   

    /**
     * @param array<string, mixed> $data
     * 
     * @return array<string, mixed>
     */
    protected function mapShipmentDataToCustomerData(array $data): array 
    {
        return [
            "email" => $data["KundeEmail"],
            "firstname" => $data["KundeVorname"],
            "lastname" => $data["KundeName"]
        ];
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return array<string, mixed>
     */
    protected function mapShipmentDataToDeliveriesData(array $data): array 
    {
        return [
            "address" => [
                "firstname" =>  $data["KundeVorname"],
                "lastname" =>  $data["KundeName"],
                "street" => $data["KundeAdresse"],
                "zipcode" => $data["KundePLZ"],
                "city" => $data["KundeOrt"],
                "country" => "",
                "iso_code" => $data["KundeLand"]
            ]
        ];
    }
    
    /**
     * @param array<string, mixed> $data
     * 
     * @return array<string, mixed>
     */
    protected function mapShipmentDataToLineItems(array $data): array
    {
        $positionen = $data['Positionen'];
        $shipments = $data['Lieferungen'];
        $lineItems = [];

        foreach ($positionen  as $item) {

            $lineItems[] = [
                "product_number" => $item["Bestellnummer"],
                "quantity_ordered" => $item['ArtMenge'],
                "quantity_shipped" => $item['GelieferteMenge'],
                "quantity_open_to_ship" => $item['OffeneMenge'],
                "shipments" => $this->getShipments($item, $shipments),
            ];
        }

        return $lineItems;
    }

    /**
     * @param array<string, mixed> $lineItem
     * @param array<int, mixed> $shipments
     * 
     * @return array
     */
    protected function getShipments(array $lineItem, array $shipments): array 
    {
        foreach($shipments as $shipment) {

            if (!array_key_exists("Positionen", $shipment)
            || !isset($shipment["Positionen"])
            ) {
                continue;
            }

            foreach ($shipment["Positionen"] as $position) {
                if ($position["Bestellnummer"] !== $lineItem["Bestellnummer"]) {
                    continue;
                }

                return [
                    "shipping_carier" => $shipment["PrefLieferart"],
                    "shipping_method" => $shipment["Parcel"]["VersandArt"],
                    "tracking_code" => $shipment["Parcel"]["ParcelNummer"],
                    "shipment_date" => $shipment["Parcel"]["Datum"]
                ];
            }
        }

        return [];
    }

}