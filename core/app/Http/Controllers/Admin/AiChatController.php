<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Anthropic\Client;
use Anthropic\Messages\ToolUseBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiChatController extends Controller
{
    private ?Client $client = null;

    private string $systemPrompt = <<<'PROMPT'
Tu es un assistant IA intégré au système de gestion **Programme Bandama**, une plateforme de gestion de coopératives agricoles en Côte d'Ivoire.

Tu as accès à des outils pour interroger les données en temps réel : producteurs, parcelles, livraisons, stocks, formations, coopératives, sections, etc.

Règles :
- Réponds toujours en **français** de façon claire et structurée.
- Pour toute question portant sur des données chiffrées ou des entités, utilise les outils disponibles pour obtenir des informations actualisées.
- Présente les résultats sous forme de liste ou tableau quand c'est pertinent.
- Si une question est hors de ton périmètre (données non disponibles), indique-le poliment.
PROMPT;

    private array $tools = [];

    public function __construct()
    {
        $this->tools = $this->buildTools();
    }

    private function model(): string
    {
        return env('ANTHROPIC_MODEL', 'claude-sonnet-4-5');
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $apiKey = env('ANTHROPIC_API_KEY');
            if (!$apiKey) {
                throw new \RuntimeException('ANTHROPIC_API_KEY non configuré dans le fichier .env');
            }
            $this->client = new Client(apiKey: $apiKey);
        }
        return $this->client;
    }

    private function buildTools(): array
    {
        return [
            [
                'name' => 'get_statistics',
                'description' => 'Obtenir les statistiques globales : nombre total de producteurs, parcelles, coopératives, livraisons, formations, stocks, etc.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_producteurs',
                'description' => 'Rechercher et lister des producteurs. Filtrable par nom/prénom, code, coopérative, section.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'search'         => ['type' => 'string',  'description' => 'Recherche par nom, prénom ou code'],
                        'cooperative_id' => ['type' => 'integer', 'description' => 'ID coopérative'],
                        'section_id'     => ['type' => 'integer', 'description' => 'ID section'],
                        'limit'          => ['type' => 'integer', 'description' => 'Nombre max de résultats (défaut 10, max 50)'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_parcelles',
                'description' => 'Rechercher et lister des parcelles. Filtrable par culture, producteur, coopérative.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'culture'        => ['type' => 'string',  'description' => 'Type de culture (ex: cacao, café)'],
                        'cooperative_id' => ['type' => 'integer', 'description' => 'ID coopérative'],
                        'producteur_id'  => ['type' => 'integer', 'description' => 'ID producteur'],
                        'limit'          => ['type' => 'integer', 'description' => 'Nombre max de résultats (défaut 10, max 50)'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_livraisons',
                'description' => 'Informations sur les livraisons : quantités, paiements, par coopérative ou période.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'cooperative_id' => ['type' => 'integer', 'description' => 'ID coopérative'],
                        'date_debut'     => ['type' => 'string',  'description' => 'Date de début (YYYY-MM-DD)'],
                        'date_fin'       => ['type' => 'string',  'description' => 'Date de fin (YYYY-MM-DD)'],
                        'limit'          => ['type' => 'integer', 'description' => 'Nombre max de résultats (défaut 10)'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_cooperatives',
                'description' => 'Lister les coopératives avec nom, code, nombre de producteurs et sections.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'search' => ['type' => 'string', 'description' => 'Recherche par nom ou code'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_stocks',
                'description' => 'État des stocks dans les magasins centraux et sections.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'cooperative_id' => ['type' => 'integer', 'description' => 'ID coopérative'],
                        'type_magasin'   => ['type' => 'string',  'description' => '"central" ou "section"'],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_formations',
                'description' => 'Informations sur les formations des producteurs et du staff.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'cooperative_id' => ['type' => 'integer', 'description' => 'ID coopérative'],
                        'limit'          => ['type' => 'integer', 'description' => 'Nombre max de résultats (défaut 10)'],
                    ],
                    'required' => [],
                ],
            ],
        ];
    }

    public function index()
    {
        $pageTitle = 'Assistant IA';
        return view('admin.aichat.index', compact('pageTitle'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        // Rebuild messages array from history + new user message
        $messages = [];
        foreach ($history as $item) {
            if (!empty($item['role']) && !empty($item['content'])) {
                $messages[] = [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = $this->getClient()->messages->create(
                model: $this->model(),
                maxTokens: 4096,
                system: $this->systemPrompt,
                tools: $this->tools,
                messages: $messages,
            );

            // Agentic loop: execute tools while Claude requests them
            while ($response->stopReason === 'tool_use') {
                $toolResults = [];
                foreach ($response->content as $block) {
                    if ($block instanceof ToolUseBlock) {
                        $toolResult = $this->executeToolCall($block->name, (array) $block->input);
                        $toolResults[] = [
                            'type' => 'tool_result',
                            'tool_use_id' => $block->id,
                            'content' => $toolResult,
                        ];
                    }
                }

                $messages[] = ['role' => 'assistant', 'content' => $response->content];
                $messages[] = ['role' => 'user', 'content' => $toolResults];

                $response = $this->getClient()->messages->create(
                    model: $this->model(),
                    maxTokens: 4096,
                    system: $this->systemPrompt,
                    tools: $this->tools,
                    messages: $messages,
                );
            }

            // Extract final text response
            $assistantText = '';
            foreach ($response->content as $block) {
                if ($block->type === 'text') {
                    $assistantText .= $block->text;
                }
            }

            // Build updated history (user message + assistant response)
            $updatedHistory = array_merge($history, [
                ['role' => 'user', 'content' => $userMessage],
                ['role' => 'assistant', 'content' => $assistantText],
            ]);

            // Keep max 20 exchanges (40 messages) to avoid context explosion
            if (count($updatedHistory) > 40) {
                $updatedHistory = array_slice($updatedHistory, -40);
            }

            return response()->json([
                'success' => true,
                'message' => $assistantText,
                'history' => $updatedHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function clearHistory()
    {
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    //  Tool implementations
    // ─────────────────────────────────────────────

    private function executeToolCall(string $name, array $input): string
    {
        try {
            return match ($name) {
                'get_statistics' => $this->toolGetStatistics(),
                'get_producteurs' => $this->toolGetProducteurs($input),
                'get_parcelles' => $this->toolGetParcelles($input),
                'get_livraisons' => $this->toolGetLivraisons($input),
                'get_cooperatives' => $this->toolGetCooperatives($input),
                'get_stocks' => $this->toolGetStocks($input),
                'get_formations' => $this->toolGetFormations($input),
                default => json_encode(['erreur' => "Outil inconnu : $name"]),
            };
        } catch (\Exception $e) {
            return json_encode(['erreur' => $e->getMessage()]);
        }
    }

    private function toolGetStatistics(): string
    {
        $stats = [
            'producteurs' => DB::table('producteurs')->count(),
            'parcelles' => DB::table('parcelles')->count(),
            'cooperatives' => DB::table('cooperatives')->count(),
            'sections' => DB::table('sections')->count(),
            'livraisons' => DB::table('livraisons')->count(),
            'menages' => DB::table('menages')->count(),
        ];

        // Superficie totale des parcelles
        $superficieTotal = DB::table('parcelles')
            ->whereNotNull('superficie')
            ->whereRaw("superficie REGEXP '^[0-9]+(\\.[0-9]+)?$'")
            ->sum(DB::raw('CAST(superficie AS DECIMAL(10,2))'));
        $stats['superficie_totale_ha'] = round($superficieTotal, 2);

        // Formations
        if (DB::getSchemaBuilder()->hasTable('formation_staffs')) {
            $stats['formations'] = DB::table('formation_staffs')->count();
        }

        return json_encode($stats);
    }

    private function toolGetProducteurs(array $input): string
    {
        $limit = min((int) ($input['limit'] ?? 10), 50);
        $query = DB::table('producteurs as p')
            ->leftJoin('cooperatives as c', 'p.cooperative_id', '=', 'c.id')
            ->leftJoin('sections as s', 'p.section_id', '=', 's.id')
            ->select(
                'p.id', 'p.nom', 'p.prenoms', 'p.codeProd', 'p.sexe',
                'p.telephone', 'p.created_at',
                'c.name as cooperative', 's.name as section'
            );

        if (!empty($input['search'])) {
            $q = '%' . $input['search'] . '%';
            $query->where(function ($w) use ($q) {
                $w->where('p.nom', 'like', $q)
                    ->orWhere('p.prenoms', 'like', $q)
                    ->orWhere('p.codeProd', 'like', $q);
            });
        }
        if (!empty($input['cooperative_id'])) {
            $query->where('p.cooperative_id', $input['cooperative_id']);
        }
        if (!empty($input['section_id'])) {
            $query->where('p.section_id', $input['section_id']);
        }

        $total = $query->count();
        $results = $query->limit($limit)->get()->toArray();

        return json_encode(['total' => $total, 'affichés' => count($results), 'producteurs' => $results]);
    }

    private function toolGetParcelles(array $input): string
    {
        $limit = min((int) ($input['limit'] ?? 10), 50);
        $query = DB::table('parcelles as pa')
            ->leftJoin('producteurs as p', 'pa.producteur_id', '=', 'p.id')
            ->leftJoin('cooperatives as c', 'p.cooperative_id', '=', 'c.id')
            ->select(
                'pa.id', 'pa.codeParc', 'pa.culture', 'pa.superficie',
                'pa.latitude', 'pa.longitude', 'pa.anneeCreation', 'pa.typedeclaration',
                'p.nom as producteur_nom', 'p.prenoms as producteur_prenoms',
                'p.codeProd', 'c.name as cooperative'
            );

        if (!empty($input['culture'])) {
            $query->where('pa.culture', 'like', '%' . $input['culture'] . '%');
        }
        if (!empty($input['cooperative_id'])) {
            $query->where('p.cooperative_id', $input['cooperative_id']);
        }
        if (!empty($input['producteur_id'])) {
            $query->where('pa.producteur_id', $input['producteur_id']);
        }

        $total = $query->count();
        $results = $query->limit($limit)->get()->toArray();

        return json_encode(['total' => $total, 'affichés' => count($results), 'parcelles' => $results]);
    }

    private function toolGetLivraisons(array $input): string
    {
        $limit = min((int) ($input['limit'] ?? 10), 50);
        $query = DB::table('livraisons as l')
            ->leftJoin('cooperatives as c', 'l.cooperative_id', '=', 'c.id')
            ->select(
                'l.id', 'l.codeLivraison', 'l.dateLivraison',
                'l.poidsTotal', 'l.montantTotal', 'l.status',
                'c.name as cooperative'
            );

        if (!empty($input['cooperative_id'])) {
            $query->where('l.cooperative_id', $input['cooperative_id']);
        }
        if (!empty($input['date_debut'])) {
            $query->where('l.dateLivraison', '>=', $input['date_debut']);
        }
        if (!empty($input['date_fin'])) {
            $query->where('l.dateLivraison', '<=', $input['date_fin']);
        }

        $total = $query->count();
        $aggregats = [
            'poids_total_kg' => $query->sum('l.poidsTotal'),
            'montant_total' => $query->sum('l.montantTotal'),
        ];
        $results = $query->orderBy('l.dateLivraison', 'desc')->limit($limit)->get()->toArray();

        return json_encode([
            'total_livraisons' => $total,
            'aggregats' => $aggregats,
            'affichés' => count($results),
            'livraisons' => $results,
        ]);
    }

    private function toolGetCooperatives(array $input): string
    {
        $query = DB::table('cooperatives as c')
            ->leftJoin('sections as s', 'c.id', '=', 's.cooperative_id')
            ->leftJoin('localites as l', 's.id', '=', 'l.section_id')
            ->leftJoin('producteurs as p', 'l.id', '=', 'p.localite_id')
            ->select(
                'c.id', 'c.name', 'c.code', 'c.address', 'c.status',
                DB::raw('COUNT(DISTINCT s.id) as nb_sections'),
                DB::raw('COUNT(DISTINCT p.id) as nb_producteurs')
            )
            ->groupBy('c.id', 'c.name', 'c.code', 'c.address', 'c.status');

        if (!empty($input['search'])) {
            $q = '%' . $input['search'] . '%';
            $query->where(function ($w) use ($q) {
                $w->where('c.name', 'like', $q)->orWhere('c.code', 'like', $q);
            });
        }

        $results = $query->get()->toArray();
        return json_encode(['cooperatives' => $results]);
    }

    private function toolGetStocks(array $input): string
    {
        $data = [];

        // Magasin central
        if (empty($input['type_magasin']) || $input['type_magasin'] === 'central') {
            $queryCentral = DB::table('stock_magasin_centrals as s')
                ->leftJoin('magasin_centrals as m', 's.magasin_central_id', '=', 'm.id')
                ->leftJoin('cooperatives as c', 'm.cooperative_id', '=', 'c.id')
                ->leftJoin('products as p', 's.product_id', '=', 'p.id')
                ->select(
                    'c.name as cooperative', 'm.name as magasin',
                    'p.name as produit', 's.quantite', 's.updated_at'
                );
            if (!empty($input['cooperative_id'])) {
                $queryCentral->where('c.id', $input['cooperative_id']);
            }
            $data['stocks_centraux'] = $queryCentral->get()->toArray();
        }

        // Magasin section
        if (empty($input['type_magasin']) || $input['type_magasin'] === 'section') {
            $querySection = DB::table('stock_magasin_sections as s')
                ->leftJoin('magasin_sections as m', 's.magasin_section_id', '=', 'm.id')
                ->leftJoin('sections as sec', 'm.section_id', '=', 'sec.id')
                ->leftJoin('cooperatives as c', 'sec.cooperative_id', '=', 'c.id')
                ->leftJoin('products as p', 's.product_id', '=', 'p.id')
                ->select(
                    'c.name as cooperative', 'sec.name as section',
                    'm.name as magasin', 'p.name as produit', 's.quantite', 's.updated_at'
                );
            if (!empty($input['cooperative_id'])) {
                $querySection->where('c.id', $input['cooperative_id']);
            }
            $data['stocks_sections'] = $querySection->get()->toArray();
        }

        return json_encode($data);
    }

    private function toolGetFormations(array $input): string
    {
        $limit = min((int) ($input['limit'] ?? 10), 50);

        if (!DB::getSchemaBuilder()->hasTable('formation_staffs')) {
            return json_encode(['message' => 'Table formations non disponible']);
        }

        $query = DB::table('formation_staffs as f')
            ->leftJoin('cooperatives as c', 'f.cooperative_id', '=', 'c.id')
            ->select(
                'f.id', 'f.titre', 'f.dateFormation', 'f.lieu',
                'f.nombreParticipants', 'c.name as cooperative'
            );

        if (!empty($input['cooperative_id'])) {
            $query->where('f.cooperative_id', $input['cooperative_id']);
        }

        $total = $query->count();
        $results = $query->orderBy('f.dateFormation', 'desc')->limit($limit)->get()->toArray();

        return json_encode(['total' => $total, 'affichés' => count($results), 'formations' => $results]);
    }
}
