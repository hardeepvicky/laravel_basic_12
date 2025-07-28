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
                    <div class="form-group mb-3">
                        <?php
                        $field = 'name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                            placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                    </div>
                    <div class="form-group mb-3">
                        <?php
                        $field = 'subject';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                            mandatory="true" />
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-sm-12">
                    <div class="form-group mb-3">
                        <?php
                        $field = 'placeholders';
                        $label = get_value_from_list($field_labels, $field);
                        $value = is_array($model->placeholders) ? implode(',', $model->placeholders) : "";
                        ?>
                        <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                            class="choice" placeholder="Enter {{ $label }}" />
                    </div>
                    <div class="form-group mb-3">
                        <?php
                        $field = 'body';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-area :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />
                    </div>
                    <div class="form-group mb-3">
                        <?php
                        $field = 'emailTemplateFile';
                        $label = get_value_from_list($field_labels, $field, $field);
                        $value = $model->emailTemplateFile ? implode(',', $model->emailTemplateFile?->pluck('document_file_id')->toArray()) : "";
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$value"
                            multiple="multiple" />
                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                    </div>
                    <div class="form-group mb-3">
                        <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
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

                $("#emailTemplateFile").browseGallery({
                    opener: "#files-choose"
                });

                var placeholders = $("#placeholders").val();
                placeholders = placeholders.split(",");

                console.log(placeholders);

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
