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

const app = new Vue(vueConfig).$mount('#app');
