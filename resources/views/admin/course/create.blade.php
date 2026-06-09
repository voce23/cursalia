@extends('layouts.admin')

@section('title', 'Nuevo curso')
@section('page-title', 'Nuevo curso')
@section('page-subtitle', 'Crea un curso para tu academia')

@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.course._form', ['course' => null])
</form>
@endsection
