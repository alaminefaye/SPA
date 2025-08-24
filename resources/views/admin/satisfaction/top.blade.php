@extends('layouts.app')

@section('title', 'Top employés - Notes de satisfaction')

@section('page-css')
<style>
    .rating-stars {
        font-size: 18px;
        color: #FFD700;
    }
    .employee-card {
        transition: transform .3s;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }
    .employee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .employee-card.gold {
        background: linear-gradient(135deg, #fff7e6, #ffd700);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }
    .employee-card.silver {
        background: linear-gradient(135deg, #f8f9fa, #c0c0c0);
        box-shadow: 0 4px 12px rgba(192, 192, 192, 0.3);
    }
    .employee-card.bronze {
        background: linear-gradient(135deg, #fff4e8, #cd7f32);
        box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);
    }
    .rank-badge {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 40px;
        height: 40px;
        background-color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10;
    }
    .rank-badge.rank-1 { background-color: gold; color: black; }
    .rank-badge.rank-2 { background-color: silver; color: black; }
    .rank-badge.rank-3 { background-color: #cd7f32; color: white; }
    
    .month-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        background-color: #6200EA;
        color: white;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 10;
    }
    .top-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .trophy-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .employee-month-card {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        background-color: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Employés /</span> 
    <span class="text-muted fw-light">Notes de satisfaction /</span> 
    Top employés
</h4>

<!-- Employés du mois -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Employés du mois</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($employeesOfTheMonth as $index => $data)
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="employee-month-card">
                                <div class="month-badge">{{ $data['month_name'] }} {{ $data['year'] }}</div>
                                <div class="card-body text-center pt-5">
                                    <div class="trophy-icon text-warning">🏆</div>
                                    <div class="avatar avatar-xl mb-3">
                                        @if($data['employee']->photo)
                                            <img src="{{ asset('storage/' . $data['employee']->photo) }}" alt="{{ $data['employee']->nom }} {{ $data['employee']->prenom }}" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($data['employee']->prenom, 0, 1) }}{{ substr($data['employee']->nom, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <h5 class="card-title mb-1">{{ $data['employee']->prenom }} {{ $data['employee']->nom }}</h5>
                                    <div class="rating-stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($data['employee']->avg_rating))
                                                <i class="bx bxs-star"></i>
                                            @else
                                                <i class="bx bx-star"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted ms-1">({{ number_format($data['employee']->avg_rating, 1) }})</span>
                                    </div>
                                    <p class="card-text">{{ $data['employee']->total_feedbacks }} évaluations</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                Aucune donnée disponible pour les employés du mois.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 Employés -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Top 10 des employés les mieux notés</h5>
        <a href="{{ route('admin.satisfaction.index') }}" class="btn btn-sm btn-primary">Retour à toutes les notes</a>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($topEmployees as $index => $employee)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="employee-card position-relative {{ $index < 3 ? ($index == 0 ? 'gold' : ($index == 1 ? 'silver' : 'bronze')) : '' }}">
                        <div class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    @if($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->nom }} {{ $employee->prenom }}" class="rounded-circle">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($employee->prenom, 0, 1) }}{{ substr($employee->nom, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">{{ $employee->prenom }} {{ $employee->nom }}</h5>
                                    <small class="text-muted">{{ $employee->total_feedbacks }} évaluations</small>
                                </div>
                            </div>
                            
                            <div class="rating-stars mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($employee->avg_rating))
                                        <i class="bx bxs-star"></i>
                                    @else
                                        <i class="bx bx-star"></i>
                                    @endif
                                @endfor
                                <span class="fw-semibold ms-1">{{ number_format($employee->avg_rating, 1) }} / 5</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-label-success">{{ $employee->five_stars }} notes 5★</span>
                                </div>
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-user me-1"></i> Profil
                                </a>
                            </div>
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
@endsection
