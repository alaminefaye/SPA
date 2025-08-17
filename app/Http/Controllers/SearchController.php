<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /**
     * Recherche des éléments de menu et des pages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // Structure de tous les menus avec leurs routes
        $menuItems = $this->getMenuItems();
        
        // Normaliser la requête (retirer les accents)
        $normalizedQuery = $this->removeAccents(strtolower($query));
        
        // Recherche des correspondances
        $results = $menuItems->filter(function ($item) use ($normalizedQuery) {
            // Normaliser le titre pour la recherche
            $normalizedTitle = $this->removeAccents(strtolower($item['title']));
            return Str::contains($normalizedTitle, $normalizedQuery);
        })->values()->take(7);

        return response()->json(['results' => $results]);
    }
    
    /**
     * Retire les accents d'une chaîne de caractères
     *
     * @param  string  $string
     * @return string
     */
    private function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Décomposer les caractères accentués
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y'
        ];
        
        return strtr($string, $chars);
    }

    /**
     * Obtient tous les éléments de menu disponibles.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getMenuItems()
    {
        return collect([
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'bx-home-circle',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Gestion des séances',
                'route' => 'seances.index',
                'icon' => 'bx-calendar',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Liste des séances',
                'route' => 'seances.index',
                'icon' => 'bx-calendar',
                'category' => 'Séances'
            ],
            [
                'title' => 'Séances à démarrer',
                'route' => 'seances.a_demarrer',
                'icon' => 'bx-calendar',
                'category' => 'Séances'
            ],
            [
                'title' => 'Séances terminées',
                'route' => 'seances.terminees',
                'icon' => 'bx-calendar',
                'category' => 'Séances'
            ],
            [
                'title' => 'Gestion des salons',
                'route' => 'salons.index',
                'icon' => 'bx-building',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Services et prestations',
                'route' => 'prestations.index',
                'icon' => 'bx-timer',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Gestion clients',
                'route' => 'clients.index',
                'icon' => 'bx-user',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Liste des clients',
                'route' => 'clients.index',
                'icon' => 'bx-user',
                'category' => 'Clients'
            ],
            [
                'title' => 'Points de fidélité',
                'route' => 'loyalty-points.index',
                'icon' => 'bx-star',
                'category' => 'Clients'
            ],
            [
                'title' => 'Tous les réservations',
                'route' => 'reservations.index',
                'icon' => 'bx-calendar-check',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Suggestions et préoccupations',
                'route' => 'feedbacks.index',
                'icon' => 'bx-message-alt-dots',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Gestion des produits',
                'route' => 'products.index',
                'icon' => 'bx-package',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Catégories de produits',
                'route' => 'product-categories.index',
                'icon' => 'bx-category',
                'category' => 'Produits'
            ],
            [
                'title' => 'Produits',
                'route' => 'products.index',
                'icon' => 'bx-package',
                'category' => 'Produits'
            ],
            [
                'title' => 'Achats',
                'route' => 'purchases.index',
                'icon' => 'bx-cart',
                'category' => 'Produits'
            ],
            [
                'title' => 'Gestion d\'activité',
                'route' => 'activity.index',
                'icon' => 'bx-history',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Journal d\'activité',
                'route' => 'activity.index',
                'icon' => 'bx-history',
                'category' => 'Activité'
            ],
            [
                'title' => 'Activités de connexion',
                'route' => 'login-activities.index',
                'icon' => 'bx-log-in',
                'category' => 'Activité'
            ],
            [
                'title' => 'Gestion des utilisateurs',
                'route' => 'users.index',
                'icon' => 'bx-user',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Rôles et permissions',
                'route' => 'roles.index',
                'icon' => 'bx-key',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Rôles',
                'route' => 'roles.index',
                'icon' => 'bx-key',
                'category' => 'Permissions'
            ],
            [
                'title' => 'Permissions',
                'route' => 'permissions.index',
                'icon' => 'bx-lock',
                'category' => 'Permissions'
            ],
            [
                'title' => 'Rapports et statistiques',
                'route' => 'reports.index',
                'icon' => 'bx-line-chart',
                'category' => 'Menu principal'
            ],
            [
                'title' => 'Vue d\'ensemble',
                'route' => 'reports.index',
                'icon' => 'bx-line-chart',
                'category' => 'Rapports'
            ],
            [
                'title' => 'Séances (Rapports)',
                'route' => 'reports.seances',
                'icon' => 'bx-line-chart',
                'category' => 'Rapports'
            ],
            [
                'title' => 'Prestations (Rapports)',
                'route' => 'reports.prestations',
                'icon' => 'bx-line-chart',
                'category' => 'Rapports'
            ],
            [
                'title' => 'Ventes de produits',
                'route' => 'reports.products',
                'icon' => 'bx-line-chart',
                'category' => 'Rapports'
            ],
            [
                'title' => 'Scanner QR Code',
                'route' => 'qrscanner.index',
                'icon' => 'bx-qr-scan',
                'category' => 'Menu principal'
            ]
        ]);
    }
}
