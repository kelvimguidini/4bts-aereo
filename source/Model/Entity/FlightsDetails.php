<?php

namespace Source\Model\Entity;

use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Address
 * @package Source\Model\Entity
 */
class FlightsDetails extends DataLayer
{
    /**
     * FlightsDetails constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "flights_details",
            [],
            "Id",
            false
        );
    }
}
