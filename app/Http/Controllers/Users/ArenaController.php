<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ArenasContract;

class ArenaController extends Controller
{
    public function __construct(private readonly ArenasContract $arenasRepository)
    {
    }

    /**
     * Listar todas as arenas ativas (público)
     */
    public function list(Request $request): JsonResponse
    {
        $filters = $request->all();

        // Filtrar apenas arenas ativas por padrão
        if (!isset($filters['ativo'])) {
            $filters['ativo'] = true;
        }

        $dados = $this->arenasRepository->paginate($filters)->toArray();

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Visualizar detalhes de uma arena (público)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $arena = $this->arenasRepository->getById($id);

            // Retornar apenas se a arena estiver ativa
            if (!$arena->ativo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arena não encontrada ou inativa'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $arena
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arena não encontrada'
            ], 404);
        }
    }

    /**
     * Buscar arenas por cidade/estado
     */
    public function buscarPorLocalizacao(Request $request): JsonResponse
    {
        $request->validate([
            'cidade' => 'required_without:estado|string',
            'estado' => 'required_without:cidade|string|size:2',
        ]);

        $filters = [
            'ativo' => true,
            'per_page' => $request->get('per_page', 20)
        ];

        if ($request->has('cidade')) {
            $filters['cidade'] = $request->cidade;
        }

        if ($request->has('estado')) {
            $filters['estado'] = $request->estado;
        }

        $dados = $this->arenasRepository->paginate($filters)->toArray();

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Buscar arenas próximas (por coordenadas)
     */
    public function buscarProximas(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'raio_km' => 'nullable|numeric|min:1|max:100',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $raioKm = $request->get('raio_km', 10); // padrão 10km

        // Buscar arenas próximas usando fórmula de Haversine
        $arenas = \App\Databases\Models\Arenas::query()
            ->where('ativo', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distancia_km
            ", [$latitude, $longitude, $latitude])
            ->having('distancia_km', '<=', $raioKm)
            ->orderBy('distancia_km', 'asc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $arenas
        ]);
    }

    /**
     * Buscar arenas por nome
     */
    public function buscar(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->q;

        $arenas = \App\Databases\Models\Arenas::query()
            ->where('ativo', true)
            ->where(function ($q) use ($query) {
                $q->where('nome', 'ILIKE', "%{$query}%")
                  ->orWhere('cidade', 'ILIKE', "%{$query}%")
                  ->orWhere('descricao', 'ILIKE', "%{$query}%");
            })
            ->orderBy('rating', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $arenas
        ]);
    }
}
