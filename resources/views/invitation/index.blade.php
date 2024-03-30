@extends('layouts.panel')

@section('content')
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Invitar</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ url('/diagramas') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-angle-left"></i>
                        Regresar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if ($users->isEmpty())
            <div class="alert alert-warning" role="alert">
                No hay usuarios disponibles.
            </div>
        @else
            <form action="{{ url('/diagramas/invitaciones')}}" method="POST">
                @csrf
                <input type="hidden" name="diagram_id" value="{{ $diagram->id }}">
                <div class="form-group">
                    <label for="user_id">Seleccionar Usuario</label>
                    <select name="user_id" id="user_id" class="form-control">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Enviar Invitación</button>
            </form>
        @endif
        </div>
    </div>
@endsection
