<?php

namespace Source\Model\Entity;

use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Address
 * @package Source\Model\Entity
 */
class Acesso extends DataLayer
{
    /**
     * Acesso constructor.
     */
    public function __construct()
    {
        parent::__construct("flights_ws_acessos", ["id", "url", "login", "senha", "ambiente", "branch"], "id", false);
    }
}
