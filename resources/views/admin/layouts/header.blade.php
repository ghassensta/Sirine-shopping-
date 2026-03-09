<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
     id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">

        <!-- Barre d'information à gauche -->
        <div class="d-flex align-items-center gap-3 text-muted small fw-medium">
            <!-- Bienvenue -->
            <span class="d-none d-md-inline">
                <i class="ti ti-user-check me-1"></i>
                Bienvenue, {{ Auth::user()?->name ?? 'Administrateur' }}
            </span>

            

            <!-- Frais de livraison -->
            <span class="badge bg-label-info rounded-pill px-3 py-2 d-none d-lg-inline">
                <i class="ti ti-package ti-sm me-1"></i>
                Frais de livraison: {{ number_format($config?->shipping_cost ?? 5.99, 2, ',', ' ') }} DT
            </span>

            <!-- Date -->
            <span class="d-none d-xl-inline">
                <i class="ti ti-calendar-event ti-sm me-1"></i>
                {{ now()->locale('fr')->isoFormat('DD MMM YYYY') }}
            </span>
        </div>

        <!-- Menu de droite -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Lien vers le front-office -->
            <li class="nav-item me-3">
                <a href="{{ url('/') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center px-3 py-2">
                    <i class="ti ti-world ti-sm me-2"></i>
                    <span class="d-none d-md-inline">Voir le site</span>
                </a>
            </li>

            <!-- Dropdown utilisateur -->
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle hide-arrow p-1" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ $config?->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/images/default_avatar.png') }}"
                             alt="Avatar"
                             class="rounded-circle" />
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $config?->site_logo ? asset('storage/' . $config->site_logo) : asset('assets/images/default_avatar.png') }}"
                                             alt="Avatar"
                                             loading="lazy"
                                             class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ Auth::user()?->name ?? 'Super Administrateur' }}</span>
                                    <small class="text-muted d-block">{{ Auth::user()?->roles?->first()?->name ?? '—' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-2"></li>

                    <li>
                        <a class="dropdown-item" href="{{ route('configurations.index') }}">
                            <i class="ti ti-settings me-2 ti-sm"></i> Paramètres
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-2"></li>

                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout me-2 ti-sm"></i> Déconnexion
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<!-- /Navbar -->
