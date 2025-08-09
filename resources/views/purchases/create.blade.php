@extends('layouts.app')

@section('title', 'Nouvel Achat')

@section('page-css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Styles pour la recherche de produits */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + 0.75rem + 2px);
    }
    .product-item {
        display: flex;
        justify-content: space-between;
    }
    .product-info {
        flex-grow: 1;
    }
    .product-stock {
        text-align: right;
        white-space: nowrap;
        padding-left: 10px;
    }
    .product-low-stock {
        color: #dc3545;
        font-weight: bold;
    }
</style>

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Nouvel Achat</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Enregistrer un nouvel achat</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telephone">Recherche client par téléphone</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Numéro de téléphone du client">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="searchClient">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Recherchez un client existant ou saisissez un nouveau numéro</small>
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <input type="hidden" id="client_id" name="client_id" value="{{ old('client_id') }}">
                        
                        <div class="form-group">
                            <label for="nom_complet">Nom complet du client</label>
                            <input type="text" class="form-control" id="nom_complet" name="nom_complet" value="{{ old('nom_complet') }}" placeholder="Nom complet du client (auto-complété si client existant)">
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse_mail">Adresse e-mail</label>
                            <input type="email" class="form-control" id="adresse_mail" name="adresse_mail" value="{{ old('adresse_mail') }}" placeholder="Adresse e-mail du client (auto-complété si client existant)">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Méthode de paiement <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Sélectionner une méthode de paiement</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>
                                        @switch($method)
                                            @case('cash')
                                                Espèces
                                                @break
                                            @case('wave')
                                                WAVE
                                                @break
                                            @case('orange_money')
                                                Orange Money
                                                @break
                                            @default
                                                {{ $method }}
                                        @endswitch
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Produits</h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="products-table">
                        <thead>
                            <tr>
                                <th style="width: 40%">Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Sous-total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les lignes de produits seront ajoutées ici dynamiquement -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                <td id="total-amount" class="font-weight-bold">0.00 FCFA</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-success mb-4" id="add-product">
                    <i class="fas fa-plus-circle mr-1"></i> Ajouter un produit
                </button>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submit-purchase">
                        <i class="fas fa-save mr-1"></i> Enregistrer l'achat
                    </button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modèle pour une nouvelle ligne de produit -->
