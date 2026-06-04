@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Edit Kategori</h1>
            <div class="muted">{{ $category->name }}</div>
        </div>
        <a class="button ghost" href="{{ route('categories.index') }}">Kembali</a>
    </div>

    <section class="panel">
        <form method="post" action="{{ route('categories.update', $category) }}" class="form-grid">
            @csrf
            @method('put')
            <div class="field full">
                <label for="name">Nama Kategori</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="field full">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="field full">
                <button type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </section>
@endsection
