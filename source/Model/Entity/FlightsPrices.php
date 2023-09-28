<?php

namespace Source\Model\Entity;

use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Address
 * @package Source\Model\Entity
 */
class FlightsPrices extends DataLayer
{
    /**
     * FlightsPrices constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "flights_prices",
            [],
            "Id",
            false
        );
    }
}