<template id="product-row-template">
    <tr class="product-row">
        <td>
            <select class="form-control product-select product-search" name="items[__INDEX__][product_id]" required>
                <option value="">Rechercher un produit...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                        {{ $product->name }} - Stock: {{ $product->stock }} {{ $product->isLowStock() ? '⚠️' : '' }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control unit-price" readonly>
                <div class="input-group-append">
                    <span class="input-group-text">FCFA</span>
                </div>
            </div>
        </td>
        <td>
            <input type="number" class="form-control quantity" name="items[__INDEX__][quantity]" min="1" value="1" required>
            <small class="text-muted stock-info"></small>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control subtotal" readonly>
                <div class="input-group-append">
                    <span class="input-group-text">FCFA</span>
                </div>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-icon btn-danger remove-product"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@section('page-js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let productIndex = 0;
        
        // Initialiser les tooltips Bootstrap
        initTooltips();
        
        // Recherche client par téléphone
        $('#searchClient').click(function() {
            const telephone = $('#telephone').val();
            
            if (!telephone) {
                alert('Veuillez saisir un numéro de téléphone');
                return;
            }
            
            // Appel AJAX pour rechercher le client
            $.ajax({
                url: '{{ route("clients.searchByPhone") }}',
                type: 'GET',
                data: { phone: telephone },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Client trouvé, remplir les champs
                        $('#client_id').val(response.client.id);
                        $('#nom_complet').val(response.client.nom_complet);
                        $('#adresse_mail').val(response.client.adresse_mail);
                        
                        alert('Client trouvé et informations remplies automatiquement');
                    } else {
                        // Client non trouvé
                        $('#client_id').val('');
                        $('#nom_complet').val('');
                        $('#adresse_mail').val('');
                        
                        alert(response.message + '. Veuillez compléter les informations du client.');
                        $('#nom_complet').focus();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la recherche du client');
                }
            });
        });
        
        // Fonction pour ajouter une nouvelle ligne de produit
        $('#add-product').click(function() {
            const template = $('#product-row-template').html();
            const newRow = template.replace(/__INDEX__/g, productIndex++);
            $('#products-table tbody').append(newRow);
            
            // Initialiser les événements pour la nouvelle ligne
            initRowEvents($('#products-table tbody tr:last'));
            
            // Réinitialiser les tooltips pour les nouveaux éléments
            initTooltips();
        });
        
        // Fonction pour initialiser les tooltips Bootstrap
        function initTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
        
        // Fonction pour initialiser les événements d'une ligne
        function initRowEvents(row) {
            // Initialiser Select2 pour la recherche de produit
            $(row).find('.product-search').select2({
                theme: 'bootstrap-5',
                placeholder: 'Rechercher un produit...',
                width: '100%',
                language: {
                    noResults: function() {
                        return "Aucun produit trouvé";
                    },
                    searching: function() {
                        return "Recherche en cours...";
                    }
                },
                templateResult: formatProduct,
                templateSelection: formatProductSelection,
                minimumInputLength: 1, // Exige au moins 1 caractère avant d'afficher des résultats
                minimumResultsForSearch: Infinity // Masque la barre de recherche interne de Select2
            });
            
            // Changement de produit
            $(row).find('.product-select').change(function() {
                const option = $(this).find('option:selected');
                const price = parseFloat(option.data('price')) || 0;
                const stock = parseInt(option.data('stock')) || 0;
                
                $(row).find('.unit-price').val(price.toFixed(2));
                $(row).find('.stock-info').text(`Stock disponible: ${stock}`);
                
                // Limiter la quantité au stock disponible
                $(row).find('.quantity').attr('max', stock);
                
                // Recalculer le sous-total
                updateRowTotal(row);
            });
            
            // Changement de quantité
            $(row).find('.quantity').on('input', function() {
                updateRowTotal(row);
            });
            
            // Supprimer la ligne
            $(row).find('.remove-product').click(function() {
                $(row).remove();
                updateTotalAmount();
            });
        }
        
        // Mettre à jour le sous-total d'une ligne
        function updateRowTotal(row) {
            const unitPrice = parseFloat($(row).find('.unit-price').val()) || 0;
            const quantity = parseInt($(row).find('.quantity').val()) || 0;
            const subtotal = unitPrice * quantity;
            
            $(row).find('.subtotal').val(subtotal.toFixed(2));
            
            updateTotalAmount();
        }
        
        // Mettre à jour le montant total
        function updateTotalAmount() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            
            $('#total-amount').text(total.toFixed(2) + ' FCFA');
        }
        
        // Validation avant soumission
        $('#purchase-form').submit(function(event) {
            if ($('#products-table tbody .product-row').length === 0) {
                alert('Veuillez ajouter au moins un produit à l\'achat.');
                event.preventDefault();
                return false;
            }
            
            // Vérifier que toutes les lignes ont un produit et une quantité valides
            let isValid = true;
            $('#products-table tbody .product-row').each(function() {
                const productId = $(this).find('.product-select').val();
                const quantity = parseInt($(this).find('.quantity').val()) || 0;
                const maxQuantity = parseInt($(this).find('.quantity').attr('max')) || 0;
                
                if (!productId || quantity <= 0) {
                    isValid = false;
                    return false;
                }
                
                if (quantity > maxQuantity) {
                    alert(`La quantité demandée dépasse le stock disponible pour un produit (max: ${maxQuantity}).`);
                    isValid = false;
                    return false;
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                return false;
            }
        });
        
        // Ajouter une première ligne de produit automatiquement
        $('#add-product').trigger('click');
        
        // Fonction pour formater l'affichage des produits dans la liste déroulante
        function formatProduct(product) {
            if (!product.id) {
                return product.text;
            }
            
            const $option = $(product.element);
            const price = parseFloat($option.data('price')) || 0;
            const stock = parseInt($option.data('stock')) || 0;
            const isLowStock = stock <= 5;
            
            const $container = $('<div class="product-item"></div>');
            
            const $productInfo = $('<div class="product-info"></div>');
            $productInfo.text(product.text.split(' - ')[0]); // Affiche uniquement le nom du produit
            
            const $stockInfo = $('<div class="product-stock"></div>');
            if (isLowStock) {
                $stockInfo.addClass('product-low-stock');
                $stockInfo.html(`Stock: ${stock} ⚠️`);
            } else {
                $stockInfo.text(`Stock: ${stock}`);
            }
            
            $container.append($productInfo);
            $container.append($stockInfo);
            
            return $container;
        }
        
        // Fonction pour formater l'affichage des produits sélectionnés
        function formatProductSelection(product) {
            if (!product.id) {
                return product.text;
            }
            
            // Retourne uniquement le nom du produit sans les informations de stock
            return product.text.split(' - ')[0];
        }
    });
</script>
@endsection
