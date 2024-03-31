{{-- <h6 class="navbar-heading text-muted">Gestion</h6> --}}
<ul class="navbar-nav">
    <li class="nav-item  active ">
        <a class="nav-link  active " href="{{ url('/home') }}">
            <i class="ni ni-tv-2 text-info"></i> Inicio
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="{{ url('/diagramas') }}">
            <i class="fas fa-project-diagram text-danger"></i> Diagramas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="{{ url('/colaboraciones') }}">
            <i class="fas fa-bezier-curve text-success"></i> Colaboraciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('formLogout').submit();">
            <i class="fas fa-sign-in-alt"></i> Cerrar sesión
        </a>
        <form action="{{ route('logout') }}" method="POST" style="display: none;" id="formLogout">
            @csrf
        </form>
    </li>
</ul>
