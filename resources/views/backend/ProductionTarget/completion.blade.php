@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxxl-12">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                            $field = 'product_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3">
                                    {{ $model->product?->getDisplayName() }}
                                </div>
                            </div>
                            <?php
                            $field = 'target_date';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3">
                                    {{ if_date($model->target_date) }}
                                </div>
                            </div>
                            <?php
                            $field = 'qty';
                            $label = "Target Qty"
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3" id="target_qty">
                                    {{ $model->qty }}
                                </div>
                            </div>

                            <?php
                            $field = 'complete_qty';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3" id="complete_qty">
                                    {{ $model->complete_qty }}
                                </div>
                            </div>

                            <?php
                            $field = 'complete_by_employee_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$employee_list"
                                layout="horizontal"
                                class="select2"
                                mandatory="true" />

                            <?php
                            $field = 'qty';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                class="validate-float"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                rows="3"
                            placeholder="Enter {{ $label }}" />
                                
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-buttons mb-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
    </form>

    <script>
        $(function(){

            $("form").submit(function(){
                var _this = $(this);

                var qty = $("#qty").val();
                qty = qty ? parseFloat(qty) : 0;

                var target_qty = $("#target_qty").text();
                target_qty = target_qty ? parseFloat(target_qty) : 0;

                var complete_qty = $("#complete_qty").text();
                complete_qty = complete_qty ? parseFloat(complete_qty) : 0;

                if (qty + complete_qty > target_qty)
                {
                    var confirm = $(this).attr("data-confirm");

                    if (!confirm)
                    {
                        confirmDialog("Some Qty and Complete Qty is greater than Target Qty.", function(){
                            _this.attr("data-confirm", 1);
                            _this.trigger("submit");
                        });

                        return false;
                    }
                }
            });
        });
    </script>

@endsection