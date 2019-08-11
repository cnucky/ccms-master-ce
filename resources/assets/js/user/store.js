import Vue from 'vue';
import Vuex from 'vuex';

var md5 = require('blueimp-md5/js/md5.min');

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: userInformation,
        defaultCurrency: defaultCurrency,
    },
    getters: {
        isLoggedIn: state => {
            return state.user instanceof Object && state.user.hasOwnProperty('id');
        },
        user: state => {
            return state.user;
        },
        defaultCurrency: state => {
            return state.defaultCurrency;
        },
        avatar: (state, getters) => (size = 100) => {
            if (getters.isLoggedIn) {
                return "//www.gravatar.com/avatar/" + md5(state.user.email) + "?s=" + size;
            }
            return "";
        },
        name: state => {
            return state.user.name;
        },
    },
    mutations: {
        update: function (state) {
            axios
                .get(route('currentUser'))
                .then((response) => {
                    state.user = response.data;
                }).catch((error) => {
                console.log(error);
                state.user = null;
            })
            ;
        },
        setUser: function (state, user) {
            state.user = user;
        },
        userCredit: function (state, credit) {
            state.user.credit = credit;
        },
        userFrozenCredit: function (state, frozenCredit) {
            state.frozen_credit = frozenCredit;
        },
        clear: function (state) {
            state.user = null;
        },
    }
});