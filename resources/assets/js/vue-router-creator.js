import VueRouter from 'vue-router';
import vueRouterAutoUpdateTitle from "./vue-router-auto-update-title";

export default function (vueRouterConfig) {
    var vueRouter = new VueRouter(vueRouterConfig);
    vueRouterAutoUpdateTitle(vueRouter);

    return vueRouter;
}