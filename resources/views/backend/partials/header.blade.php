<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\FileUtility;
<<<<<<< HEAD
?>
<header class="page-header row">
    <div class="logo-wrapper d-flex align-items-center col-auto">
        <a href="{{ route('home') }}"><img class="light-logo img-fluid" src="/assets/images/logo/logo1.png" alt="logo" /><img class="dark-logo img-fluid" src="/assets/images/logo/logo-dark.png" alt="logo" /></a>
=======
use App\Models\Notification;

?>
<header class="page-header row">
    <div class="logo-wrapper d-flex align-items-center col-auto">
        <a href="{{ route('home') }}">
                <img class="light-logo img-fluid" src="/assets/images/logo/logo.png" alt="logo" />
                <img class="dark-logo img-fluid" src="/assets/images/logo/logo.png" alt="logo" />
            </a>
>>>>>>> base_v3
        <a class="close-btn toggle-sidebar" href="javascript:void(0)">
            <svg class="svg-color">
                <use href="/assets/svg/iconly-sprite.svg#Category"></use>
            </svg>
        </a>
    </div>
    <div class="page-main-header col">
        <div class="header-left">
            <form class="form-inline search-full col" action="#" method="get">
                <div class="form-group w-100">
                    <div class="Typeahead Typeahead--twitterUsers">
<<<<<<< HEAD
                        <div class="u-posRelative">
                            <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Admiro .." name="q" title="" autofocus="autofocus" />
                            <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...</span></div>
                            <i class="close-search" data-feather="x"></i>
=======
                        <div class="u-posRelative d-flex align-items-center">
                            <input class="header-search demo-input py-0 Typeahead-input form-control-plaintext w-100"
                            type="text" placeholder="Type to Search..." name="q" title="" /><i
                            class="search-bg iconly-Search icli"></i>
>>>>>>> base_v3
                        </div>
                        <div class="Typeahead-menu"></div>
                    </div>
                </div>
            </form>
            <div class="form-group-header d-lg-block d-none">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative d-flex align-items-center">
<<<<<<< HEAD
                        <input class="demo-input py-0 Typeahead-input form-control-plaintext w-100" type="text" placeholder="Type to Search..." name="q" title="" /><i class="search-bg iconly-Search icli"></i>
=======
                        <input id="header-search" class="header-search demo-input py-0 Typeahead-input form-control-plaintext w-100"
                            type="text" placeholder="(F2) Type to Search..." name="q" title="" /><i
                            class="search-bg iconly-Search icli"></i>
>>>>>>> base_v3
                    </div>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <ul class="header-right">
                <li class="search d-lg-none d-flex">
                    <a href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#Search"></use>
                        </svg>
                    </a>
                </li>
                <li>
                    <a class="dark-mode" href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#moondark"></use>
                        </svg>
                    </a>
                </li>
<<<<<<< HEAD
                <li class="custom-dropdown">
=======
                <li class="custom-dropdown open-notification-panel">
>>>>>>> base_v3
                    <a href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#notification"></use>
                        </svg>
                    </a>
<<<<<<< HEAD
                    <span class="badge rounded-pill badge-primary">4</span>
                    <div class="custom-menu notification-dropdown py-0 overflow-hidden">
                        <h3 class="title bg-primary-light dropdown-title">Notification <span class="font-primary">View all</span></h3>
                        <ul class="activity-timeline">
                            <li class="d-flex align-items-start">
                                <div class="activity-line"></div>
                                <div class="activity-dot-primary"></div>
                                <div class="flex-grow-1">
                                    <h6 class="f-w-600 font-primary">
                                        30-04-2024<span>Today</span>
                                        <span class="circle-dot-primary float-end">
                                            <svg class="circle-color">
                                                <use href="/assets/svg/iconly-sprite.svg#circle"></use>
                                            </svg>
                                        </span>
                                    </h6>
                                    <h5>Alice Goodwin</h5>
                                    <p class="mb-0">Fashion should be fun. It shouldn't be labelled intellectual.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="activity-dot-secondary"></div>
                                <div class="flex-grow-1">
                                    <h6 class="f-w-600 font-secondary">
                                        28-06-2024<span>1 hour ago</span>
                                        <span class="float-end circle-dot-secondary">
                                            <svg class="circle-color">
                                                <use href="/assets/svg/iconly-sprite.svg#circle"></use>
                                            </svg>
                                        </span>
                                    </h6>
                                    <h5>Herry Venter</h5>
                                    <p>I am convinced that there can be luxury in simplicity.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="activity-dot-primary"></div>
                                <div class="flex-grow-1">
                                    <h6 class="f-w-600 font-primary">
                                        04-08-2024<span>Today</span>
                                        <span class="float-end circle-dot-primary">
                                            <svg class="circle-color">
                                                <use href="/assets/svg/iconly-sprite.svg#circle"></use>
                                            </svg>
                                        </span>
                                    </h6>
                                    <h5>Loain Deo</h5>
                                    <p>I feel that things happen for open new opportunities.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="activity-dot-secondary"></div>
                                <div class="flex-grow-1">
                                    <h6 class="f-w-600 font-secondary">
                                        12-11-2024<span>Yesterday</span>
                                        <span class="float-end circle-dot-secondary">
                                            <svg class="circle-color">
                                                <use href="/assets/svg/iconly-sprite.svg#circle"></use>
                                            </svg>
                                        </span>
                                    </h6>
                                    <h5>Fenter Jessy</h5>
                                    <p>Sometimes the simplest things are the most profound.</p>
                                </div>
                            </li>
