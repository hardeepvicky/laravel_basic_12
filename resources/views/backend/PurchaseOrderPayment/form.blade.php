@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];

    $disabled = isset($screen) && $screen == "edit" ? true : false;
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
                            $field = 'voucher_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                mandatory="true" disabled="true"/>

                            <?php
                            $field = 'purchase_order_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :list="$purchase_order_list"
                                :value="$model->{$field}" 
                                class="select2"
                                mandatory="true" 
                            />
                       
                            <?php
                            $field = 'amount';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    @endif
                                </div>                                
                                <div class="col-sm-8">
                                    <div class="d-flex align-items-start">                                        
                                        <div class="flex-grow-1">
                                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                                :value="$model->{$field}" 
                                                class="validate-float"                                                
                                                placeholder="Enter {{ $label }}"
                                                mandatory="true" 
                                            />                                            
                                        </div>
                                        <div class="p-1">
                                            Pending Amount : <span id="pending_amount"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = "files";
                            $label = get_value_from_list($field_labels, $field, $field);
                            $value = implode(',', $model->paymentFile?->pluck('document_file_id')->toArray() ?? []);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                        <span class="mandatory">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-9">                                        
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" multiple="multiple"  />                        
                                    <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                                </div>
                            </div>

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                rows="3"
                                placeholder="Enter {{ $label }}" />                                
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        var pending_amount;
        $(function() {
            var model_id = '<?= $model->id ?? 0 ?>';
            var currency_formatter = new Intl.NumberFormat('en-IN', { 
                style: 'currency', 
                currency: 'INR',
                maximumFractionDigits: 0
            });

            $("#purchase_order_id").change(function(){
                var v = $(this).val();

                if (v)
                {
                    ajaxGetJson("/purchase-order/ajax_get_pending_payable_amount/" + v + "/" + model_id, function(response){
                        pending_amount = parseFloat(response['pending_amount']);
                        $("#pending_amount").html(pending_amount);
                    });
                }
            });

            $("form").submit(function(){
                var _this = $(this);
                if (pending_amount)
                {
                    var amount = $("#amount").val();
                    amount = amount ? parseFloat(amount) : 0;

                    if (amount > pending_amount)
                    {
                        var confirm = $(this).attr("data-confirm");

                        if (!confirm)
                        {
                            confirmDialog("Amount is greter than Pending Amount.", function(){
                                _this.attr("data-confirm", 1);
                                _this.trigger("submit");
                            });

                            return false;
                        }
                    }
                }
            });

            $("#files").browseGallery({
                opener : "#files-choose",
                multiple : true
            });

            $.events.execAtLast(function(){

                $("#purchase_order_id").trigger("change", {pageLoad : true});
            });
        });
    </script>
@endsection
