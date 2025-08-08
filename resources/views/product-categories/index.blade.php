@extends('layouts.app')

@section('title', 'Catégories de Produits')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-icons.css') }}" />
<style>
  .btn-action {
    padding: 0.3rem 0.6rem;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    border-radius: 0.25rem;
  }
  .action-icons {
    display: flex;
    justify-content: center;
  }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Produits /</span> Catégories de Produits
</h4>

<!-- Alerte de succès -->
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Liste des catégories</h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
      <i class='bx bx-plus'></i> Ajouter une catégorie
    </button>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>NOM</th>
          <th>DESCRIPTION</th>
          <th>PRODUITS</th>
          <th class="text-center">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($categories as $category)
        <tr>
          <td>{{ $category->id }}</td>
          <td>{{ $category->name }}</td>
          <td>{{ $category->description ?? '-' }}</td>
          <td>
            <span class="badge bg-primary">{{ $category->products_count ?? 0 }}</span>
          </td>
          <td>
            <div class="action-icons">
              <button type="button" class="btn btn-icon btn-primary btn-action" 
                      data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" 
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                <i class='bx bx-edit-alt'></i>
              </button>
              <button type="button" class="btn btn-icon btn-danger btn-action"
                      data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                <i class='bx bx-trash'></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center">Aucune catégorie disponible</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal pour ajouter une catégorie -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter une catégorie</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('product-categories.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label for="name" class="form-label">Nom de la catégorie</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="Entrez le nom" required />
            </div>
            <div class="col-12">
              <label for="description" class="form-label">Description (optionnelle)</label>
              <textarea id="description" name="description" class="form-control" placeholder="Entrez une description"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modals pour modifier chaque catégorie -->
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier la catégorie</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('product-categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label for="edit_name{{ $category->id }}" class="form-label">Nom de la catégorie</label>
              <input type="text" id="edit_name{{ $category->id }}" name="name" class="form-control" value="{{ $category->name }}" required />
            </div>
            <div class="col-12">
              <label for="edit_description{{ $category->id }}" class="form-label">Description (optionnelle)</label>
              <textarea id="edit_description{{ $category->id }}" name="description" class="form-control">{{ $category->description }}</textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->name }}</strong> ?</p>
        <p class="text-danger small">Cette action est irréversible.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <form action="{{ route('product-categories.destroy', $category->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endforeach

@endsection

@section('page-js')
<script>
  // Activer les tooltips Bootstrap
  document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  });
</script>
@endsection
