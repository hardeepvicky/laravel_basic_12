<div class="card general-widget">
    <div class="card-header card-no-border pb-0">
        <h3>Leads</h3>
        <div class="card-header-right">
            <ul class="list-unstyled card-option" style="width: 230px;">
                <li><i class="fa-solid fa-maximize full-card"></i></li>
                <li><i class="fa-solid fa-minus minimize-card"></i></li>
            </ul>
        </div>
    </div>

    <div class="card-body">        
        <div class="border-info border p-2">
            <div class="flex flex-wrap justify-center gap-4 mb-4">
                @php
                    $filters = [
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'last_3_months' => 'Last 3 Months',
                        'last_6_months' => 'Last 6 Months',
                        'last_12_months' => 'Last 12 Months',
                    ];
                @endphp

                @foreach($filters as $value => $label)
                    <button
                        wire:click="$set('period', '{{ $value }}')"
                        class="btn btn-sm transition-all duration-300 ease-in-out {{ $period === $value ? 'btn-secondary ' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div wire:loading wire:target="period" class="text-center text-lg p-8">
                <i class="fas fa-spinner fa-spin"></i> Loading data...
            </div>
        
            @if($data)
                <div wire:loading.remove wire:target="period">
                    <p>
                        Data showing of Date range {{ $data['date_range']['start'] }} - {{ $data['date_range']['end'] }}
                    </p>
                    <div class="row">
                        <div class="col-lg-7">
                            @foreach ($data['counters'] as $arr)
                                <div class="card small-widget bg-light-primary">
                                    <div class="card-body primary }}">
                                        <span class="f-light">
                                            {{ $arr['label'] }}
                                        </span>
                                        <div class="d-flex align-items-end gap-1">
                                            <h4>
                                                {{ $arr['count'] }}
                                            </h4>
                                        </div>
                                        <div class="bg-gradient">
                                            <svg class="stroke-icon svg-fill">
                                                <use href="/assets/svg/icon-sprite.svg#new-order"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-5">
                        </div>
                    </div>
                </div>
            @else
                {{-- Initial state or error state --}}
                <div class="text-center text-lg p-8">
                    Select a filter to view data.
                </div>
            @endif              
        </div>

        <div class="mt-1 pt-1">
            @foreach ($data['change_counters'] as $arr)
                <div class="card small-widget bg-light-primary">
                    <div class="card-body {{ $arr['css_class'] ?? 'primary' }}">
                        <span class="f-light">
                            {{ $arr['label'] }}
                        </span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>
                                {{ $arr['count'] }}
                            </h4>
                            @if(isset($arr['up']) && $arr['up'] > 0)
                            <span class="font-primary f-12 f-w-500">
                                <i class="icon-arrow-up"></i>
                                <span>+{{ $arr['up'] }}%</span>
                            </span>
                            @elseif(isset($arr['down']) && $arr['down'] > 0) 
                            <span class="font-secondary f-12 f-w-500">
                                <i class="icon-arrow-up"></i>
                                <span>+{{ $arr['down'] }}%</span>
                            </span>
                            @endif
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="/assets/svg/icon-sprite.svg#new-order"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>