=======

                    @if (isset($unread_notification_count) && $unread_notification_count)
                        <span class="badge rounded-pill badge-primary">
                            {{ $unread_notification_count }}
                        </span>
                    @endif

                    <div class="custom-menu notification-dropdown py-0 overflow-hidden">
                        <h3 class="title bg-primary-light dropdown-title">Notification
                            <a class="font-primary" href="{{ route('notification.my') }}">
                                View all
                            </a>
                        </h3>
                        <ul class="activity-timeline">
                            @if (isset($unread_notifications) && $unread_notifications)
                                @foreach ($unread_notifications as $notification)
                                    :
                                    <li class="d-flex align-items-start">
                                        <div class="activity-line"></div>
                                        @if ($notification->sub_type == Notification::SUB_TYPE_INFO)
                                            <div class="activity-dot-primary"></div>
                                        @elseif($notification->sub_type == Notification::SUB_TYPE_CRITICAL)
                                            <div class="activity-dot-danger"></div>
                                        @else
                                            <div class="activity-dot-secondary"></div>
                                        @endif

                                        <div class="flex-grow-1">
                                            <h6 class="f-w-600 font-primary">
                                                <span>{{ if_date($notification->created_at) }}</span>
                                                {{ $notification->created_at_original->diffForHumans() }}
                                            </h6>
                                            <h5>
                                                @if ($notification->from_user_id === 0)
                                                    System
                                                @else
                                                    {{ $notification->fromUser?->getDisplayName() ?? 'NA' }}
                                                @endif
                                            </h5>
                                            <p class="mb-0">
                                                @if (isset($notification->data['msg']) && $notification->data['msg'])
                                                    <span>{{ $notification->data['msg'] }}</span>
                                                @endif

                                                @if (isset($notification->data['link']) && $notification->data['link'])
                                                    <a href="{{ $notification->data['link'] }}">Open Link</a>
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
>>>>>>> base_v3
                        </ul>
                    </div>
                </li>
                <li>
                    <a class="full-screen" href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#scanfull"></use>
                        </svg>
                    </a>
<<<<<<< HEAD
                </li>                
                @auth
                @php
                $user = Auth::user();
                @endphp
                <li class="profile-nav custom-dropdown">
                    <div class="user-wrap">
                        @if(isset($user->profile_image) && $user->profile_image)
                        <div class="user-img"><img src="{{ FileUtility::get($user->profile_image) }}" alt="user" /></div>
                        @endif
                        <div class="user-content">
                            <!-- <h6>Ava Davis</h6> -->
                            <p class="mb-0">{{ $user->name }}<i class="fa-solid fa-chevron-down"></i></p>
                        </div>
                    </div>
                    <div class="custom-menu overflow-hidden">
                        <ul class="profile-body">
                            <li class="d-flex">
                                <svg class="svg-color">
                                    <use href="/assets/svg/iconly-sprite.svg#Profile"></use>
                                </svg>
                                @if (Route::has('user.change.password'))
                                <a class="ms-2" href="{{ route('user.change.password') }}">Change Password</a>
                                @else
                                Route not exist : user.change.password
                                @endif
                            </li>
                            <!-- <li class="d-flex">
                                <svg class="svg-color">
                                    <use href="../assets/svg/iconly-sprite.svg#Message"></use>
                                </svg>
                                <a class="ms-2" href="letter-box.html">Inbox</a>
                            </li>
                            <li class="d-flex">
                                <svg class="svg-color">
                                    <use href="../assets/svg/iconly-sprite.svg#Document"></use>
                                </svg>
                                <a class="ms-2" href="to-do.html">Task</a>
                            </li> -->

                            <li class="d-flex">
                                <svg class="svg-color">
                                    <use href="/assets/svg/iconly-sprite.svg#Login"></use>
                                </svg>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>

                            </li>
                        </ul>
                    </div>
                </li>
