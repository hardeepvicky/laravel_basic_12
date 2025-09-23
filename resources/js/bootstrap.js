import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
<<<<<<< HEAD
=======

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    withCredentials: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    if (window.Echo)
    {
        let userId = document.head.querySelector('meta[name="user-id"]').content;
        window.Echo.private(`App.Models.User.${userId}`).notification((notification) => {
            console.log(notification);

            if (typeof $.toast != "undefined")
            {
                $.toast.init(notification.sub_type, notification.msg).show();
            }
        });
    }
});
>>>>>>> base_v3
