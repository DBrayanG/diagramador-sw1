<div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
    <div class=" dropdown-header noti-title">
        <h6 class="text-overflow m-0">Bienvenido</h6>
    </div>
    <a href="{{ url('/diagramas')}}" class="dropdown-item">
        <i class="ni ni-single-02"></i>
        <span>Mis Diagramas</span>
    </a>
    <a href="{{ url('/colaboraciones')}}" class="dropdown-item">
        <i class="ni ni-settings-gear-65"></i>
        <span>Colaboraciones</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="{{ route('logout') }}" class="dropdown-item"  onclick="event.preventDefault(); document.getElementById('formLogout').submit();">
        <i class="fas fa-sign-in-alt"></i>
        <span>Cerrar Sesión</span>
        <form action="{{ route('logout') }}" method="POST" style="display: none;" id="formLogout">
            @csrf
        </form>
    </a>
</div>
