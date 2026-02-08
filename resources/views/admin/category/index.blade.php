@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <h4 class="mb-0">Gestion des Catégories</h4>
        <button type="button" class="btn btn-primary add-new-category">
            <i class="ti ti-plus me-1"></i> Nouvelle catégorie
        </button>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-categories table border-top">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Image</th>
                        <th>Nom / Hiérarchie</th>
                        <th>Parent</th>
                        <th>Visibilité</th>
                        <th>Statut</th>
                        <th>Créée le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

<!-- MODAL AJOUT -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formAddCategory" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Vêtements femme">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" class="form-select parent-select-add" data-placeholder="Aucune (catégorie principale)">
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Image (couverture)</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="is_active" class="form-select" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Titre de section</label>
                            <input type="text" name="title_section" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sous-titre de section</label>
                            <input type="text" name="sous_title_section" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" name="order" class="form-control" min="0" value="999">
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_publish" id="add_is_publish" value="1" >
                                <label class="form-check-label" for="add_is_publish">Publiée / Visible</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Référencement (SEO) — optionnel</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="Titre pour Google">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" placeholder="Description courte pour les moteurs de recherche"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-danger mt-3" id="addCategoryErrors" role="alert" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div class="modal fade" id="modalEditCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditCategory" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" id="edit_parent_id" class="form-select parent-select-edit" data-placeholder="Aucune (catégorie principale)">
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Image actuelle</label>
                            <div id="current_image_preview" class="mb-2" style="display:none;">
                                <img id="current_image" src="" alt="Image actuelle" style="max-height:80px; border-radius:6px;">
                            </div>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Laissez vide pour conserver l’image actuelle</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="is_active" id="edit_is_active" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Titre de section</label>
                            <input type="text" name="title_section" id="edit_title_section" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sous-titre de section</label>
                            <input type="text" name="sous_title_section" id="edit_sous_title_section" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" name="order" id="edit_order" class="form-control" min="0">
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_publish" id="edit_is_publish" value="1">
                                <label class="form-check-label" for="edit_is_publish">Publiée / Visible</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Référencement (SEO) — optionnel</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="edit_meta_title" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="edit_meta_description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-danger mt-3" id="editCategoryErrors" role="alert" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// =============================================================
// CHARGEMENT HIÉRARCHIQUE DES CATÉGORIES PARENTES
// =============================================================
function loadHierarchicalParents(selector, excludeId = null, selectedId = null) {
    if ($(selector).hasClass('select2-hidden-accessible')) {
        $(selector).select2('destroy');
    }

    $.ajax({
        url: '{{ route("categories.hierarchical") }}',
        type: 'GET',
        data: { exclude_id: excludeId },
        success: function(data) {
            let html = '<option value="">— Aucune (catégorie principale) —</option>';
            data.forEach(item => {
                let selected = (item.id == selectedId) ? ' selected' : '';
                html += `<option value="${item.id}"${selected}>${item.name}</option>`;
            });

            $(selector).html(html);

            $(selector).select2({
                dropdownParent: $(selector).closest('.modal'),
                placeholder: "Sélectionner une catégorie parente",
                allowClear: true,
                width: '100%'
            });

            if (selectedId) {
                $(selector).val(selectedId).trigger('change');
            }
        },
        error: function() {
            console.error('Erreur chargement catégories parentes');
        }
    });
}

