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
                            $field = 'order_from_type';
                            ?>
                            <x-Inputs.text-field type="hidden" :id="$field" :name="$field" :label="$false" :value="$model->{$field}" />

                            <?php
                                $field = 'contact_id';
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
                                <div class="col-sm-9">
                                    <div class="d-flex align-items-start">
                                        <span class="btn btn-outline-info summary-action-button open_contact_modal" style="padding: 9px 12px;">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2"
                                                data-sr-cascade-target="#sale_order_id" data-sr-cascade-url="/sale-order/ajax_get_list/{contact_id}"
                                                required="true" >
                                                <option value="">Please Select</option>
                                                @foreach ($contact_list as $k => $t)
                                                    @php
                                                        $attr = $k == $t['type'] . "." . $model->{$field} ? 'selected="selected"' : '';
                                                    @endphp
                                                    <option value="{{ $k }}" {!! $attr !!}>
                                                        {{ $t['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error($field)
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = 'sale_order_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :list="$empty_list"
                                :value="$model->{$field}" 
                                class="select2"
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
                                <div class="col-sm-6">
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
            
            $(document).on("input", "input.order_qty", function(){
                load_qty_fields($(this).closest("tr"));
            });

            $(".open_contact_modal").browseContact({
                selectedValues : 0,
                multiple : 0,
                onSelect : function(id_list)
                {
                    var obj = id_list[0];

                    console.log(obj);

                    $("#contact_id").val(obj.type + "." + obj.id).trigger("change");
                    $("#order_from_type").val(obj.type);
                }
            });

            $("#contact_id").cascade({
                beforeGet: function (src, url) {
                    $.loader.init().setInfo("Fetching...").show();

                    var v = $("#contact_id").val();

                    if (v)
                    {
                        contact_id = v.split(".")[1];

                        url = url.replace("{contact_id}", contact_id);
                    }

                    return url;
                },
                afterGet: function (src, dest, response) {
                    $.loader.init().hide();
                    return response;
                },
                afterValueSet: function (src, dest, val) {
                    if (val)
                    {
                        $("#sale_order_id").trigger("change");
                    }
                },
            });

            $("#sale_order_id").change(function(){
                var v = $(this).val();

                if (v)
                {
                    ajaxGetJson("/sale-order/ajax_get_pending_payable_amount/" + v + "/" + model_id, function(response){
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
                opener : "#files-choose"
            });

            $.events.execAtLast(function(){
                $("#contact_id").trigger("change", {pageLoad : true});
            });
        });
    </script>
@endsection
