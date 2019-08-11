import Vue from 'vue';
import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales.generated';

Vue.use(VueInternationalization);

const lang = document.documentElement.lang;
// or however you determine your current app locale

export default new VueInternationalization({
    locale: lang,
    fallbackLocale: 'zh-CN',
    messages: Locale
});
