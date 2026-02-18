@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            <i class="ti ti-shopping-cart me-2"></i>Commandes
        </h4>
    </div>

    <!-- Filtre statut - Mobile friendly -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <select name="statut" id="statut-filter" class="form-select">
                        <option value="all" selected>📊 Tous les statuts</option>
                        @forelse ($statuts as $item)
                            <option value="{{ $item->id }}">{{ $item->name ?? 'Statut Inconnu' }}</option>
                        @empty
                            <option disabled>Aucun statut</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input type="text" id="search-input" class="form-control" placeholder="🔍 Rechercher...">
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Desktop : DataTable -->
    <div class="d-none d-lg-block">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatables-commandes table table-hover">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th class="text-center">Articles</th>
                                <th>Total TTC</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Mobile : Cards -->
    <div class="d-lg-none" id="mobile-orders-container">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- Pagination Mobile -->
    <div class="d-lg-none mt-3" id="mobile-pagination">
        <nav>
            <ul class="pagination pagination-sm justify-content-center"></ul>
        </nav>
    </div>
</div>

<!-- Modal Détails -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <div>
                    <h6 class="modal-title mb-0">
                        <i class="ti ti-receipt me-2"></i>Commande <span id="modal-order-number"></span>
                    </h6>
                    <small class="opacity-75" id="modal-order-date"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="orderDetailsContent"></div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Statut -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>Modifier le statut
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="updateStatusContent"></div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<style>
    /* Mobile Cards */
    .order-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
        animation: fadeInUp 0.3s ease;
    }
    .order-card:active { transform: scale(0.98); }

    .order-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
    }
    .order-card-body { padding: 1rem; }

    /* Info rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        color: #6c757d;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-value { font-weight: 600; text-align: right; }

    /* ══ Product items dans modal ══ */
    .product-item-modal {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        background: #fafafa;
        transition: box-shadow 0.2s ease;
    }
    .product-item-modal:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

    /* ══ Image produit dans modal ══ */
    .product-img-modal {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        flex-shrink: 0;
        background: #f8f9fa;
        display: block;
    }
    .product-no-img {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 1.5rem;
    }

    /* Action buttons mobile */
    .action-btn-mobile {
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 0.875rem;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Badges */
    .badge-mobile {
        padding: 0.5em 0.75em;
        border-radius: 20px;
        font-size: 0.813rem;
        font-weight: 500;
    }

    /* Summary */
    .summary-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .summary-line:last-child {
        border-bottom: none;
        border-top: 2px solid #007bff;
        padding-top: 1rem;
        margin-top: 0.5rem;
    }

    @media (min-width: 992px) {
        .product-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
        .badge-status { padding: 0.5em 1em; font-size: 0.875rem; border-radius: 20px; font-weight: 500; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
'use strict';

{{-- ══ URL storage rendue côté Laravel pour éviter tout problème de chemin ══ --}}
const STORAGE_URL = "{{ rtrim(asset('storage'), '/') }}";

$(function() {
    let dtCommande;
    let mobileOrders  = [];
    let currentPage   = 1;
    const itemsPerPage = 10;
    const isMobile    = window.innerWidth < 992;

    // ============================================================
    // DESKTOP : DataTable
    // ============================================================
    if (!isMobile && $('.datatables-commandes').length) {
        dtCommande = $('.datatables-commandes').DataTable({
            processing:  true,
            serverSide:  true,
            responsive:  false,
            ajax: {
                url:  "{{ route('commandes.get') }}",
                type: "GET",
                data: function(d) {
                    d._token    = '{{ csrf_token() }}';
                    d.statut_id = $('#statut-filter').val();
                }
            },
            columns: [
                { data: 'numero_commande' },
                { data: 'date' },
                { data: 'client_name' },
                { data: 'items_count' },
                { data: 'total_ttc' },
                { data: 'statut_name' },
                { data: null }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function(data) {
                        return `<span class="fw-bold text-primary">${data}</span>`;
                    }
                },
                {
                    targets: 2,
                    render: function(data, type, full) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">${escapeHtml(data)}</span>
                                <small class="text-muted">
                                    <i class="ti ti-phone ti-xs me-1"></i>${escapeHtml(full.client_phone)}
                                </small>
                            </div>`;
                    }
                },
                {
                    targets: 3,
                    className: 'text-center',
                    render: function(data) {
                        return `<span class="badge bg-label-info rounded-pill">${data}</span>`;
                    }
                },
                {
                    targets: 4,
                    render: function(data) {
                        return `<span class="fw-bold text-success">${fmtPrice(data)} DT</span>`;
                    }
                },
                {
                    targets: 5,
                    render: function(data) { return getStatusBadge(data); }
                },
                {
                    targets: -1,
                    orderable:  false,
                    searchable: false,
                    className:  'text-center',
                    render: function(data, type, full) { return getActionButtons(full); }
                }
            ],
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                searchPlaceholder: "Rechercher...",
                search: ""
            }
        });

        $('#search-input').on('keyup', debounce(function() {
            dtCommande.search($(this).val()).draw();
        }, 400));
    }

    // ============================================================
    // MOBILE : Cards
    // ============================================================
    if (isMobile) {
        loadMobileOrders();
        $('#search-input').on('keyup', debounce(loadMobileOrders, 500));
    }

    function loadMobileOrders() {
        $.ajax({
            url:  "{{ route('commandes.get') }}",
            type: "GET",
            data: {
                _token:    '{{ csrf_token() }}',
                statut_id: $('#statut-filter').val(),
                start:  0,
                length: 100,
                search: { value: $('#search-input').val() }
            },
            success: function(response) {
                mobileOrders = response.data;
                currentPage  = 1;
                renderMobileOrders();
            }
        });
    }

    function renderMobileOrders() {
        const container = $('#mobile-orders-container');

        if (!mobileOrders.length) {
            container.html(`
                <div class="text-center py-5">
                    <i class="ti ti-inbox ti-lg text-muted mb-3 d-block"></i>
                    <p class="text-muted">Aucune commande trouvée</p>
                </div>`);
            return;
        }

        const start = (currentPage - 1) * itemsPerPage;
        const paged = mobileOrders.slice(start, start + itemsPerPage);

        const html = paged.map(order => `
            <div class="order-card">
                <div class="order-card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${escapeHtml(order.numero_commande)}</h6>
                            <small class="opacity-75">
                                <i class="ti ti-calendar ti-xs me-1"></i>${escapeHtml(order.date)}
                            </small>
                        </div>
                        <span class="badge ${getStatusBadgeClass(order.statut_name)} badge-mobile">
                            ${escapeHtml(order.statut_name)}
                        </span>
                    </div>
                </div>
                <div class="order-card-body">
                    <div class="info-row">
                        <span class="info-label"><i class="ti ti-user"></i> Client</span>
                        <span class="info-value">${escapeHtml(order.client_name)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ti ti-phone"></i> Téléphone</span>
                        <span class="info-value">${escapeHtml(order.client_phone)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ti ti-package"></i> Articles</span>
                        <span class="info-value">
                            <span class="badge bg-label-info rounded-pill">${order.items_count}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ti ti-currency-dollar"></i> Total</span>
                        <span class="info-value text-success fw-bold">${fmtPrice(order.total_ttc)} DT</span>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-primary action-btn-mobile view-order-mobile"
                                data-order='${safeJson(order)}'>
                            <i class="ti ti-eye"></i> Voir
                        </button>
                        <button class="btn btn-info action-btn-mobile edit-status" data-id="${order.id}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <a href="/admin/sirine-shopping/commandes/${order.id}/pdf"
                           target="_blank"
                           class="btn btn-success action-btn-mobile">
                            <i class="ti ti-file-download"></i>
                        </a>
                        <button class="btn btn-danger action-btn-mobile delete-order" data-id="${order.id}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            </div>`).join('');

        container.html(html);
        renderMobilePagination();
    }

    function renderMobilePagination() {
        const total = Math.ceil(mobileOrders.length / itemsPerPage);
        const ul    = $('#mobile-pagination ul');

        if (total <= 1) { ul.html(''); return; }

        let html = `<li class="page-item ${currentPage===1?'disabled':''}">
            <a class="page-link" href="#" data-page="${currentPage-1}">‹</a></li>`;

        for (let i = 1; i <= total; i++) {
            if (i===1 || i===total || (i>=currentPage-1 && i<=currentPage+1)) {
                html += `<li class="page-item ${i===currentPage?'active':''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            } else if (i===currentPage-2 || i===currentPage+2) {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
        }

        html += `<li class="page-item ${currentPage===total?'disabled':''}">
            <a class="page-link" href="#" data-page="${currentPage+1}">›</a></li>`;

        ul.html(html);
    }

    $(document).on('click', '#mobile-pagination .page-link', function(e) {
        e.preventDefault();
        const p   = parseInt($(this).data('page'));
        const max = Math.ceil(mobileOrders.length / itemsPerPage);
        if (p && p > 0 && p <= max) {
            currentPage = p;
            renderMobileOrders();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // ============================================================
    // MODAL DÉTAILS — CORRECTION IMAGE PRODUIT
    // ============================================================
    $(document).on('click', '.view-order, .view-order-mobile', function() {
        showOrderDetails($(this).data('order'));
    });

    /**
     * Construit l'URL complète de l'image depuis un chemin relatif ou absolu.
     *
     * Cas gérés :
     *   null / ''                    → null  (affiche placeholder)
     *   'https://...'                → utilisée telle quelle
     *   'products/cover/abc.webp'   → STORAGE_URL + '/products/cover/abc.webp'
     *   'storage/products/...'      → STORAGE_URL + '/products/...'  (retire le storage/ de tête)
     *   '/storage/products/...'     → idem
     */
    function buildImgUrl(path) {
        if (!path || !String(path).trim()) return null;
        if (/^https?:\/\//i.test(path)) return path;
        // Retire l'éventuel préfixe /storage/ ou storage/ pour éviter le doublon
        const clean = String(path).replace(/^\/?(storage\/)?/, '');
        return STORAGE_URL + '/' + clean;
    }

    /**
     * Retourne le HTML de l'image produit avec fallback sur placeholder.
     * Le onerror masque l'img cassée et affiche l'icône à la place.
     */
    function productImgHtml(item) {
        console.log('Building image URL for:', item.image_avant);
        const url = buildImgUrl(item.image);

        if (!url) {
            return `<div class="product-no-img" title="Pas d'image">
                        <i class="ti ti-photo-off"></i>
                    </div>`;
        }

        return `<img
                    src="${url}"
                    alt="${escapeHtml(item.name)}"
                    class="product-img-modal"
                    loading="lazy"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="product-no-img" style="display:none;" title="Image introuvable">
                    <i class="ti ti-photo-off"></i>
                </div>`;
    }

    function showOrderDetails(order) {
        $('#modal-order-number').text(order.numero_commande);
        $('#modal-order-date').text(order.date_full || order.date);

        // ── Articles ──────────────────────────────────────────
        let itemsHtml = '';
        console.log('Order items:', order.items);
        if (order.items && order.items.length > 0) {
            itemsHtml = order.items.map(item => `
                <div class="product-item-modal">
                    <div class="d-flex gap-3 align-items-center">
                        ${productImgHtml(item)}
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-1 text-truncate" title="${escapeHtml(item.name)}">
                                ${escapeHtml(item.name)}
                            </h6>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <small class="text-muted">
                                    Qté : <strong>${item.quantity}</strong>
                                    × ${fmtPrice(item.unit_price || 0)} DT
                                </small>
                                <strong class="text-success">${fmtPrice(item.subtotal || 0)} DT</strong>
                            </div>
                        </div>
                    </div>
                </div>`).join('');
        } else {
            itemsHtml = `<div class="alert alert-info m-3">
                <i class="ti ti-info-circle me-2"></i>Aucun article trouvé
            </div>`;
        }

        // ── Récapitulatif ──────────────────────────────────────
        const content = `
            <div class="p-3">

                <!-- Client -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="ti ti-user me-2"></i>Client
                    </h6>
                    <div class="info-row">
                        <span class="info-label">Nom</span>
                        <span class="info-value">${escapeHtml(order.client_name)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Téléphone</span>
                        <span class="info-value">${escapeHtml(order.client_phone)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Adresse</span>
                        <span class="info-value">${escapeHtml(order.client_adresse || '—')}</span>
                    </div>
                </div>

                <!-- Produits -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="ti ti-package me-2"></i>Produits
                        <span class="badge bg-label-info ms-2">${order.items_count}</span>
                    </h6>
                    ${itemsHtml}
                </div>

                <!-- Récapitulatif -->
                <div class="summary-section">
                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="ti ti-calculator me-2"></i>Récapitulatif
                    </h6>
                    <div class="summary-line">
                        <span>Sous-total HT</span>
                        <strong>${fmtPrice(order.subtotal_ht || 0)} DT</strong>
                    </div>
                    <div class="summary-line">
                        <span>Frais de livraison</span>
                        <strong>${fmtPrice(order.shipping_cost || 0)} DT</strong>
                    </div>
                    <div class="summary-line">
                        <span class="fw-bold">Total TTC</span>
                        <strong class="text-success fs-5">${fmtPrice(order.total_ttc || 0)} DT</strong>
                    </div>
                </div>

            </div>`;

        $('#orderDetailsContent').html(content);
        $('#orderDetailsModal').modal('show');
    }

    // ============================================================
    // HELPERS
    // ============================================================

    function escapeHtml(str) {
        if (str == null) return '—';
        return String(str)
            .replace(/&/g,  '&amp;')
            .replace(/</g,  '&lt;')
            .replace(/>/g,  '&gt;')
            .replace(/"/g,  '&quot;')
            .replace(/'/g,  '&#039;');
    }

    function fmtPrice(val) {
        return parseFloat(val || 0).toLocaleString('fr-TN', { minimumFractionDigits: 3 });
    }

    function safeJson(obj) {
        return JSON.stringify(obj).replace(/'/g, "&#39;");
    }

    function getStatusBadge(label) {
        return `<span class="badge ${getStatusBadgeClass(label)} badge-status">${escapeHtml(label)}</span>`;
    }

    function getStatusBadgeClass(label) {
        const s = (label || '').toLowerCase();
        if (s.includes('livr') && !s.includes('en cours')) return 'bg-label-success';
        if (s.includes('annul'))      return 'bg-label-danger';
        if (s.includes('traitement')) return 'bg-label-warning';
        if (s.includes('en cours'))   return 'bg-label-info';
        if (s.includes('livraison'))  return 'bg-label-info';
        return 'bg-label-secondary';
    }

    function getActionButtons(full) {
        return `
            <div class="d-flex justify-content-center gap-1">
                <button class="btn btn-sm btn-icon btn-primary view-order"
                        data-order='${safeJson(full)}'
                        title="Voir détails">
                    <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-info edit-status"
                        data-id="${full.id}"
                        title="Modifier statut">
                    <i class="ti ti-edit"></i>
                </button>
                <a href="/admin/sirine-shopping/commandes/${full.id}/pdf"
                   target="_blank"
                   class="btn btn-sm btn-icon btn-success"
                   title="PDF">
                    <i class="ti ti-file-download"></i>
                </a>
                <button class="btn btn-sm btn-icon btn-danger delete-order"
                        data-id="${full.id}"
                        title="Supprimer">
                    <i class="ti ti-trash"></i>
                </button>
            </div>`;
    }

    function debounce(fn, wait) {
        let t;
        return function(...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // ============================================================
    // ÉVÉNEMENTS COMMUNS
    // ============================================================

    $('#statut-filter').on('change', function() {
        if (isMobile) { currentPage = 1; loadMobileOrders(); }
        else          { dtCommande.ajax.reload(); }
    });

    // Modifier statut
    $(document).on('click', '.edit-status', function() {
        const id  = $(this).data('id');
        const url = "{{ route('commandes.edit-status', ':id') }}".replace(':id', id);

        $('#updateStatusContent').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <p class="mt-2 text-muted small">Chargement...</p>
            </div>`);

        $('#updateStatusModal').modal('show');
        $('#updateStatusContent').load(url);
    });

    // Soumission statut
    $(document).on('submit', '#updateStatusForm', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true)
           .html('<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement...');

        $.ajax({
            url:  $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#updateStatusModal').modal('hide');
                isMobile ? loadMobileOrders() : dtCommande.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Statut mis à jour !', timer: 2000, showConfirmButton: false });
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Une erreur est survenue' });
                btn.prop('disabled', false)
                   .html('<i class="ti ti-device-floppy me-1"></i> Enregistrer');
            }
        });
    });

    // Supprimer
    $(document).on('click', '.delete-order', function() {
        const id  = $(this).data('id');
        const url = "{{ route('commandes.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title:             'Êtes-vous sûr ?',
            text:              'Cette action est irréversible !',
            icon:              'warning',
            showCancelButton:  true,
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText:  'Annuler',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton:  'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  url,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    isMobile ? loadMobileOrders() : dtCommande.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Supprimée !', timer: 2000, showConfirmButton: false });
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de supprimer' });
                }
            });
        });
    });
});
</script>
@endsection
