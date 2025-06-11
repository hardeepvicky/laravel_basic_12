@extends($layout)

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <label class="pointer">
                <input type="checkbox" id="select-all">
                <b>Select All</b>
            </label>
        </div>
        <div class="col-sm-6 text-right">
            <button type="submit" class="btn btn-primary w-md">Submit</button>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover mt-2 mb-2 i-data-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th data-search="like" data-sort="text">Section</th>
                <th data-search="like" data-sort="text">Sub Section</th>
                <th data-search="like" data-sort="text">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
            @foreach ($sections as $section_group => $section_sub_groups)
                @php
                    $j = 0;
                @endphp
                @foreach ($section_sub_groups as $section_sub_group => $actions)
                    @php
                        $section_group = trim($section_group);
                        $section_group_str = str_replace(' ', '_', $section_group);
                        $section_sub_group = trim($section_sub_group);
                        $section_sub_group_str = str_replace(' ', '_', $section_sub_group);

                        $a = 0;
                    @endphp

                    @foreach ($actions as $action_name => $action_arr)
                        @php
                            $i++;
                            $j++;
                            $a++;
                            
                            $tr_cls = $a == 1 ? 'first-row' : '';
                        @endphp
                        <tr class="{{ $tr_cls }}">
                            <td class="text-center">{{ $i }}</td>
                            <?php if (!is_numeric($section_group)): ?>
                            <td>                                
                                <?php if ($j == 1): ?>
                                    <label class="pointer">
                                        <input type="checkbox" class="chk-select-all section_group_parent"
                                            data-sr-chkselect-children="input.sub_section-<?= $section_group_str ?>">
                                        <b><?= $section_group ?></b>
                                    </label>
                                <?php else: ?>                  
                                    <?= $section_group ?>                  
                                <?php endif; ?>                                
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php if ($a == 1): ?>
                                    <label class="pointer">
                                        <input type="checkbox" class="chk-select-all section_sub_group_parent sub_section-<?= $section_group_str ?>"
                                            data-sr-chkselect-children="input.action-<?= $section_group_str . $section_sub_group_str ?>">
                                        <b><?= $section_sub_group ?></b>
                                    </label>
                                <?php else: ?>
                                    <?= $section_sub_group ?>
                                <?php endif; ?>
                            </td>
                            <?php if (is_numeric($section_group)): ?>
                                <td></td>
                            <?php endif; ?>
                            <td>
                                <label class="pointer">
                                    <input type="checkbox" <?= $action_arr['is_checked'] ? 'checked' : '' ?>
                                        class="action-<?= $section_group_str . $section_sub_group_str ?> chk-action"
                                        name="data[<?= $section_group ?>][<?= $section_sub_group ?>][]" value="<?= $action_name ?>" />
                                    <?= $action_name ?>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="row justify-content-center mt-2">
        <div class="col-sm-3">
            <div>
                <button type="submit" class="btn btn-primary w-md">Submit</button>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#select-all").change(function() {
                $("input.section_group_parent, input.section_sub_group_parent, input.chk-action").prop("checked", $(this).prop("checked"));                
            });
        });
    </script>
@endsection
