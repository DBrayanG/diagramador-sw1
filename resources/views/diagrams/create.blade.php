@extends('layouts.panel')

@section('content')
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Nuevo Diagrama</h3>
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
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Verifique</strong> {{ $error }}
                </div>
                @endforeach
            @endif

            <form action="{{ url('/diagramas')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="titulo">Titulo del Diagrama</label>
                    <input type="text" name="titulo" class="form-control"  value="{{ old('titulo') }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}">
                </div>

               {{--  <div class="form-group">
                    <label for="contenido">Contenido</label>
                    <input type="text" name="contenido" class="form-control" value="{{ old('contenido') }}">
                </div> --}}

                <button type="submit" class="btn btn-sm btn-primary">Crear diagrama</button>
            </form>
       </div>
    </div>
@endsection
