@extends($layout)

@section('content')
    <?php
    use App\Models\Task;
    
    $page_header_links[] = ['title' => 'List', 'url' => route($routePrefix . '.index')];
    $page_header_links[] = ['title' => 'Add', 'url' => route($routePrefix . '.create')];
    
    ?>

    @include($partial_path . '.page_header')

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link text-primary active" id="tab_my_task" data-bs-toggle="tab" href="#tab_my_task_block"
                role="tab" aria-controls="tab_my_task_block" aria-selected="true">
                My Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-primary" id="tab_other_task" data-bs-toggle="tab" href="#tab_other_task_block" role="tab"
                aria-controls="tab_other_task_block" aria-selected="true">
                Assign To Others
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active show fade" id="tab_my_task_block" role="tabpanel" aria-labelledby="tab_my_task">
            <div class="card" style="border-radius: 0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-table table-sm">
                            <thead>
                                <tr class="border-bottom-primary">
                                    <th>Sr. No.</th>
                                    <th>{!! sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name'))  !!}</th>
                                    <th>{!! sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) !!}</th>
                                    <th>{!! sortable_anchor('start_date_time', get_value_from_list($field_labels, 'start_date_time', 'start_date_time')) !!} </th>
                                    <th>Duration</th>
                                    <th>{!! sortable_anchor('end_date_time', get_value_from_list($field_labels, 'end_date_time', 'end_date_time')) !!} </th>
                                    <th>Time Left</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($my_tasks as $i => $record)
                                    <tr>
                                        <th scope="row">{{ $i + 1 }}</th>
                                        <td class="name">{{ $record->name }}</td>
                                        <td>{{ $record->priority }}</td>
                                        <td>
                                            <x-Backend.celec-date-time :value="$record->start_date_time"/>
                                        </td>
                                        <td>
                                            {{ $record->duration }}
                                            {{ get_value_from_list($duration_type_list, $record->duration_type) }}
                                        </td>
                                        <td>
                                            <x-Backend.celec-date-time :value="$record->end_date_time"/>
                                        </td>
                                        <td>{{ $record->time_left }}</td>
                                        <td>
                                            {{ get_value_from_list($status_list, $record->status) }}
                                        </td>
                                        <td>
                                            @if ($record->status == Task::STATUS_ASSIGNED)
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_confirm', ['id' => $record->id]) }}">
                                                    Confirm Now
                                                </a>

                                                <a class="btn btn-secondary btn-sm mb-2 status_in_progress"
                                                    href="{{ route($routePrefix . '.status_in_progress', ['id' => $record->id]) }}">
                                                    Move To In-Progress
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_CONFIRM]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_in_progress', ['id' => $record->id]) }}">
                                                    Move To In-Progress
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_IN_PROGRESS]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_confirm', ['id' => $record->id]) }}">
                                                    Revert To Previous Status
                                                </a>

                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_complete', ['id' => $record->id]) }}">
                                                    Complete Now
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_COMPLETED]))
                                                <a class="btn btn-secondary btn-sm mb-2 status_verified"
                                                    href="{{ route($routePrefix . '.status_verified', ['id' => $record->id]) }}">
                                                    Verify Now
                                                </a>
                                            @endif

                                            <a class="btn btn-secondary btn-sm mb-2"
                                                href="{{ route($routePrefix . '.index', ['id' => $record->id]) }}">
                                                Go To List
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab_other_task_block" role="tabpanel" aria-labelledby="tab_other_task">
            <div class="card" style="border-radius: 0">
                <div class="card-body">
                    <form method="GET" id="form_today_other_task" class="summary_search" action="{{ route($routePrefix . '.today_other_task') }}" data-load_html_at="#today_other_task">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <div class="row mb-4">                        
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                @php
                                $field = "dept_id";
                                $label = get_value_from_list($field_labels, $field, $field);
                                @endphp
                                <x-Inputs.drop-down name="other_dept_id" :label="$label" :value="$search['other_dept_id']" :list="$department_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                @php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                @endphp
                                <x-Inputs.drop-down name="other_assign_to_user_id" :label="$label" :value="$search['other_assign_to_user_id']"
                                    :list="$user_list" class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                @php
                                $field = 'status';
                                $label = get_value_from_list($field_labels, $field, $field);
                                @endphp
                                <x-Inputs.drop-down name="other_task_status" :label="$label" :value="$search['other_task_status']" :list="$status_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                @php
                                $field = "name";
                                $label = get_value_from_list($field_labels, $field, $field);
                                @endphp
                                <x-Inputs.text-field name="other_task_name" :label="$label" value="{{ $search["other_task_name"] }}" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                @php
                                $field = "tag_name";
                                $label = get_value_from_list($field_labels, $field, $field);
                                @endphp
                                <x-Inputs.text-field name="other_tag_name" :label="$label" value="{{ $search["other_tag_name"] }}" />
                            </div>
                        </div>
                        @php $false = false; @endphp
                        <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
                    </form>                    
                </div>
            </div>

            <div id="today_other_task" class="index_table">                
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-task-verify" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Task Verify</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-Form.start action="" method="PUT" />

                    <div class="card height-equal">
                        <div class="card-header card-no-border pb-0">
                            <h3>Rate It</h3>
                            <p class="desc mb-0 mt-1">

                            </p>
                        </div>
                        <div class="card-body">
                            <div class="emoji-wrapper">
                                <input id="star-1" type="radio" name="rating" value="1">
                                <input id="star-2" type="radio" name="rating" value="2">
                                <input id="star-3" type="radio" name="rating" value="3">
                                <input id="star-4" type="radio" name="rating" value="4">
                                <input id="star-5" type="radio" name="rating" value="5">
                                <div class="content">
                                    <div class="outer">
                                        <ul class="emojis">
                                            <li class="slideImg">
                                                <img src="/assets/images/other-images/emoji/angry.png" alt="">
                                            </li>
                                            <li>
                                                <img src="/assets/images/other-images/emoji/sad.png" alt="">
                                            </li>
                                            <li>
                                                <img src="/assets/images/other-images/emoji/think.png" alt="">
                                            </li>
                                            <li>
                                                <img src="/assets/images/other-images/emoji/smile.png" alt="">
                                            </li>
                                            <li>
                                                <img src="/assets/images/other-images/emoji/love.png" alt="">
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="stars">
                                        <label class="star-1 fas fa-star" for="star-1"></label>
                                        <label class="star-2 fas fa-star" for="star-2"></label>
                                        <label class="star-3 fas fa-star" for="star-3"></label>
                                        <label class="star-4 fas fa-star" for="star-4"></label>
                                        <label class="star-5 fas fa-star" for="star-5"></label>
                                    </div>
                                </div>
                                <div class="footer"><span class="text"></span><span class="numb"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        @php
                        $field = 'review_remarks';
                        $label = get_value_from_list($field_labels, $field, $field);
                        @endphp
                        <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="5"
                            placeholder="Enter {{ $label }}" />
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $.events.execAtLast(function() {
                $("a.status_verified").click(function(e) {

                    var _tr = $(this).closest("tr");

                    var modal = $('#modal-task-verify');

                    e.preventDefault();

                    modal.modal("show");

                    modal.find("form").attr("action", $(this).attr("href"));

                    var html = "";
                    if (_tr.find(".assignToUser").length > 0) {
                        html += _tr.find(".assignToUser").html();

                        html += " -> ";
                    }

                    html += _tr.find(".name").html();

                    modal.find(".desc").html(html);

                    return false;
                });

                $('#modal-task-verify form').ajaxForm({
                    beforeSubmit: function(arr, $form, options) {

                        $.loader.init().setInfo("Requesting....").show();

                        return true;
                    },
                    uploadProgress: function(event, position, total, percentComplete) {},
                    error: function(xhr, status, errorString) {
                        $.loader.hide();

                        response = xhr.responseText.trim();

                        if (response.length == 0) {
                            $.events.onAjaxError("JSON Parse Error", "Empty Response");
                            return false;
                        }

                        try {
                            var responseJson = JSON.parse(response);
                        } catch (e) {
                            $.events.onAjaxError("JSON Parse Error", response);
                            return false;
                        }

                        var modal = $('#modal-task-verify');

                        form_errors(modal.find('form'), responseJson);
                    },
                    success: function(responseText, statusText, xhr) {
                        ajaxHandleResponse(null, responseText, function(response) {
                            var modal = $('#modal-task-verify');
                            modal.modal("hide");

                            $.loader.setInfo("Reloading Page...");

                            location.reload(true);
                        });
                    }
                });

                $('#modal-task-verify .submit').click(function() {
                    $('#modal-task-verify form').trigger("submit");
                });

                $("#form_today_other_task").trigger("submit", {pageLoad : true});
            });
        })
    </script>
@endsection
