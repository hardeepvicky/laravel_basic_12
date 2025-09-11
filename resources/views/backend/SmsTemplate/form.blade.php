<?php

use App\Models\Task;
use App\Helpers\DateUtility;

$false = false;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    $empty_list = [];
    $is_disabled = $model->name && !is_developer_role_login();
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xxl-6">
                        <?php
                        $field = 'name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}" :isDisabled="$is_disabled"
                            placeholder="Enter {{ $label }}" 
                            layout="horizontal"
                            mandatory="true" 
                            autocomplete="off" />

                        <?php
                        $field = 'template_name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                            layout="horizontal"                            
                            mandatory="true" />

                        <?php
                        $field = 'language';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                            layout="horizontal"                            
                            mandatory="true" />

                        <?php
                        $field = 'placeholders';
                        $label = get_value_from_list($field_labels, $field);
                        $value = is_array($model->placeholders) ? implode(',', $model->placeholders) : "";
                        ?>
                        <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value" :isDisabled="$is_disabled"
                            class="choice" 
                            placeholder="Enter {{ $label }}" 
                            layout="horizontal"
                            />

                        <div class="textarea-wrapper">
                            <?php
                            $field = 'template';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            

                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-9">
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$false" :value="$model->{$field}"/>
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>
                            </div>
                        </div>
                            
                        <div class="row">
                            <div class="col-sm-3">
                                
                            </div>
                            <div class="col-sm-9">
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                            </div>
                        </div>
                </div>
             </div>
            <div class="row">
                <div class="col-xl-8">
                    <x-Backend.form-common-footer-buttons />
                </div>
            </div>
        </div>
    </div>
    </form>

    <script>
        $(function() {
            var screen_name = '<?= $screen_name ?>';
            $.events.execAtLast(function() {

                var placeholders = $("#placeholders").val();
                placeholders = placeholders.split(",");

                var suggestion_data = [];

                for(var i in placeholders)
                {
                    suggestion_data.push({
                        name : "@" + placeholders[i],
                        display : placeholders[i],
                    });
                }

                placeholder_suggestion($("#template"), $("#template-suggestion-box"), suggestion_data);

                $("#body").summernote({
                    height: 400,
                    placeholder: 'type with @ to get list of placehoders',
                    hint: {
                        mentions: placeholders,
                        match: /\B@(\w*)$/,
                        search: function(keyword, callback) {
                            callback($.grep(this.mentions, function(item) {
                                return item.indexOf(keyword) === 0;
                            }));
                        },
                        content: function (item) {
                            return '@' + item;
                        }
                    }
                })

            });
        })
    </script>
@endsection
