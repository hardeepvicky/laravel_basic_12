<x-Form.start action="" method="PUT" />
    <ul class="ml-3" style="list-style: circle;">
        @foreach($model->taskTodo as $taskTodo)                                                
        <li>
            <?php
            $label = $taskTodo->todo;
            ?>
            <x-Inputs.checkbox name="todo[{{ $taskTodo->id }}]" :label="$label" :value="$taskTodo->is_completed"/>
        </li>
        @endforeach
    </ul>
    
    <div class="form-group mb-3">
        @php
        $field = 'description';
        $label = get_value_from_list($field_labels, $field, $field);
        @endphp
        <x-Inputs.text-area :id="$field" :name="$field" :label="$label" :value="$model->description" rows="5" placeholder="Enter {{ $label }}" />
    </div>
</form>