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
                'name' => 'Art de Table',
                'slug' => 'art-de-table',
                'image' => 'categories/art-de-table.jpg',
                'meta_title' => 'Art de Table - Service à table et verrerie',
                'meta_description' => 'Découvrez notre collection d\'art de table : services à table, verrerie, ménagères et plateaux.',
                'meta_keywords' => 'art de table, service à table, verrerie, ménagère, plateaux',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Service à table',
                        'slug' => 'service-a-table',
                        'meta_title' => 'Service à table - Vaisselle et assiettes',
                        'meta_description' => 'Services à table complets pour toutes les occasions.',
                    ],
                    [
                        'name' => 'Verrerie',
                        'slug' => 'verrerie',
                        'meta_title' => 'Verrerie - Verres et services',
                        'meta_description' => 'Verrerie élégante : verres à vin, à eau et plus.',
                        'children' => [
                            [
                                'name' => 'Service café',
                                'slug' => 'service-cafe',
                                'meta_title' => 'Service café - Tasses et cafetières',
                            ],
                            [
                                'name' => 'Service thé',
                                'slug' => 'service-the',
                                'meta_title' => 'Service thé - Théières et tasses',
                            ],
                            [
                                'name' => 'Service à Eau',
                                'slug' => 'service-a-eau',
                                'meta_title' => 'Service à Eau - Carafes et verres',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Coffret Ménagère',
                        'slug' => 'coffret-menagere',
                        'meta_title' => 'Coffret Ménagère - Couverts complets',
                        'meta_description' => 'Coffrets de couverts élégants et durables.',
                    ],
                    [
                        'name' => 'Plateaux',
                        'slug' => 'plateaux',
                        'meta_title' => 'Plateaux - Service et décoration',
                    ],
                    [
                        'name' => 'Bonbonières',
                        'slug' => 'bonbonieres',
                        'meta_title' => 'Bonbonières - Contenants élégants',
                    ]
                ]
            ],
            [
                'name' => 'Cuisine',
                'slug' => 'cuisine',
                'image' => 'categories/cuisine.jpg',
                'meta_title' => 'Cuisine - Ustensiles et accessoires',
                'meta_description' => 'Tout pour votre cuisine : ustensiles, batteries de cuisine, porte-épices.',
                'meta_keywords' => 'cuisine, ustensiles, batterie de cuisine, porte épice',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Porte épice',
                        'slug' => 'porte-epice',
                        'meta_title' => 'Porte épice - Rangement épices',
                    ],
                    [
                        'name' => 'Ustensile de cuisine',
                        'slug' => 'ustensile-de-cuisine',
                        'meta_title' => 'Ustensile de cuisine - Outils de cuisson',
                    ],
                    [
                        'name' => 'Batterie de cuisine',
                        'slug' => 'batterie-de-cuisine',
                        'meta_title' => 'Batterie de cuisine - Casseroles et poêles',
                    ]
                ]
            ],
            [
                'name' => 'Décoration',
                'slug' => 'decoration',
                'image' => 'categories/decoration.jpg',
                'meta_title' => 'Décoration - Objets déco et accessoires',
                'meta_description' => 'Accessoires de décoration : vases, statues, porte-bougies, boîtes de rangement.',
                'meta_keywords' => 'décoration, vase, statue, porte bougie, décoration murale',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Porte bougie',
                        'slug' => 'porte-bougie',
                        'meta_title' => 'Porte bougie - Ambiance chaleureuse',
                    ],
                    [
                        'name' => 'Vase',
                        'slug' => 'vase',
                        'meta_title' => 'Vase - Décoration florale',
                    ],
                    [
                        'name' => 'Statues',
                        'slug' => 'statues',
                        'meta_title' => 'Statues - Décoration artistique',
                    ],
                    [
                        'name' => 'Boite de rangement',
                        'slug' => 'boite-de-rangement',
                        'meta_title' => 'Boite de rangement - Organisation déco',
                    ],
                    [
                        'name' => 'Décoration murale',
                        'slug' => 'decoration-murale',
                        'meta_title' => 'Décoration murale - Tableaux et cadres',
                    ]
                ]
            ],
            [
                'name' => 'Luminaire',
                'slug' => 'luminaire',
                'image' => 'categories/luminaire.jpg',
                'meta_title' => 'Luminaire - Lustres et veilleuses',
                'meta_description' => 'Éclairage pour votre maison : lustres et veilleuses.',
                'meta_keywords' => 'luminaire, lustre, veilleuse, éclairage',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Lustres',
                        'slug' => 'lustres',
                        'meta_title' => 'Lustres - Éclairage suspendu',
                    ],
                    [
                        'name' => 'Veilleuse',
                        'slug' => 'veilleuse',
                        'meta_title' => 'Veilleuse - Éclairage doux',
                    ]
                ]
            ],
            [
                'name' => 'Linge De Maison',
                'slug' => 'linge-de-maison',
                'image' => 'categories/linge-de-maison.jpg',
                'meta_title' => 'Linge De Maison - Parures de lit et couvertures',
                'meta_description' => 'Linge de maison : parures de lit, couvertures et textiles.',
                'meta_keywords' => 'linge de maison, parure de lit, couverture',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Parure de lit',
                        'slug' => 'parure-de-lit',
                        'meta_title' => 'Parure de lit - Draps et housses',
                    ],
                    [
                        'name' => 'Couverture',
                        'slug' => 'couverture',
                        'meta_title' => 'Couverture - Chaleur et confort',
                    ]
                ]
            ],
            [
                'name' => 'Salle de Bain',
                'slug' => 'salle-de-bain',
                'image' => 'categories/salle-de-bain.jpg',
                'meta_title' => 'Salle de Bain - Accessoires et textiles',
                'meta_description' => 'Accessoires de salle de bain : séries complètes, tapis et accessoires.',
                'meta_keywords' => 'salle de bain, accessoires, tapis, série salle de bain',
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Série salle de bain',
                        'slug' => 'serie-salle-de-bain',
                        'meta_title' => 'Série salle de bain - Ensembles coordonnés',
                    ],
                    [
                        'name' => 'Accessoires salle de bain',
                        'slug' => 'accessoires-salle-de-bain',
                        'meta_title' => 'Accessoires salle de bain - Pratiques et déco',
                    ],
                    [
                        'name' => 'Tapis salle de bain',
                        'slug' => 'tapis-salle-de-bain',
                        'meta_title' => 'Tapis salle de bain - Confort et sécurité',
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
