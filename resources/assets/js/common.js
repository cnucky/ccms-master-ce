import Vue from "vue";
import VueRouter from 'vue-router';

import moment from 'moment-timezone'

moment.tz.setDefault(serverTimezone);
moment.locale(document.documentElement.lang ? document.documentElement.lang : "zh-CN");

Vue.use(require('vue-moment'), {
    moment,
});

var Decimal = require('decimal.js-light');
window.Decimal = Decimal;

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

// require('bootstrap-sass');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.updateCSRFToken = function (newToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
    token.content = newToken;
};

window.getCSRFToken = function () {
    return axios.get(route('currentCSRFToken'));
};

window.refreshCSRFToken = function (onSuccess) {
    return getCSRFToken().then((response) => {
        var validToken = response.data.token;
        updateCSRFToken(validToken);
        if (typeof onSuccess === "function") {
            return onSuccess(validToken);
        }
    });
};

import VueClipboard from 'vue-clipboard2'

Vue.use(VueClipboard);

function setErrorsIfAvailable(error) {
    if (!error.hasOwnProperty('config')) {
        console.log(error);
        return;
    }

    if (!error.config.hasOwnProperty('vueInstance'))
        return;
    var vueInstance = error.config.vueInstance;
    if (!(vueInstance instanceof Vue))
        return;

    var showError;
    if (error.config.hasOwnProperty("useFloatingMessage") || !vueInstance.hasOwnProperty("errors")) {
        showError = function (error) {
            vueInstance.negativeFloatingMessage(error);
        }
    } else {
        showError = function (error) {
            vueInstance.errors = error;
        }
    }

    if (error.response) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        // console.log(error.response.data);
        // console.log(error.response.status);
        // console.log(error.response.headers);

        if (error.response.data.hasOwnProperty("errors"))
            showError(error.response.data.errors);
        else if (error.response.data.hasOwnProperty("message")) {
            showError({0: error.response.data.message});
        } else {
            showError({0: "未知错误"});
        }
    } else {
        // Something happened in setting up the request that triggered an Error
        showError({0: error.message});
        // console.log('Error', error.message);
    }
}

axios.interceptors.response.use(null, (error) => {
    if (error.config && error.response && error.response.status === 419) {
        var counterValue = 0;

        if (error.config.hasOwnProperty("status419Counter")) {
            counterValue = error.config.status419Counter;
        }

        if (counterValue < 3) {
            error.config.status419Counter = counterValue + 1;

            return refreshCSRFToken((token) => {
                error.config.headers['X-CSRF-TOKEN'] = token;
                return axios.request(error.config);
            });
        }
    }

    setErrorsIfAvailable(error);
    return Promise.reject(error);
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key',
//     cluster: 'mt1',
//     encrypted: true
// });

require('./common-components');

require('./init-axios-progress-bar');

Vue.mixin({
    data: function () {
        return {
            $serverTimezone: serverTimezone,
        };
    },
    methods: {
        route: route,
        confirmModal: function () {
            return this.$root.$refs.confirmModal.init();
        },
        floatingMessage: function () {
            return this.$root.$refs.floatingMessage;
        },
        showFloatingMessages: function (type, messages, header, delay) {
            this.floatingMessage().showMessages(type, messages, header, delay);
        },
        positiveFloatingMessage: function (messages, header, delay) {
            this.floatingMessage().showMessages("positive", messages, header, delay);
        },
        negativeFloatingMessage: function (messages, header, delay) {
            this.floatingMessage().showMessages("negative", messages, header, delay);
        },
        $errorToNegativeFloatingMessage: function (error) {
            console.log(error);
            this.negativeFloatingMessage(error.toString());
        },
        $globalErrnoHandler: function (data, onNotGlobalErrno = null) {
            if (typeof data === "object" && data.hasOwnProperty("errno")) {
                var errno = data.errno;
                if (errno === 20040) {
                    this.negativeFloatingMessage("要执行此操作，帐号余额不可少于" + this.$store.getters.defaultCurrency.prefix + data.min + "，当前仅有" + this.$store.getters.defaultCurrency.prefix + data.current);
                } else if (this.$te("globalError." + errno)) {
                    this.negativeFloatingMessage(this.$t("globalError." + errno));
                }
            } else {
                if (typeof onNotGlobalErrno === "function")
                    onNotGlobalErrno(data);
                else if (data.hasOwnProperty("message")) {
                    this.negativeFloatingMessage(data.message);
                }
            }
        },
        $axiosCatchError2Console: function (error) {
            console.log(error);
        },
        $asServerTimeMoment: function (time) {
            return this.$moment.tz(time, this.$data.$serverTimezone);
        },
        $toLocalMoment: function (time) {
            return this.$moment(time).local();
        },
        $toLocalTime: function (time) {
            if (this.$store.getters.user && this.$store.getters.user.timezone !== null) {
                return this.$toLocalMoment(time).utcOffset(this.$store.getters.user.timezone).format("YYYY-MM-DD HH:mm:ss");
            }
            return this.$toLocalMoment(time).local().format("YYYY-MM-DD HH:mm:ss");
        },
        $momentFrom: function (time) {
            return this.$toLocalMoment(time).fromNow();
        },
        $axiosGet: function (url, onSuccess, onCompleted = () => {}) {
            this.$axiosPromiseHandler(axios.get(url, {vueInstance: this}), onSuccess, onCompleted);
        },
        $axiosPost: function (url, data, onSuccess, onCompleted = () => {}) {
            this.$axiosPromiseHandler(axios.post(url, data, {vueInstance: this}), onSuccess, onCompleted);
        },
        $axiosPatch: function (url, data, onSuccess, onCompleted = () => {}) {
            this.$axiosPromiseHandler(axios.patch(url, data, {vueInstance: this}), onSuccess, onCompleted);
        },
        $axiosDelete: function (url, onSuccess, onCompleted = () => {}) {
            this.$axiosPromiseHandler(axios.delete(url, {vueInstance: this}), onSuccess, onCompleted);
        },
        $axiosPromiseHandler: function (promise, onSuccess, onCompleted = () => {}) {
            promise
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        onSuccess(data);
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(onCompleted)
            ;
        }
    }
});


Vue.use(VueRouter);

import i18n from './laravel-vue-i18n';

export default {
    i18n,
    created: function () {
        // this.$store.commit("update");
    },
};