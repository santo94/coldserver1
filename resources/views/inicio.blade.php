@extends('adminlte::page')

@section('title', 'Proveedores')


@section('content_header')
    <h1>Entradas</h1>
@stop

@section('content')
    



  
@stop

    @section('css')
        <style type="text/css">
            .inp {
                border: 0;
                background-color: transparent;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css" />

        {{-- Add here extra stylesheets --}}
        {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    @stop

@section('js')


    @stop