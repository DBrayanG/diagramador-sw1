@extends('layouts.panel')

@section('content')
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Contribuciones</h3>
                </div>
            </div>
        </div>

        @if ($invitaciones->isEmpty())
            <div class="alert alert-warning" role="alert">
                No esta contribuyendo en ningun diagrama por el momento
            </div>
        @else
            <div class="table-responsive">
                <!-- Projects table -->
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Diagrama</th>
                            <th scope="col">Anfitrion</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invitaciones as $invitacion)
                            <tr>
                                <th scope="row">
                                    {{ $invitacion->diagram->titulo }}
                                </th>
                                <td>
                                    {{ $invitacion->diagram->user->name }}
                                </td>
                                <td>
                                    <a href="{{ url('/diagramas/'. $invitacion->diagram->id . '/pizarra') }}" class="btn btn-sm btn-success">Ir al Diagrama</a>
                                    <a href="{{ url('/diagramas/' . $invitacion->diagram->id . '/descargar') }}" class="btn btn-sm btn-dark">Copia Local</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
@endsection
