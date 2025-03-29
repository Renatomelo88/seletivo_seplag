<?php

namespace App\Http\Controllers;
abstract class ApiController
{
    const PER_PAGE_DEFAULT = 20;
    const PER_PAGE_MAX = 50;

    public function error($exception, $mensagem = null)
    {
        $error['error'] = $mensagem ?? 'Ocorreu um erro ao processar a requisição. Tente novamente mais tarde.';

        if (env('APP_ENV') !== 'production' && env('APP_DEBUG') === true) {
            $error['message'] = $exception->getMessage();
        }

        return response()->json($error, 500);
    }
}