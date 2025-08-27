@extends('layouts.app')

@section('title', 'Liste de présence des employés')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Liste de présence</h1>
        <div>
            <a href="{{ route('attendance.calendar') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-alt"></i> Vue calendrier
            </a>
            <a href="{{ route('attendance.report') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-bar"></i> Rapports
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-clipboard-list me-1"></i>
                Présence du {{ $date->format('d/m/Y') }}
            </div>
            <div class="d-flex">
                <form method="GET" action="{{ route('attendance.index') }}" class="d-flex align-items-center me-2">
                    <div class="input-group me-2" style="width: 220px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="attendanceTable">
                    <thead>
                        <tr>
                            <th width="3%">Photo</th>
                            <th width="15%">Employé</th>
                            <th width="10%">Poste</th>
                            <th width="12%">Salon</th>
                            <th width="10%">Statut</th>
                            <th width="12%">Heure d'arrivée</th>
                            <th width="12%">Heure de départ</th>
                            <th width="15%">Notes</th>
                            <th width="11%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        @php
                            $attendance = $attendances->get($employee->id);
                            $present = $attendance && $attendance->present;
                        @endphp
                        <tr class="{{ $present ? 'table-success' : '' }}">
                            <td class="text-center">
                                @if($employee->photo)
                                <img src="{{ Storage::url($employee->photo) }}" alt="Photo de {{ $employee->nom_complet }}" 
                                    class="rounded-circle" width="40" height="40">
                                @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                    style="width: 40px; height: 40px; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </td>
                            <td>{{ $employee->prenom }} {{ $employee->nom }}</td>
                            <td>{{ $employee->poste }}</td>
                            <td>{{ $employee->salon ? $employee->salon->nom : 'Non assigné' }}</td>
                            <td>
                                @if($attendance)
                                    <span class="badge {{ $attendance->present ? 'bg-success' : 'bg-danger' }}">
                                        {{ $attendance->present ? 'Présent' : 'Absent' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Non renseigné</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance && $attendance->arrival_time)
                                    {{ \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance && $attendance->departure_time)
                                    {{ \Carbon\Carbon::parse($attendance->departure_time)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $attendance->notes ?? '' }}
                            </td>
                            <td>
                                @if(!$attendance || !$attendance->present)
                                    <button type="button" class="btn btn-sm btn-success" 
                                        onclick="ouvrirModalPresent({{ $employee->id }}, '{{ $date->format('Y-m-d') }}')">
                                        <i class="fas fa-user-check"></i> Présent
                                    </button>
                                @endif
                                
                                @if($attendance && $attendance->present && !$attendance->departure_time)
                                    <button type="button" class="btn btn-sm btn-warning" 
                                        onclick="ouvrirModalDepart({{ $employee->id }}, '{{ $date->format('Y-m-d') }}')">
                                        <i class="fas fa-sign-out-alt"></i> Départ
                                    </button>
                                @endif
                                
                                @if($attendance)
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="marquerAbsent({{ $employee->id }}, '{{ $date->format('Y-m-d') }}')">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucun employé actif trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour marquer la présence -->
<div class="modal fade" id="markPresentModal" tabindex="-1" aria-labelledby="markPresentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPresentModalLabel">Marquer la présence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="markPresentForm" action="{{ route('attendance.mark') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="presentEmployeeId">
                    <input type="hidden" name="date" id="presentDate">
                    <input type="hidden" name="present" value="1">
                    
                    <div class="mb-3">
                        <label for="arrivalTime" class="form-label">Heure d'arrivée</label>
                        <input type="time" class="form-control" id="arrivalTime" name="arrival_time" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour marquer le départ -->
<div class="modal fade" id="markDepartureModal" tabindex="-1" aria-labelledby="markDepartureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markDepartureModalLabel">Marquer le départ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="markDepartureForm" action="{{ route('attendance.departure') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="departureEmployeeId">
                    <input type="hidden" name="date" id="departureDate">
                    
                    <div class="mb-3">
                        <label for="departureTime" class="form-label">Heure de départ</label>
                        <input type="time" class="form-control" id="departureTime" name="departure_time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    // Configuration globale pour AJAX avec CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Fonctions pour ouvrir les modals et définir les valeurs des champs
    function ouvrirModalPresent(employeeId, date) {
        console.log('Ouverture du modal de présence pour:', employeeId, date);
        
        // Définir les valeurs des champs cachés avant d'ouvrir le modal
        document.getElementById('presentEmployeeId').value = employeeId;
        document.getElementById('presentDate').value = date;
        
        // Vérifier que les valeurs ont bien été définies
        console.log('Valeurs définies - ID:', document.getElementById('presentEmployeeId').value);
        console.log('Valeurs définies - Date:', document.getElementById('presentDate').value);
        
        // Ouvrir le modal
        var modal = new bootstrap.Modal(document.getElementById('markPresentModal'));
        modal.show();
    }
    
    function ouvrirModalDepart(employeeId, date) {
        console.log('Ouverture du modal de départ pour:', employeeId, date);
        
        // Définir les valeurs des champs cachés avant d'ouvrir le modal
        document.getElementById('departureEmployeeId').value = employeeId;
        document.getElementById('departureDate').value = date;
        
        // Vérifier que les valeurs ont bien été définies
        console.log('Valeurs définies - ID:', document.getElementById('departureEmployeeId').value);
        console.log('Valeurs définies - Date:', document.getElementById('departureDate').value);
        
        // Ouvrir le modal
        var modal = new bootstrap.Modal(document.getElementById('markDepartureModal'));
        modal.show();
    }
    
    // Fonction pour marquer un employé comme absent (sans modal)
    function marquerAbsent(employeeId, date) {
        console.log('Marquer absent:', employeeId, date);
        
        if (confirm('\u00cates-vous s\u00fbr de vouloir marquer cet employ\u00e9 comme absent ?')) {
            // Créer un formulaire dynamique avec les données nécessaires
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("attendance.mark") }}';
            form.style.display = 'none';
            
            // Ajouter le token CSRF
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Ajouter l'ID de l'employé
            var employeeIdInput = document.createElement('input');
            employeeIdInput.type = 'hidden';
            employeeIdInput.name = 'employee_id';
            employeeIdInput.value = employeeId;
            form.appendChild(employeeIdInput);
            
            // Ajouter la date
            var dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'date';
            dateInput.value = date;
            form.appendChild(dateInput);
            
            // Ajouter le statut absent (present = 0)
            var presentInput = document.createElement('input');
            presentInput.type = 'hidden';
            presentInput.name = 'present';
            presentInput.value = '0';
            form.appendChild(presentInput);
            
            // Ajouter le formulaire au document et le soumettre
            document.body.appendChild(form);
            
            // Log pour déboguer
            console.log('Soumission du formulaire d\'absence:', {
                'employee_id': employeeId,
                'date': date,
                'present': 0
            });
            
            form.submit();
        }
    }
    
    $(document).ready(function() {
        

        // Initialiser DataTables
        $('#attendanceTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            "paging": false,
            "info": false,
            "order": [[4, 'desc'], [1, 'asc']], // Trier par statut (présent) puis par nom
        });
        
        // Définir l'heure d'arrivée par défaut à l'heure actuelle
        $('#arrivalTime').val(new Date().toTimeString().slice(0, 5));
        $('#departureTime').val(new Date().toTimeString().slice(0, 5));
        
        // Vérifier le contenu des champs avant l'ouverture des modals
        $('#markPresentModal').on('show.bs.modal', function() {
            var employeeId = document.getElementById('presentEmployeeId').value;
            var date = document.getElementById('presentDate').value;
            console.log('Modal présence - Vérification des champs: employeeId=', employeeId, 'date=', date);
        });
        
        $('#markDepartureModal').on('show.bs.modal', function() {
            var employeeId = document.getElementById('departureEmployeeId').value;
            var date = document.getElementById('departureDate').value;
            console.log('Modal départ - Vérification des champs: employeeId=', employeeId, 'date=', date);
        });
        
        // Réactivation de la soumission AJAX avec débogage visuel amélioré
        $('#markPresentForm').on('submit', function(e) {
            e.preventDefault();
            
            // Vérifier que les champs cachés ont bien des valeurs avant soumission
            var employeeId = $('#presentEmployeeId').val();
            var date = $('#presentDate').val();
            
            console.log('Soumission du formulaire de présence - Vérification des champs:');
            console.log('employeeId =', employeeId);
            console.log('date =', date);
            console.log('Formulaire complet:', $(this).serialize());
            
            if (!employeeId || !date) {
                alert('Erreur: ID employé ou date manquant. Veuillez réessayer.');
                console.error('Erreur de soumission - employeeId:', employeeId, 'date:', date);
                return false;
            }
            
            // Essayons en désactivant temporairement AJAX pour voir si le formulaire standard fonctionne
            // Retirer cette ligne pour réactiver AJAX après débogage
            return true;
            
            var formData = $(this).serialize();
            
            // Afficher des informations de débogage directement dans l'interface
            var debugInfo = $('<div class="alert alert-info mt-3">').html(
                '<strong>Débogage:</strong><br>' +
                'URL: ' + $(this).attr('action') + '<br>' +
                'Données: ' + formData
            );
            
            // Ajouter l'information de débogage au modal
            $('.modal-body').append(debugInfo);
            
            console.log('Formulaire soumis:', formData);
            console.log('URL d\'action:', $(this).attr('action'));
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function(xhr) {
                    // Assurer que le token CSRF est inclus
                    var token = $('meta[name="csrf-token"]').attr('content');
                    if (token) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }
                },
                success: function(response) {
                    console.log('Succès:', response);
                    debugInfo.removeClass('alert-info').addClass('alert-success').html(
                        '<strong>Succès:</strong><br>' + JSON.stringify(response)
                    );
                    setTimeout(function() {
                        $('#markPresentModal').modal('hide');
                        window.location.reload();
                    }, 2000); // Attendre 2 secondes pour voir le message de succès
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', status, error);
                    console.error('Réponse:', xhr.responseText);
                    debugInfo.removeClass('alert-info').addClass('alert-danger').html(
                        '<strong>Erreur:</strong><br>' +
                        'Status: ' + status + '<br>' +
                        'Error: ' + error + '<br>' +
                        'Response: ' + xhr.responseText
                    );
                }
            });
        });
        
        
        // Gérer la soumission du formulaire de départ
        $('#markDepartureForm').on('submit', function(e) {
            e.preventDefault();
            
            // Vérifier que les champs cachés ont bien des valeurs avant soumission
            var employeeId = $('#departureEmployeeId').val();
            var date = $('#departureDate').val();
            
            console.log('Soumission du formulaire de départ - Vérification des champs:');
            console.log('employeeId =', employeeId);
            console.log('date =', date);
            console.log('Formulaire complet:', $(this).serialize());
            
            if (!employeeId || !date) {
                alert('Erreur: ID employé ou date manquant. Veuillez réessayer.');
                console.error('Erreur de soumission départ - employeeId:', employeeId, 'date:', date);
                return false;
            }
            
            // Désactiver temporairement AJAX pour déboguer
            return true;
            console.log('URL d\'action départ:', $(this).attr('action'));
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Succès départ:', response);
                    $('#markDepartureModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur départ:', status, error);
                    console.error('Réponse départ:', xhr.responseText);
                    alert('Erreur lors de l\'enregistrement du départ. Consultez la console pour plus de détails.');
                }
            });
        });
        
        // Le gestionnaire d'événement pour le bouton "Absent" a été remplacé par la fonction marquerAbsent()
    });
</script>
@endsection
