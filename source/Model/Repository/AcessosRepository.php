<?php

namespace Source\Model\Repository;

require __DIR__ . '../../../Model/Entity/Acesso.php';

use Source\Model\Entity\Acesso;

class AcessosRepository
{
    public function lerDadosAcesso(): Acesso
    {
        $model = new Acesso();
        $valor = CONFIGURACOES['ambiente'];
        $acesso = $model->find("ambiente = '$valor'")->limit(1)->fetch(true);

        return $acesso != null ? $acesso[0] : $model;
    }
}
