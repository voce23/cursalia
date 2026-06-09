@extends('layouts.admin')

@section('title', 'Editar curso')
@section('page-title', 'Editar curso')
@section('page-subtitle', $course->title)

@section('content')
<form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.course._form', ['course' => $course])
</form>
@endsection
