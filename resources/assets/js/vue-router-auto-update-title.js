const DEFAULT_TITLE = 'Cloud Computing Management System';

const TITLE_SUFFIX = DEFAULT_TITLE;

function getTitleValue(title, vueRouterInstance) {
    if (typeof title === "string")
        return title;
    return title(vueRouterInstance);
}

export default function (vueRouterInstance, defaultTitle = DEFAULT_TITLE, titleSuffix = TITLE_SUFFIX) {
    vueRouterInstance.beforeEach(function (to, from, next) {
        if (to.hasOwnProperty('meta') && to.meta.hasOwnProperty('title'))
            document.title = getTitleValue(to.meta.title, vueRouterInstance) + ' - ' + titleSuffix;
        else
            document.title = DEFAULT_TITLE;
        next();
    });
}