=======
                </li>
                @auth
                    @php
                        $user = Auth::user();
                    @endphp
                    <li class="profile-nav custom-dropdown">
                        <div class="user-wrap">
                            @if (isset($user) && $user)
                                <div class="user-img">
                                    <img src="{{ $user->getProfileImage() }}" alt="user" style="background:none" />
                                </div>
                            @endif
                            <div class="user-content">
                                <!-- <h6>Ava Davis</h6> -->
                                <p class="mb-0">{{ $user->person_name }}<i class="fa-solid fa-chevron-down"></i></p>
                            </div>
                        </div>
                        <div class="custom-menu overflow-hidden">
                            <ul class="profile-body">
                                <li class="d-flex">
                                    <svg class="svg-color">
                                        <use href="/assets/svg/iconly-sprite.svg#Profile"></use>
                                    </svg>
                                    @if (Route::has('user.change.password'))
                                        <a class="ms-2" href="{{ route('user.change.password') }}">Change Password</a>
                                    @else
                                        Route not exist : user.change.password
                                    @endif
                                </li>
                                <!-- <li class="d-flex">
                                                                                        <svg class="svg-color">
                                                                                            <use href="../assets/svg/iconly-sprite.svg#Message"></use>
                                                                                        </svg>
                                                                                        <a class="ms-2" href="letter-box.html">Inbox</a>
                                                                                    </li>
                                                                                    <li class="d-flex">
                                                                                        <svg class="svg-color">
                                                                                            <use href="../assets/svg/iconly-sprite.svg#Document"></use>
                                                                                        </svg>
                                                                                        <a class="ms-2" href="to-do.html">Task</a>
                                                                                    </li> -->

                                <li class="d-flex">
                                    <svg class="svg-color">
                                        <use href="/assets/svg/iconly-sprite.svg#Login"></use>
                                    </svg>
                                    <form class="ms-2" action="{{ route('logout') }}" method="POST"
                                        style="margin-block-end:0">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>

                                </li>
                            </ul>
                        </div>
                    </li>
>>>>>>> base_v3
                @endauth
            </ul>
        </div>
    </div>
<<<<<<< HEAD
</header>
=======
</header>

<style>
    .twitter-typeahead .tt-suggestion {
        padding: 3px 20px;
        line-height: 24px;
        cursor: pointer;
        opacity: 1;
    }

    .tt-suggestion:hover
    {
        background-color: rgba(48, 142, 135, .2);
    }

    .tt-suggestion.tt-cursor {
    }

    .tt-suggestion p {
        margin: 0;        
    }

    .tt-highlight {
        /* For highlighting query matches */
        font-weight: bold;
    }

    .twitter-typeahead{
        width: 95%;
    }

    .twitter-typeahead .tt-menu{
        min-width: 30rem;
    }

    .twitter-typeahead .tt-menu{
        margin-top: 12px;
    }
</style>


<script>
    $(function() {
        $(".open-notification-panel").click(function() {

            var unread_notification_count = parseInt('<?= $unread_notification_count ?? 0 ?>');

            if (unread_notification_count) {
                var url = "/notification/ajax_mark_read";

                $.get(url, function(response) {
                    ajaxHandleResponse(url, response, function(response) {

                    });
                });
            }
        });

        $.events.execAtLast(function() {

            // constructs the suggestion engine
            var tagEngine = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: '/tags/ajax_get_list_for_header?limit=5',
                remote: {
                    url: '/tags/ajax_get_list_for_header?query=%QUERY',
                    wildcard: '%QUERY',
                    transform: function(response) {
                        var result = $.map(response['data'], function(item) {
                            return item;
                        });

                        console.log(result)
                        return result;
                    }
                },
            });

            function search(q, sync) {
                if (q === '') {
                    // sync(tagEngine.get('Detroit Lions', 'Green Bay Packers', 'Chicago Bears'));
                } else {
                    tagEngine.search(q, sync);
                }
            }

            $('.header-search').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'tags',
                display : 'display',
                source: tagEngine,
                templates: {
                    suggestion: function(data) {
                        return `<div>
                            <a href="${data.url}">
                                <span> ${data.display}</span>
                            </a>
                        </div>
                    `;
                    }
                }
            }).bind('typeahead:select', function(ev, suggestion) {
                if (typeof suggestion.url == "string")
                {
                    $.loader.init().setInfo("Redirecting....").show();
                    window.location.href = suggestion.url;
                }
            });
        });


        $(document).on('keydown', function(event) {
            // event.which is deprecated, but widely supported.
            // event.key is more modern, but F-keys can be tricky with it.
            // event.code is also an option.
            if (event.which === 113) {
                event.preventDefault();
                $("#header-search").focus();
            }
        });
    });
</script>
>>>>>>> base_v3
