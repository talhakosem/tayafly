@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-4">Hoş Geldiniz!</h2>
                <p>Admin paneline başarıyla giriş yaptınız.</p>
                <p><strong>Kullanıcı:</strong> {{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
