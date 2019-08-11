import Vue from 'vue';

Vue.component('admin-guest-navigation', require('./../components/AdminArea/GuestNavigation'));
Vue.component('admin-login-form', require('./../components/AdminArea/LoginForm'));

Vue.component('authenticated-user-sidebar', require('./../components/AdminArea/AuthenticatedUserSidebar'));
Vue.component('authenticated-user-quick-action', require('./../components/AdminArea/AuthenticatedUserQuickAction'));

import draggable from 'vuedraggable'
Vue.component('draggable', draggable);