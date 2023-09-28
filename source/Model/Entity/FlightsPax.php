<?php

namespace Source\Model\Entity;

use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Address
 * @package Source\Model\Entity
 */
class FlightsPax extends DataLayer
{
    /**
     * FlightsPax constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "flights_pax",
            [],
            "Id",
            false
        );
    }
}
