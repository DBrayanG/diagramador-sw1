{{-- <h6 class="navbar-heading text-muted">Gestion</h6> --}}
<ul class="navbar-nav">
    <li class="nav-item  active ">
        <a class="nav-link  active " href="{{ url('/home') }}">
            <i class="ni ni-tv-2 text-info"></i> Inicio
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="{{ url('/diagramas') }}">
            <i class="fas fa-project-diagram text-danger"></i> Mis Diagramas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="{{ url('/colaboraciones') }}">
            <i class="fas fa-bezier-curve text-success"></i> Contribuciones
        </a>
    </li>
</ul>
