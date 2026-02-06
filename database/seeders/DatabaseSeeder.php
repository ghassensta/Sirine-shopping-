<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProfilSeeder::class,
            CategoryHierarchySeeder::class,
        ]);
    }
}

/**
 * Seeder pour créer des catégories hiérarchiques de test
 */
class CategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // Catégories parentes
        $categories = [
            [
                'name' => 'Éclairage',
                'slug' => 'eclairage',
                'image' => 'categories/eclairage.jpg',
                'meta_title' => 'Éclairage - Lampes et Luminaires',
                'meta_description' => 'Découvrez notre collection d\'éclairage : lampes, suspensions, appliques et plus.',
                'meta_keywords' => 'éclairage, lampes, luminaires, suspension, applique',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Lampadaires',
                        'slug' => 'lampadaires',
                        'meta_title' => 'Lampadaires - Éclairage de sol',
                        'meta_description' => 'Lampadaires design pour tous les styles d\'intérieur.',
                        'children' => [
                            [
                                'name' => 'Lampadaires LED',
                                'slug' => 'lampadaires-led',
                                'meta_title' => 'Lampadaires LED - Économiques et modernes',
                            ],
                            [
                                'name' => 'Lampadaires design',
                                'slug' => 'lampadaires-design',
                                'meta_title' => 'Lampadaires Design - Pièces uniques',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Suspensions',
                        'slug' => 'suspensions',
                        'meta_title' => 'Suspensions - Éclairage plafond',
                        'meta_description' => 'Suspensions modernes et classiques pour votre intérieur.',
                    ],
                    [
                        'name' => 'Appliques Murales',
                        'slug' => 'appliques-murales',
                        'meta_title' => 'Appliques Murales - Éclairage mural',
                    ]
                ]
            ],
            [
                'name' => 'Mobilier',
                'slug' => 'mobilier',
                'image' => 'categories/mobilier.jpg',
                'meta_title' => 'Mobilier - Meubles design et fonctionnels',
                'meta_description' => 'Meubles pour salon, chambre, cuisine : tables, chaises, étagères.',
                'meta_keywords' => 'mobilier, meubles, table, chaise, étagère, salon',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Tables',
                        'slug' => 'tables',
                        'meta_title' => 'Tables - Tables à manger, tables basses',
                        'children' => [
                            [
                                'name' => 'Tables à manger',
                                'slug' => 'tables-a-manger',
                                'meta_title' => 'Tables à manger - Salle à repas',
                            ],
                            [
                                'name' => 'Tables basses',
                                'slug' => 'tables-basses',
                                'meta_title' => 'Tables basses - Salon',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Chaises',
                        'slug' => 'chaises',
                        'meta_title' => 'Chaises - Sièges design et confortables',
                    ],
                    [
                        'name' => 'Rangements',
                        'slug' => 'rangements',
                        'meta_title' => 'Rangements - Étagères, commodes, bibliothèques',
                    ]
                ]
            ],
            [
                'name' => 'Décoration',
                'slug' => 'decoration',
                'image' => 'categories/decoration.jpg',
                'meta_title' => 'Décoration - Objets déco et accessoires',
                'meta_description' => 'Accessoires de décoration : vases, cadres, miroirs, coussins.',
                'meta_keywords' => 'décoration, objets déco, vases, cadres, miroirs',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Vases et Pots',
                        'slug' => 'vases-et-pots',
                        'meta_title' => 'Vases et Pots - Décoration florale',
                    ],
                    [
                        'name' => 'Cadres et Tableaux',
                        'slug' => 'cadres-et-tableaux',
                        'meta_title' => 'Cadres et Tableaux - Décoration murale',
                    ],
                    [
                        'name' => 'Miroirs',
                        'slug' => 'miroirs',
                        'meta_title' => 'Miroirs - Décoration et fonctionnalité',
                    ]
                ]
            ],
            [
                'name' => 'Textile',
                'slug' => 'textile',
                'image' => 'categories/textile.jpg',
                'meta_title' => 'Textile - Rideaux, coussins, tapis',
                'meta_description' => 'Textiles pour la maison : rideaux, coussins, tapis, linge de maison.',
                'meta_keywords' => 'textile, rideaux, coussins, tapis, linge de maison',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Rideaux et Voilages',
                        'slug' => 'rideaux-et-voilages',
                        'meta_title' => 'Rideaux et Voilages - Décoration fenêtre',
                    ],
                    [
                        'name' => 'Coussins et Plaids',
                        'slug' => 'coussins-et-plaids',
                        'meta_title' => 'Coussins et Plaids - Confort et style',
                    ],
                    [
                        'name' => 'Tapis',
                        'slug' => 'tapis',
                        'meta_title' => 'Tapis - Sol et confort',
                    ]
                ]
            ]
        ];
        
        foreach ($categories as $categoryData) {
            $this->createCategoryWithChildren($categoryData);
        }
        
        $this->command->info('✅ Catégories hiérarchiques créées avec succès !');
    }
    
    private function createCategoryWithChildren(array $data, ?int $parentId = null): Category
    {
        $children = $data['children'] ?? null;
        unset($data['children']);
        
        $data['parent_id'] = $parentId;
        $data['slug'] = Str::slug($data['name']);
        
        $category = Category::create($data);
        
        if ($children && is_array($children)) {
            foreach ($children as $childData) {
                $this->createCategoryWithChildren($childData, $category->id);
            }
        }
        
        return $category;
    }
}
