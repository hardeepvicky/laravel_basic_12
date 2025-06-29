<?php

$list = ['Hardeep', 'Vicky', '<span class="badge badge-info">Vicky</span>', 'Languages' => ['C', 'PHP']];

?>

@extends('backend.layouts.default')

@section('content')
    <x-common-list :list="$list" headerText="Order List" type="ul" />
@endsection
