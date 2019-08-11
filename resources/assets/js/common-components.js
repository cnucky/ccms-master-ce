import Vue from "vue";

Vue.component('slide-fade-transition', require('./components/SlideFadeTransition.vue'));
Vue.component('slide-fade-transition-group', require('./components/SlideFadeTransitionGroup'));
Vue.component('slide-fade-router-view', require('./components/TransitionRouterView.vue'));
Vue.component('transition-list', require('./components/TransitionList'));
Vue.component('csrf-field', require('./components/CSRFField.vue'));
Vue.component('left-fixed-menu', require('./components/LeftFixedMenu.vue'));
Vue.component('navigation-container', require('./components/NavigationContainer.vue'));
Vue.component('main-search', require('./components/MainSearch.vue'));
Vue.component('navigation', require('./components/ClientArea/Navigation.vue'));
Vue.component('guest-navigation', require('./components/GuestNavigation.vue'));
Vue.component('user-guest-navigation', require('./components/ClientArea/GuestNavigation.vue'));
Vue.component('semantic-ui-confirm-modal', require('./components/SemanticUI/ConfirmModal'));
Vue.component('semantic-ui-inverted-dimmer', require('./components/SemanticUI/InvertedDimmer'));
Vue.component('semantic-ui-loader', require('./components/SemanticUI/Loader'));
Vue.component('semantic-ui-checkbox', require('./components/SemanticUI/Checkbox.vue'));
Vue.component('semantic-ui-message', require('./components/SemanticUI/Message.vue'));
Vue.component('semantic-ui-basic-message', require('./components/SemanticUI/BasicMessage'));
Vue.component('semantic-ui-delay-callback-message', require('./components/SemanticUI/DelayCallbackMessage'));
Vue.component('semantic-ui-dynamic-message', require('./components/SemanticUI/DynamicMessage.vue'));
Vue.component('semantic-ui-button', require('./components/SemanticUI/Button'));
Vue.component('semantic-ui-pagination', require('./components/SemanticUI/Pagination'));

Vue.component('semantic-ui-dropdown-menu', require('./components/SemanticUI/DropdownMenu'));

Vue.component('floating-message', require('./components/FloatingMessage'));

Vue.component('linked-label-checkbox', require('./components/SemanticUI/LinkedLabelCheckbox.vue'));

Vue.component('login-form-container', require('./components/LoginFormContainer.vue'));

Vue.component('form-modal', require('./components/ModelIndex/FormModal'));

Vue.component('sortable-table', require('./components/SortableTable'));
Vue.component('index-table-search-input', require('./components/ModelIndex/IndexTableSearchInput'));
Vue.component('model-index-refresh-button', require('./components/ModelIndex/RefreshButton'));
Vue.component('model-index-mass-destroy-button', require('./components/ModelIndex/MassDestroyButton'));
Vue.component('model-index-create-button', require('./components/ModelIndex/CreateModelButton'));
Vue.component('model-index-page-number-input', require('./components/ModelIndex/PageNumberInput'));
Vue.component('model-index-pre-page-item-selector', require('./components/ModelIndex/PrePageItemSelector'));
Vue.component('javascript-sortable-table', require('./components/JavascriptSortableTable'));
Vue.component('show-value', require('./components/ColumnComponent/ShowValue'));
Vue.component('long-text-column', require('./components/ColumnComponent/LongTextColumn'));
Vue.component('local-time-column', require('./components/ColumnComponent/LocalTimeColumn'));
Vue.component('duration-column', require('./components/ColumnComponent/DurationColumn'));
Vue.component('amount-column', require('./components/ColumnComponent/AmountColumn'));