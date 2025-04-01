<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuscaRequest;
use App\Http\Requests\ServidorEfetivoFormRequestStore;
use App\Http\Requests\ServidorEfetivoFormRequestUpdate;
use App\Http\Resources\ServidorEfetivoResource;
use App\Http\Resources\ServidorUnidadeResource;
use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use App\Models\FotoPessoa;
use App\Models\Endereco;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServidorEfetivoController extends ApiController
{
    public function index(Request $request)
    {
        $quant_itens = min($request->query('per_page', parent::PER_PAGE_DEFAULT), parent::PER_PAGE_MAX);

        $response = ServidorEfetivo::with('pessoa.foto', 'pessoa.endereco.cidade')->paginate($quant_itens);

        return response()->json([
            'last_page' => $response->lastPage(),
            'per_page' => $response->perPage(),
            'total' => $response->total(),
            'itens' => ServidorEfetivoResource::collection($response->items()),
        ]);
    }

    public function store(ServidorEfetivoFormRequestStore $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Criar ou buscar a Cidade
            $cidade = Cidade::firstOrCreate(
                ['nome' => $validated['cidade'], 'uf' => $validated['uf']],
                ['nome' => $validated['cidade'], 'uf' => $validated['uf']]
            );

            // Criar a Pessoa
            $pessoa = Pessoa::create([
                'nome' => $validated['nome'],
                'data_nascimento' => $validated['data_nascimento'],
                'sexo' => $validated['sexo'],
                'mae' => $validated['mae'],
                'pai' => $validated['pai'],
            ]);

            // Criar o ServidorEfetivo associado à Pessoa
            $servidor = ServidorEfetivo::create([
                'pessoa_id' => $pessoa->id,
                'matricula' => $validated['matricula'],
            ]);

            // Fazer upload da foto e criar FotoPessoa
            $fotoPath = $request->file('foto')->store('fotos', 's3');
            $foto = FotoPessoa::create([
                'pessoa_id' => $pessoa->id,
                'data' => now(),
                'bucket' => env('AWS_BUCKET'),
                'hash' => $fotoPath,
            ]);

            // Criar o Endereco e associar à Pessoa
            $endereco = Endereco::create([
                'tipo_logradouro' => $validated['tipo_logradouro'],
                'logradouro' => $validated['logradouro'],
                'numero' => $validated['numero'],
                'bairro' => $validated['bairro'],
                'cidade_id' => $cidade->id,
            ]);
            $pessoa->endereco()->attach($endereco->id);

            $servidor->load('pessoa.foto', 'pessoa.endereco.cidade');

            DB::commit();

            return response()->json(new ServidorEfetivoResource($servidor), 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return parent::error($e);
        }
    }

    public function show($matricula)
    {
        try {
            $servidor = ServidorEfetivo::with('pessoa.foto', 'pessoa.endereco.cidade')
                ->where('matricula', $matricula)
                ->firstOrFail();

            return response()->json(new ServidorEfetivoResource($servidor));

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Servidor não encontrado'], 404);
        }
    }

    public function update(ServidorEfetivoFormRequestUpdate $request, $matricula)
    {

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Buscar o ServidorEfetivo pela matricula
            $servidor = ServidorEfetivo::with('pessoa.foto', 'pessoa.endereco.cidade')
                ->where('matricula', $matricula)
                ->firstOrFail();

            // Se cidade for enviada, usa o UF enviado ou o UF atual do endereço
            if (isset($validated['cidade'])) {
                $uf = $validated['uf'] ?? $servidor->pessoa->endereco->first()->cidade->uf;
                $cidade = Cidade::firstOrCreate(
                    ['nome' => $validated['cidade'], 'uf' => $uf],
                    ['nome' => $validated['cidade'], 'uf' => $uf]
                );
            } else {
                $cidade = $servidor->pessoa->endereco->first()->cidade;
            }

            // Atualizar a Pessoa
            $servidor->pessoa->update([
                'nome' => $validated['nome'] ?? $servidor->pessoa->nome,
                'data_nascimento' => $validated['data_nascimento'] ?? $servidor->pessoa->data_nascimento,
                'sexo' => $validated['sexo'] ?? $servidor->pessoa->sexo,
                'mae' => $validated['mae'] ?? $servidor->pessoa->mae,
                'pai' => $validated['pai'] ?? $servidor->pessoa->pai,
            ]);

            // Atualizar o ServidorEfetivo (se matricula mudar)
            if (isset($validated['matricula']) && $validated['matricula'] !== $servidor->matricula) {
                $servidor->update(['matricula' => $validated['matricula']]);
            }

            // Atualizar a Foto, se fornecida
            if ($request->hasFile('foto')) {

                if ($servidor->pessoa->foto) {
                    // não apaga o arquivo pois esta usando softdeletes para manter o historico
                    $servidor->pessoa->foto->delete();
                }
                // Fazer upload da nova foto
                $fotoPath = $request->file('foto')->store('fotos', 's3');
                FotoPessoa::create([
                    'pessoa_id' => $servidor->pessoa->id,
                    'data' => now(),
                    'bucket' => env('AWS_BUCKET'),
                    'hash' => $fotoPath,
                ]);
            }

            // Atualizar o Endereco, se algum campo relacionado for fornecido
            if (isset($validated['tipo_logradouro']) || isset($validated['logradouro']) ||
                isset($validated['numero']) || isset($validated['bairro']) ||
                isset($validated['cidade'])) {
                $endereco = $servidor->pessoa->endereco->first(); // Pega o primeiro endereço
                if ($endereco) {
                    $endereco->update([
                        'tipo_logradouro' => $validated['tipo_logradouro'] ?? $endereco->tipo_logradouro,
                        'logradouro' => $validated['logradouro'] ?? $endereco->logradouro,
                        'numero' => $validated['numero'] ?? $endereco->numero,
                        'bairro' => $validated['bairro'] ?? $endereco->bairro,
                        'cidade_id' => $cidade->id,
                    ]);
                } else {
                    // Cria um novo endereço se não existir
                    $endereco = Endereco::create([
                        'tipo_logradouro' => $validated['tipo_logradouro'] ?? '',
                        'logradouro' => $validated['logradouro'] ?? '',
                        'numero' => $validated['numero'] ?? 0,
                        'bairro' => $validated['bairro'] ?? '',
                        'cidade_id' => $cidade->id,
                    ]);
                    $servidor->pessoa->endereco()->attach($endereco->id);
                }
            }

            // Recarregar as relações atualizadas
            $servidor->load('pessoa.foto', 'pessoa.endereco.cidade');

            DB::commit();

            return response()->json(new ServidorEfetivoResource($servidor));

        } catch (ModelNotFoundException $e) {

            DB::rollBack();
            return response()->json(['error' => 'Servidor não encontrado'], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return parent::error($e);
        }
    }


    public function destroy($matricula)
    {
        DB::beginTransaction();
        try {
            $servidor = ServidorEfetivo::with('pessoa.foto', 'pessoa.endereco')
                ->where('matricula', $matricula)
                ->firstOrFail();

            // A foto não será deletada do S3 pois o sistema está com softDeletes
            if ($servidor->pessoa->foto) {
                $servidor->pessoa->foto->delete();
            }


            // Desvincular e deletar endereços associados
            $enderecos = $servidor->pessoa->endereco;
            if ($enderecos->isNotEmpty()) {
                $servidor->pessoa->endereco()->detach();
                foreach ($enderecos as $endereco) {
                    $endereco->delete();
                }
            }

            // Deletar a pessoa e o servidor efetivo
            $servidor->pessoa->delete();
            $servidor->delete();

            DB::commit();

            // Retorna status 204 (No Content) indicando sucesso sem corpo de resposta
            return response()->json(null, 204);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Servidor não encontrado'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao excluir o servidor: ' . $e->getMessage()], 500);
        }
    }

    public function busca(BuscaRequest $request)
    {
        $quant_itens = min($request->query('per_page', parent::PER_PAGE_DEFAULT), parent::PER_PAGE_MAX);
        $request = $request->validated();
        try {
            $servidoresEfetivos = ServidorEfetivo::whereHas('pessoa', function ($query) use ($request) {
                $query->whereRaw('nome ilike ?', '%' . $request['nome'] . '%');
            })->with('pessoa', 'pessoa.lotacao.unidade.endereco')
                ->paginate($quant_itens);

            return response()->json([
                'last_page' => $servidoresEfetivos->lastPage(),
                'per_page' => $servidoresEfetivos->perPage(),
                'total' => $servidoresEfetivos->total(),
                'itens' => ServidorUnidadeResource::collection($servidoresEfetivos),
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Servidor não encontrado'], 404);
        }
    }

}