@extends($layout)

@section('content')
<?php
$page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
?>

@include($partial_path . '.page_header')

<div id="index_table">
    @include($viewPrefix . '.index_table')
</div>
@endsection