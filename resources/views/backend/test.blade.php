<?php

$list = ['Hardeep', 'Vicky', '<span class="badge badge-info">Vicky</span>', 'Languages' => ['C', 'PHP']];

?>

@extends('backend.layouts.default')

@section('content')
    <div class="card-wrapper rounded-3 h-100 bg-light-secondary">
        <x-common-list :list="$list" headerText="Order List" type="ol" />
    </div>
@endsection
