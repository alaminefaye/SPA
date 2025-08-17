/**
 * Gestion de la recherche de menu
 * Avec support pour ignorer les accents (e et é sont équivalents)
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('navbar-search');
    const searchResults = document.getElementById('search-results');
    const searchContainer = document.getElementById('search-container');
    let debounceTimer;

    // Créer le dropdown des résultats s'il n'existe pas
    if (!searchResults) {
        const resultsDiv = document.createElement('div');
        resultsDiv.id = 'search-results';
        resultsDiv.className = 'search-results-dropdown';
        document.querySelector('#search-container').appendChild(resultsDiv);
    }

    // Gestionnaire d'événements pour la saisie dans la recherche
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();

        // Effacer le timer précédent
        clearTimeout(debounceTimer);

        // Ne pas chercher si moins de 2 caractères
        if (query.length < 2) {
            document.getElementById('search-results').innerHTML = '';
            document.getElementById('search-results').classList.remove('show');
            return;
        }

        // Débounce pour éviter trop de requêtes
        debounceTimer = setTimeout(() => {
            fetchSearchResults(query);
        }, 300);
    });

    // Gestion du focus sur l'input de recherche
    searchInput.addEventListener('focus', function() {
        if (searchInput.value.trim().length >= 2) {
            document.getElementById('search-results').classList.add('show');
        }
    });

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            document.getElementById('search-results').classList.remove('show');
        }
    });

    // Fonction pour récupérer les résultats de recherche
    function fetchSearchResults(query) {
        // Nous laissons le backend gérer la normalisation des accents
        // Le backend a déjà été mis à jour pour prendre en compte les accents
        fetch(`/api/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayResults(data.results);
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
            });
    }
    
    /**
     * Fonction d'aide pour retirer les accents d'une chaîne
     * Cette fonction est maintenant redondante car le serveur gère déjà les accents,
     * mais elle peut être utile pour la recherche client-side
     */
    function removeAccents(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    // Fonction pour afficher les résultats
    function displayResults(results) {
        const resultsDiv = document.getElementById('search-results');
        
        if (results.length === 0) {
            resultsDiv.innerHTML = '<div class="search-no-result">Aucun résultat trouvé</div>';
            resultsDiv.classList.add('show');
            return;
        }

        // Grouper les résultats par catégorie
        const groupedResults = {};
        results.forEach(item => {
            if (!groupedResults[item.category]) {
                groupedResults[item.category] = [];
            }
            groupedResults[item.category].push(item);
        });

        let html = '';
        
        // Générer le HTML pour chaque catégorie
        for (const category in groupedResults) {
            html += `<div class="search-category">${category}</div>`;
            
            groupedResults[category].forEach(item => {
                html += `
                <a href="${routeToUrl(item.route)}" class="search-item">
                    <i class="bx ${item.icon}"></i>
                    <span>${item.title}</span>
                </a>`;
            });
        }

        resultsDiv.innerHTML = html;
        resultsDiv.classList.add('show');
    }

    // Fonction pour convertir un nom de route en URL
    function routeToUrl(routeName) {
        // Cette fonction pourrait être améliorée pour utiliser laravelRoutes
        // Pour l'instant, nous utilisons une approche simple
        const routeMap = {
            'dashboard': '/dashboard',
            'seances.index': '/seances',
            'seances.a_demarrer': '/seances/a-demarrer',
            'seances.terminees': '/seances/terminees',
            'salons.index': '/salons',
            'prestations.index': '/prestations',
            'clients.index': '/clients',
            'loyalty-points.index': '/loyalty-points',
            'reservations.index': '/reservations',
            'feedbacks.index': '/feedbacks',
            'product-categories.index': '/product-categories',
            'products.index': '/products',
            'purchases.index': '/purchases',
            'activity.index': '/activity-logs',
            'login-activities.index': '/login-activities',
            'users.index': '/users',
            'roles.index': '/roles',
            'permissions.index': '/permissions',
            'reports.index': '/admin/reports',
            'reports.seances': '/admin/reports/seances',
            'reports.prestations': '/admin/reports/prestations',
            'reports.products': '/admin/reports/products',
            'qrscanner.index': '/qr-scanner'
        };

        return routeMap[routeName] || '#';
    }
});
