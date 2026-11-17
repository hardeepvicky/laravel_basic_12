<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\FileUtility;
use App\Models\Notification;

?>
<header class="page-header row">
    <div class="logo-wrapper d-flex align-items-center col-auto">
        <a href="{{ route('home') }}">
                <img class="light-logo img-fluid" src="/assets/images/logo/logo.jpg" alt="logo" />
                <img class="dark-logo img-fluid" src="/assets/images/logo/logo.jpg" alt="logo" />
            </a>
        <a class="close-btn toggle-sidebar" href="javascript:void(0)">
            <svg class="svg-color">
                <use href="/assets/svg/iconly-sprite.svg#Category"></use>
            </svg>
        </a>
    </div>
    <div class="page-main-header col">
        <div class="header-left">            
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
                <li class="custom-dropdown open-notification-panel">
                    <a href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#notification"></use>
                        </svg>
                    </a>

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
                        </ul>
                    </div>
                </li>
                <li>
                    <a class="full-screen" href="javascript:void(0)">
                        <svg>
                            <use href="/assets/svg/iconly-sprite.svg#scanfull"></use>
                        </svg>
                    </a>
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
                @endauth
            </ul>
        </div>
    </div>
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
    });
</script>
