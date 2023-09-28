<?php

namespace Source\Traits;

trait AppResponseTrait
{
    /**
     * Retorna uma resposta de sucesso ao cliente parametrizada
     *
     * @param array $data
     * @param string $message
     * @param integer $status
     * @return string
     */
    protected function successResponse($data = [], $message = "", $status = 200): string
    {
        header('Content-Type: application/json');

        return json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'status' => $status
        ]);
    }

    /**
     * Retorna uma resposta de erro ao cliente parametrizada
     *
     * @param array $data
     * @param string $message
     * @param integer $status
     * @return string
     */
    protected function errorResponse($data = [], $message = "", $status = 400): string
    {
        header('Content-Type: application/json');

        return json_encode([
            'success' => false,
            'data' => $data,
            'message' => $message,
            'status' => $status
        ]);
    }
}
