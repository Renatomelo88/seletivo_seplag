<?php

namespace App\Http\Controllers;


use App\Http\Requests\UnidadeFormRequestStore;
use App\Http\Requests\UnidadeFormRequestUpdate;
use App\Http\Resources\UnidadeResource;
use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'data' => UnidadeResource::collection($response->items()),
        ]);
    }

    public function store(UnidadeFormRequestStore $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Criar ou buscar a Cidade
            $cidade = Cidade::firstOrCreate(
                ['nome' => $validated['cidade'], 'uf' => $validated['uf']],
                ['nome' => $validated['cidade'], 'uf' => $validated['uf']]
            );


            // Criar a Unidade
            $unidade = Unidade::create([
                'nome' => $validated['nome'],
                'sigla' => $validated['sigla'],
            ]);

            // Criar o Endereco e associar à Unidade
            $endereco = Endereco::create([
                'tipo_logradouro' => $validated['tipo_logradouro'],
                'logradouro' => $validated['logradouro'],
                'numero' => $validated['numero'],
                'bairro' => $validated['bairro'],
                'cidade_id' => $cidade->id,
            ]);
            $unidade->endereco()->attach($endereco->id);

            $unidade->load('endereco.cidade');

            DB::commit();

            return response()->json(new UnidadeResource($unidade), 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return parent::error($e);

        }
    }

    public function show($id)
    {

        try {
            $unidade = Unidade::with('endereco.cidade')->findOrFail($id);

            return response()->json(new UnidadeResource($unidade));

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unidade não encontrada com esse id'], 404);
        }
    }

    public function update(UnidadeFormRequestUpdate $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $unidade = Unidade::with('endereco.cidade')->findOrFail($id);

            // Atualizar ou buscar a Cidade, se enviada
            if (isset($validated['cidade'])) {
                $uf = $validated['uf'] ?? $unidade->endereco->first()->cidade->uf;
                $cidade = Cidade::firstOrCreate(
                    ['nome' => $validated['cidade'], 'uf' => $uf],
                    ['nome' => $validated['cidade'], 'uf' => $uf]
                );
            } else {
                $cidade = $unidade->endereco->first()->cidade;
            }

            // Atualizar os dados da Unidade
            $unidade->update([
                'nome' => $validated['nome'] ?? $unidade->nome,
                'sigla' => $validated['sigla'] ?? $unidade->sigla,
            ]);

            // Atualizar o Endereco, se algum campo relacionado for enviado
            if (isset($validated['tipo_logradouro']) || isset($validated['logradouro']) ||
                isset($validated['numero']) || isset($validated['bairro']) ||
                isset($validated['cidade'])) {
                $endereco = $unidade->endereco->first();
                if ($endereco) {
                    $endereco->update([
                        'tipo_logradouro' => $validated['tipo_logradouro'] ?? $endereco->tipo_logradouro,
                        'logradouro' => $validated['logradouro'] ?? $endereco->logradouro,
                        'numero' => $validated['numero'] ?? $endereco->numero,
                        'bairro' => $validated['bairro'] ?? $endereco->bairro,
                        'cidade_id' => $cidade->id,
                    ]);
                } else {
                    // Criar um novo endereço se não existir
                    $endereco = Endereco::create([
                        'tipo_logradouro' => $validated['tipo_logradouro'] ?? '',
                        'logradouro' => $validated['logradouro'] ?? '',
                        'numero' => $validated['numero'] ?? 0,
                        'bairro' => $validated['bairro'] ?? '',
                        'cidade_id' => $cidade->id,
                    ]);
                    $unidade->endereco()->attach($endereco->id);
                }
            }

            $unidade->load('endereco.cidade');

            DB::commit();

            return response()->json(new UnidadeResource($unidade), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Unidade não encontrada com esse id'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return parent::error($e);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $unidade = Unidade::with('endereco')->findOrFail($id);

            // Desvincular os endereços associados
            $enderecos = $unidade->endereco;
            if ($enderecos->isNotEmpty()) {
                $unidade->endereco()->detach();
                foreach ($enderecos as $endereco) {
                    $endereco->delete();
                }
            }

            // Deletar a Unidade
            $unidade->delete();

            DB::commit();

            // Retorna status 204 (No Content) indicando sucesso sem corpo de resposta
            return response()->json(null, 204);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Unidade não encontrada com esse id'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return parent::error($e);
        }
    }


}
