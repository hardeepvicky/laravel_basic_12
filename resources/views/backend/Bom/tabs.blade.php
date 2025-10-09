@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">
            <div class="shopping-wizard">
                <div class="shipping-form">
                    <div class="nav nav-pills horizontal-options shipping-options" role="tablist" aria-orientation="vertical">
                        <?php
                        $i = 0; $last_tab_found = false;
                        foreach($tabs as $tab => $tab_name):
                            $i++;
                            $a_cls = $tab == $current_tab ? "active" : "";

                            $url = "#";
                            
                            if (isset($model->id) && $model->id )
                            {
                                $url = route($routePrefix . '.edit', ["id" => $model->id, "tab" => $tab]);
                            }

                            if ($tab == $current_tab)
                            {
                                $url = "#";                            
                            }
                            
                            if (isset($model->tab_name) &&  $tab == $model->tab_name)
                            {
                                $last_tab_found = true;
                            }
                            
                        ?>
                        <a href="<?= $url ?>" class="nav-link <?= $a_cls ?>">
                            <div class="cart-options">
                                <div class="stroke-icon-wizard">
                                    <i class="fa-solid">
                                        {{ $i }}
                                    </i>
                                </div>
                                <div class="cart-options-content">
                                    <h6 class="f-w-700">{{ $tab_name }}</h6>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="tab-content pt-3">
                        <div class="tab-pane active">
                            @include($viewPrefix . '.tab_' . $current_tab)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
