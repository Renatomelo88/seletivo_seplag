<?php

namespace App\Http\Controllers;


use App\Http\Requests\LotacaoFormRequestStore;
use App\Http\Requests\LotacaoFormRequestUpdate;
use App\Http\Resources\LotacaoResource;
use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\Lotacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotacaoController extends ApiController
{
    public function index(Request $request)
    {
        $quant_itens = min($request->query('per_page', parent::PER_PAGE_DEFAULT), parent::PER_PAGE_MAX);

        $response = Lotacao::with('pessoa.endereco.cidade', 'pessoa.foto', 'unidade.endereco.cidade')
            ->paginate($quant_itens);

        return response()->json([
            'last_page' => $response->lastPage(),
            'per_page' => $response->perPage(),
            'total' => $response->total(),
            'data' => LotacaoResource::collection($response->items()),
        ]);
    }

    public function store(LotacaoFormRequestStore $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {

            // Criar a Lotacao
            $lotacao = Lotacao::create([
                'pessoa_id' => $validated['pessoa_id'],
                'unidade_id' => $validated['unidade_id'],
                'data_lotacao' => $validated['data_lotacao'],
                'data_remocao' => $validated['data_remocao'] ?? null,
                'portaria' => $validated['portaria'],
            ]);

            $lotacao->load('pessoa.endereco.cidade', 'pessoa.foto', 'unidade.endereco.cidade');

            DB::commit();

            return response()->json(new LotacaoResource($lotacao), 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return parent::error($e);

        }
    }

    public function show($id)
    {

        try {
            $unidade = Lotacao::with('pessoa.endereco.cidade', 'pessoa.foto', 'unidade.endereco.cidade')->findOrFail($id);

            return response()->json(new LotacaoResource($unidade));

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lotação não encontrada com esse id'], 404);
        }
    }

    public function update(LotacaoFormRequestUpdate $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $lotacao = Lotacao::findOrFail($id);

            // Preparar os dados para atualização
            $updateData = [
                'data_lotacao' => $validated['data_lotacao'] ?? $lotacao->data_lotacao,
                'portaria' => $validated['portaria'] ?? $lotacao->portaria,
            ];

            // Só atualizar data_remocao se estiver presente na requisição
            if ($request->has('data_remocao')) {
                $updateData['data_remocao'] = $request->input('data_remocao'); // Pode ser null ou uma data
            }
            $lotacao->update($updateData);

            $lotacao->load('pessoa.endereco.cidade', 'pessoa.foto', 'unidade.endereco.cidade');

            DB::commit();

            return response()->json(new LotacaoResource($lotacao), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lotação não encontrada com esse id'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return parent::error($e);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $unidade = Lotacao::findOrFail($id);

            // Deletar a Lotacao
            $unidade->delete();

            DB::commit();

            // Retorna status 204 (No Content) indicando sucesso sem corpo de resposta
            return response()->json(null, 204);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lotação não encontrada com esse id'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return parent::error($e);
        }
    }


}
