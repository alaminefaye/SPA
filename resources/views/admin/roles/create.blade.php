@extends('layouts.app')

@section('title', 'Créer un Rôle')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Créer un Nouveau Rôle</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du rôle</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                        <label class="form-check-label" for="select-all">
                                            <strong>Sélectionner / Désélectionner tout</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @foreach($permissions as $group => $groupPermissions)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <div class="form-check">
                                                <input class="form-check-input group-select" type="checkbox" id="group-{{ $group }}">
                                                <label class="form-check-label" for="group-{{ $group }}">
                                                    <strong>{{ ucfirst($group) }}</strong>
                                                </label>
                                            </div>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($groupPermissions as $permission)
                                                <div class="col-md-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" 
                                                               type="checkbox" 
                                                               id="permission-{{ $permission->id }}" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->id }}"
                                                               data-group="{{ $group }}"
                                                               {{ is_array(old('permissions')) && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @error('permissions')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer le rôle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Sélection/Désélection de toutes les permissions
        $('#select-all').on('change', function() {
            $('.permission-checkbox').prop('checked', $(this).is(':checked'));
            $('.group-select').prop('checked', $(this).is(':checked'));
        });

        // Sélection/Désélection par groupe
        $('.group-select').on('change', function() {
            const group = $(this).attr('id').replace('group-', '');
            $('.permission-checkbox[data-group="' + group + '"]').prop('checked', $(this).is(':checked'));
            updateSelectAllCheckbox();
        });

        // Mise à jour de la checkbox "Sélectionner tout" basée sur les permissions sélectionnées
        $('.permission-checkbox').on('change', function() {
            const group = $(this).data('group');
            const totalInGroup = $('.permission-checkbox[data-group="' + group + '"]').length;
            const checkedInGroup = $('.permission-checkbox[data-group="' + group + '"]:checked').length;
            
            $('#group-' + group).prop('checked', checkedInGroup === totalInGroup);
            updateSelectAllCheckbox();
        });

        function updateSelectAllCheckbox() {
            const total = $('.permission-checkbox').length;
            const checked = $('.permission-checkbox:checked').length;
            $('#select-all').prop('checked', total === checked);
        }

        // Initialiser l'état des checkboxes de groupe
        $('.group-select').each(function() {
            const group = $(this).attr('id').replace('group-', '');
            const totalInGroup = $('.permission-checkbox[data-group="' + group + '"]').length;
            const checkedInGroup = $('.permission-checkbox[data-group="' + group + '"]:checked').length;
            
            $(this).prop('checked', checkedInGroup === totalInGroup);
        });

        // Initialiser l'état de la checkbox "Sélectionner tout"
        updateSelectAllCheckbox();
    });
</script>
@endsection
