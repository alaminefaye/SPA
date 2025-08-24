@extends('layouts.app')

@section('title', 'Notes de satisfaction')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<style>
    .rating-stars {
        font-size: 18px;
        color: #FFD700;
    }
    .employee-card {
        transition: transform .3s;
        border-radius: 10px;
        overflow: hidden;
    }
    .employee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .top-employee {
        position: relative;
    }
    .medal {
        position: absolute;
        top: -10px;
        right: -10px;
        font-size: 2rem;
        z-index: 10;
    }
    .medal-1 { color: gold; }
    .medal-2 { color: silver; }
    .medal-3 { color: #cd7f32; }
    .rating-bars {
        width: 100%;
    }
    .rating-bar {
        height: 8px;
        margin-bottom: 5px;
        border-radius: 4px;
    }
    .rating-bar.rating-5 { background-color: #4CAF50; }
    .rating-bar.rating-4 { background-color: #8BC34A; }
    .rating-bar.rating-3 { background-color: #FFC107; }
    .rating-bar.rating-2 { background-color: #FF9800; }
    .rating-bar.rating-1 { background-color: #F44336; }
    .rating-count {
        font-size: 0.85rem;
        color: #666;
    }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Employés /</span> Notes de satisfaction
</h4>

<!-- Top 3 Employees Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Top 3 des employés les mieux notés</h5>
                <a href="{{ route('admin.satisfaction.top-employees') }}" class="btn btn-sm btn-primary">Voir le classement complet</a>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($topEmployees as $index => $employee)
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card employee-card top-employee">
                                <span class="medal medal-{{ $index + 1 }}">
                                    @if($index == 0)
                                        🥇
                                    @elseif($index == 1)
                                        🥈
                                    @else
                                        🥉
                                    @endif
                                </span>
                                <div class="card-body text-center">
                                    <div class="avatar avatar-xl mb-3">
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->nom }} {{ $employee->prenom }}" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($employee->prenom, 0, 1) }}{{ substr($employee->nom, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <h5 class="card-title mb-1">{{ $employee->prenom }} {{ $employee->nom }}</h5>
                                    <div class="rating-stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($employee->avg_rating))
                                                <i class="bx bxs-star"></i>
                                            @else
                                                <i class="bx bx-star"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted ms-1">({{ number_format($employee->avg_rating, 1) }})</span>
                                    </div>
                                    <p class="card-text">{{ $employee->rated_feedbacks }} évaluations</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                Aucune note de satisfaction n'a encore été attribuée aux employés.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Employees Section -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Toutes les notes de satisfaction par employé</h5>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-satisfaction table border-top">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Note moyenne</th>
                    <th>Distribution des notes</th>
                    <th>Total évaluations</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar-wrapper">
                                <div class="avatar me-2">
                                    @if($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->nom }} {{ $employee->prenom }}" class="rounded-circle">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($employee->prenom, 0, 1) }}{{ substr($employee->nom, 0, 1) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-body text-truncate">
                                    <span class="fw-semibold">{{ $employee->prenom }} {{ $employee->nom }}</span>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="rating-stars">
                            @if($employee->avg_rating)
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($employee->avg_rating))
                                        <i class="bx bxs-star"></i>
                                    @else
                                        <i class="bx bx-star"></i>
                                    @endif
                                @endfor
                                <span class="ms-1 fw-semibold">{{ number_format($employee->avg_rating, 1) }}</span>
                            @else
                                <span class="text-muted">Non évalué</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($employee->rated_feedbacks > 0)
                            <div class="rating-bars">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="rating-stars me-2" style="min-width: 70px;">
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="rating-bar rating-5" style="width: {{ ($employee->five_stars / $employee->rated_feedbacks) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-count ms-2">{{ $employee->five_stars }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <div class="rating-stars me-2" style="min-width: 70px;">
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bx-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="rating-bar rating-4" style="width: {{ ($employee->four_stars / $employee->rated_feedbacks) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-count ms-2">{{ $employee->four_stars }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <div class="rating-stars me-2" style="min-width: 70px;">
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="rating-bar rating-3" style="width: {{ ($employee->three_stars / $employee->rated_feedbacks) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-count ms-2">{{ $employee->three_stars }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <div class="rating-stars me-2" style="min-width: 70px;">
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="rating-bar rating-2" style="width: {{ ($employee->two_stars / $employee->rated_feedbacks) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-count ms-2">{{ $employee->two_stars }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rating-stars me-2" style="min-width: 70px;">
                                        <i class="bx bxs-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                        <i class="bx bx-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="rating-bar rating-1" style="width: {{ ($employee->one_star / $employee->rated_feedbacks) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-count ms-2">{{ $employee->one_star }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Aucune évaluation</span>
                        @endif
                    </td>
                    <td>{{ $employee->rated_feedbacks }} / {{ $employee->total_feedbacks }}</td>
                    <td>
                        @if($employee->actif)
                            <span class="badge bg-label-success">Actif</span>
                        @else
                            <span class="badge bg-label-danger">Inactif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('vendors-js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        $('.datatables-satisfaction').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
            },
            responsive: true
        });
    });
</script>
@endsection
