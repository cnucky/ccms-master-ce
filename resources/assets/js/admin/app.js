import Vue from 'vue';
import vueConfig from './../common';

require('./components');

import router from './route.js';

// require('./../../../semantic/dist/semantic.min.js');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


import store from "./store";

vueConfig.router = router;
vueConfig.store = store;

Vue.mixin({
    methods: {
        $isSuperAdministrator: function () {
            return this.$store.getters.hasPermission("ADMIN_PERM_SUPER");
        },
        $hasAnyPermissionTo: function (permissions) {
            if (typeof permissions === "string") {
                permissions = [permissions];
            }
            if (this.$store.getters.hasPermission("ADMIN_PERM_SUPER")) {
                return true;
            }
            for (var i in permissions) {
                if (this.$store.getters.hasPermission(permissions[i])) {
                    return true;
                }
            }
            return false;
        },
    }
});

const app = new Vue(vueConfig).$mount('#app');
