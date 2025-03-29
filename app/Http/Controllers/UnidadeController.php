<?php

namespace App\Http\Controllers;


use App\Http\Resources\ServidorEfetivoResource;
use App\Models\Unidade;
use Illuminate\Http\Request;

class UnidadeController extends ApiController
{
    public function index(Request $request)
    {
        $quant_itens = min($request->query('per_page', parent::PER_PAGE_DEFAULT), parent::PER_PAGE_MAX);

        $response = Unidade::with('endereco.cidade')->paginate($quant_itens);

        return response()->json([
            'last_page' => $response->lastPage(),
            'per_page' => $response->perPage(),
            'total' => $response->total(),
            'data' => ServidorEfetivoResource::collection($response->items()),
        ]);
    }


}