$(document).ready(function() {

    // =============================================================
    // INITIALISATION DATATABLE
    // =============================================================
    const dtCategories = $('.datatables-categories').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("category.get") }}',
        columns: [
            { data: null, orderable: false, searchable: false, className: 'control' },
            { data: 'image' },
            { data: 'name' },
            { data: 'parent_name' },
            { data: 'is_publish' },
            { data: 'is_active' },
            { data: 'created_at' },
            { data: null, orderable: false, searchable: false, className: 'text-end' }
        ],
        columnDefs: [
            {
                targets: -1,
                render: function(data, type, row) {
                    return `
                    <div class="d-inline-flex">
                        <a href="javascript:void(0)" class="text-body edit-category me-3" data-id="${row.id}">
                            <i class="ti ti-edit ti-sm"></i>
                        </a>
                        <a href="javascript:void(0)" class="text-body delete-category" data-id="${row.id}">
                            <i class="ti ti-trash ti-sm"></i>
                        </a>
                    </div>`;
                }
            }
        ],
        order: [[2, 'asc']],
        dom: '<"row mx-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        return 'Détails de la catégorie : ' + row.data().name;
                    }
                }),
                type: 'column',
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({ tableClass: 'table' })
            }
        }
    });

    // =============================================================
    // OUVERTURE MODAL AJOUT
    // =============================================================
    $('.add-new-category').on('click', function() {
        $('#formAddCategory')[0].reset();
        $('#addCategoryErrors').hide().empty();
        loadHierarchicalParents('.parent-select-add');
        $('#modalAddCategory').modal('show');
    });

    // =============================================================
    // SOUMISSION AJOUT
    // =============================================================
    $('#formAddCategory').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        // IMPORTANT : forcer l'envoi de is_publish (1 ou 0)
        formData.set('is_publish', $('#add_is_publish').is(':checked') ? '1' : '0');

        $.ajax({
            url: '{{ route("categories.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                $('#modalAddCategory').modal('hide');
                dtCategories.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Ajoutée !', text: 'Catégorie créée avec succès' });
            },
            error: function(xhr) {
                let html = '';
                if (xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        html += `<div>${messages.join('<br>')}</div>`;
                    });
                } else {
                    html = 'Une erreur est survenue.';
                }
                $('#addCategoryErrors').html(html).show();
            }
        });
    });

    // =============================================================
    // ÉDITION – CHARGEMENT DONNÉES
    // =============================================================
    $(document).on('click', '.edit-category', function() {
        const id = $(this).data('id');

        $.get('{{ route("categories.show", ":id") }}'.replace(':id', id), function(data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_title_section').val(data.title_section || '');
            $('#edit_sous_title_section').val(data.sous_title_section || '');
            $('#edit_order').val(data.order || 999);
            $('#edit_is_publish').prop('checked', !!data.is_publish);
            $('#edit_is_active').val(data.is_active ? '1' : '0');
            $('#edit_meta_title').val(data.meta_title || '');
            $('#edit_meta_description').val(data.meta_description || '');

            if (data.image_url) {
                $('#current_image').attr('src', data.image_url);
                $('#current_image_preview').show();
            } else {
                $('#current_image_preview').hide();
            }

            loadHierarchicalParents('.parent-select-edit', data.id, data.parent_id);

            $('#formEditCategory').attr('action', '{{ route("categories.update", ":id") }}'.replace(':id', data.id));
            $('#modalEditCategory').modal('show');
        }).fail(function() {
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger la catégorie' });
        });
    });

    // =============================================================
    // SOUMISSION MODIFICATION
    // =============================================================
    $('#formEditCategory').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        // IMPORTANT : forcer l'envoi de is_publish (1 ou 0)
        formData.set('is_publish', $('#edit_is_publish').is(':checked') ? '1' : '0');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                $('#modalEditCategory').modal('hide');
                dtCategories.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Modifiée !', text: 'Catégorie mise à jour' });
            },
            error: function(xhr) {
                let html = '';
                if (xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        html += `<div>${messages.join('<br>')}</div>`;
                    });
                } else {
                    html = 'Une erreur est survenue.';
                }
                $('#editCategoryErrors').html(html).show();
            }
        });
    });

    // =============================================================
    // SUPPRESSION
    // =============================================================
    $(document).on('click', '.delete-category', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');

        Swal.fire({
            title: 'Confirmer la suppression ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("categories.destroy", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function() {
                        dtCategories.row(row).remove().draw();
                        Swal.fire('Supprimée', 'Catégorie supprimée avec succès', 'success');
                    },
                    error: function() {
                        Swal.fire('Erreur', 'Impossible de supprimer la catégorie', 'error');
                    }
                });
            }
        });
    });

});
</script>
@endsection