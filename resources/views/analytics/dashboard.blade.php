@extends('layouts.app')

@section('title', 'Analytics - WEGA')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Analytics Produits</h1>
            <p class="page-subtitle">Analysez les performances de vos produits</p>
        </div>
        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        @php($currentPeriod = $filters['period'] ?? 'week')
        @php($currentLimit = $filters['limit'] ?? '5')
        <form id="filtersForm" class="row g-3" data-endpoint="{{ route('analytics.data') }}">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Catégorie</label>
                <select name="categorie" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($categories as $categorie)
                        <option value="{{ $categorie }}" @selected(($filters['categorie'] ?? null) === $categorie)>{{ $categorie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Période</label>
                <select name="period" id="periodSelect" class="form-select">
                    <option value="day" @selected($currentPeriod === 'day')>Jour</option>
                    <option value="week" @selected($currentPeriod === 'week')>Semaine</option>
                    <option value="month" @selected($currentPeriod === 'month')>Mois</option>
                    <option value="year" @selected($currentPeriod === 'year')>Année</option>
                    <option value="custom" @selected($currentPeriod === 'custom')>Personnalisée</option>
                </select>
            </div>
            <div class="col-md-3 period-custom d-none">
                <label class="form-label fw-semibold">Du</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3 period-custom d-none">
                <label class="form-label fw-semibold">Au</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Top</label>
                <select name="limit" class="form-select">
                    <option value="5" @selected($currentLimit == '5')>Top 5</option>
                    <option value="10" @selected($currentLimit == '10')>Top 10</option>
                    <option value="20" @selected($currentLimit == '20')>Top 20</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Ventes</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartTopSelling"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Rentabilité</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartTopProfitable"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Produits les plus vendus</h5>
                <div class="d-flex gap-2">
                    <a data-export="{{ route('analytics.export', ['type' => 'top-selling', 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary export-link">
                        <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                    </a>
                    <a data-export="{{ route('analytics.export', ['type' => 'top-selling', 'format' => 'xlsx']) }}" class="btn btn-sm btn-outline-secondary export-link">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody id="tableTopSelling">
                            <tr><td colspan="5" class="text-center py-3">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Produits les plus rentables</h5>
                <div class="d-flex gap-2">
                    <a data-export="{{ route('analytics.export', ['type' => 'top-profitable', 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary export-link">
                        <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                    </a>
                    <a data-export="{{ route('analytics.export', ['type' => 'top-profitable', 'format' => 'xlsx']) }}" class="btn btn-sm btn-outline-secondary export-link">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Chiffre d'affaires</th>
                                <th>Bénéfice</th>
                            </tr>
                        </thead>
                        <tbody id="tableTopProfitable">
                            <tr><td colspan="6" class="text-center py-3">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const form = document.getElementById('filtersForm');
    const periodSelect = document.getElementById('periodSelect');
    const customFields = document.querySelectorAll('.period-custom');
    const sellingTable = document.getElementById('tableTopSelling');
    const profitTable = document.getElementById('tableTopProfitable');
    const exportLinks = document.querySelectorAll('.export-link');
    const sellingChartCtx = document.getElementById('chartTopSelling');
    const profitChartCtx = document.getElementById('chartTopProfitable');
    let sellingChart, profitChart;

    function toggleCustomFields() {
        const show = periodSelect.value === 'custom';
        customFields.forEach(el => el.classList.toggle('d-none', !show));
    }

    function buildParams() {
        return new URLSearchParams(new FormData(form));
    }

    function updateExportLinks() {
        const params = buildParams().toString();
        exportLinks.forEach(link => {
            const base = link.getAttribute('data-export');
            link.href = `${base}?${params}`;
        });
    }

    function drawCharts(data) {
        const sellingLabels = data.top_selling.map(item => item.product || item.code);
        const sellingValues = data.top_selling.map(item => item.quantity);
        const profitLabels = data.top_profitable.map(item => item.product || item.code);
        const profitValues = data.top_profitable.map(item => item.profit);

        if (sellingChart) sellingChart.destroy();
        if (profitChart) profitChart.destroy();

        sellingChart = new Chart(sellingChartCtx, {
            type: 'bar',
            data: {
                labels: sellingLabels,
                datasets: [{
                    label: 'Quantités vendues',
                    data: sellingValues,
                    backgroundColor: 'rgba(99, 102, 241, 0.5)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        });

        profitChart = new Chart(profitChartCtx, {
            type: 'bar',
            data: {
                labels: profitLabels,
                datasets: [{
                    label: 'Bénéfice',
                    data: profitValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        });
    }

    function renderTable(target, rows, columns) {
        if (!rows.length) {
            target.innerHTML = `<tr><td colspan="${columns}" class="text-center py-3">Aucune donnée sur la période.</td></tr>`;
            return;
        }

        target.innerHTML = rows.map(item => `
            <tr>
                <td>${item.product || '-'}</td>
                <td><span class="badge bg-primary">${item.code || '-'}</span></td>
                <td>${item.categorie || '-'}</td>
                <td><strong>${item.quantity ?? 0}</strong></td>
                <td><strong>${Number(item.revenue ?? 0).toLocaleString('fr-FR')} FCFA</strong></td>
                ${item.profit !== undefined ? `<td><strong class="text-success">${Number(item.profit ?? 0).toLocaleString('fr-FR')} FCFA</strong></td>` : ''}
            </tr>
        `).join('');
    }

    async function fetchAnalytics(event) {
        event?.preventDefault();
        toggleCustomFields();
        updateExportLinks();
        const params = buildParams();
        const endpoint = `${form.dataset.endpoint}?${params.toString()}`;
        sellingTable.innerHTML = '<tr><td colspan="5" class="text-center py-3">Chargement...</td></tr>';
        profitTable.innerHTML = '<tr><td colspan="6" class="text-center py-3">Chargement...</td></tr>';

        try {
            const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) {
                throw new Error('Erreur de chargement');
            }

            const data = await response.json();
            drawCharts(data);
            renderTable(sellingTable, data.top_selling, 5);
            renderTable(profitTable, data.top_profitable, 6);
        } catch (error) {
            sellingTable.innerHTML = '<tr><td colspan="5" class="text-danger text-center py-3">Erreur de chargement.</td></tr>';
            profitTable.innerHTML = '<tr><td colspan="6" class="text-danger text-center py-3">Erreur de chargement.</td></tr>';
        }
    }

    periodSelect.addEventListener('change', toggleCustomFields);
    form.addEventListener('submit', fetchAnalytics);
    toggleCustomFields();
    updateExportLinks();
    fetchAnalytics();
</script>
@endpush
