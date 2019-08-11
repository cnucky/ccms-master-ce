var vueRouterConfig = {
    mode: 'history',
    routes: [
        {
            name: 'welcome',
            path: route('welcome', {}, false),
            component: require('./../components/ClientArea/Welcome.vue'),
            meta: {
                title: '欢迎页',
            }
        },
        {
            name: 'login',
            path: route('login', {}, false),
            component: { template: '<login-form-container><login-form v-once></login-form></login-form-container>' },
            meta: {
                title: '登录',
            }
        },
        {
            name: 'register',
            path: route('register', {}, false),
            component: { template: '<login-form-container><register-form v-once></register-form></login-form-container>' },
            meta: {
                title: '注册',
            }
        },
        {
            name: 'dashboard',
            path: route('dashboard', {}, false),
            component: require('./../components/ClientArea/Dashboard'),
            meta: {
                title: '总览',
            }
        },
        {
            name: 'computeInstances.index',
            path: route('computeInstances.index', {}, false),
            component: require('./../components/ClientArea/ComputeInstance/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return '我的' + vueRouterInstance.app.$t('common.computeInstance');
                }
            }
        },
        {
            name: 'computeInstances.create',
            path: route('computeInstances.create', {}, false),
            component: require('./../components/ClientArea/ComputeInstance/Create'),
            meta: {
                title: function (vueRouterInstance) {
                    return '创建' + vueRouterInstance.app.$t('common.computeInstance');
                }
            }
        },
        {
            path: route('computeInstances.show', [''], false) + ":id",
            component: require('./../components/ClientArea/ComputeInstance/Show'),
            children: [
                {
                    name: 'computeInstances.dashboard',
                    path: '',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Dashboard'),
                    meta: {
                        title: '总览 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.statistics',
                    path: 'statistics',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Statistics'),
                    meta: {
                        title: '资源统计图 - 计算实例详情',
                    },
                },
                {
                    name: 'computeInstances.volumes',
                    path: 'volumes',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Volumes'),
                    meta: {
                        title: '卷 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.virtualMedias',
                    path: 'virtualMedias',
                    component: require('./../components/ClientArea/ComputeInstance/Show/VirtualMedia'),
                    meta: {
                        title: '虚拟介质 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.snapshots',
                    path: 'snapshots',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Snapshot'),
                    meta: {
                        title: '快照 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.network',
                    path: 'network',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Network'),
                    meta: {
                        title: '网络 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.settings',
                    path: 'settings',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Setting'),
                    meta: {
                        title: '设置 - 计算实例详情'
                    }
                },
                {
                    name: 'computeInstances.histories',
                    path: 'histories',
                    component: require('./../components/ClientArea/ComputeInstance/Show/History'),
                    meta: {
                        title: '操作历史 - 计算实例详情'
                    }
                },
            ]
        },
        {
            name: 'volumes.index',
            path: route('volumes.index', [], false),
            component: require('./../components/Volume/Index'),
            meta: {
                title: '卷'
            }
        },
        {
            name: 'publicIPv4s.index',
            path: route('publicIPv4Addresses.index', {}, false),
            component: require("./../components/IPAddress/IPv4Index"),
            meta: {
                title: '公网IPv4'
            }
        },
        {
            name: 'publicIPv6s.index',
            path: route('publicIPv6Addresses.index', {}, false),
            component: require("./../components/IPAddress/IPv6Index"),
            meta: {
                title: '公网IPv6'
            }
        },
        {
            name: 'billing.dashboard',
            path: route('billing.dashboard', {}, false),
            component: require('./../components/ClientArea/Billing/Dashboard'),
            meta: {
                title: '费用总览',
            },
        },
        {
            name: 'billing.addCredit',
            path: route('billing.addCredit', {}, false),
            component: require('./../components/ClientArea/Billing/AddCredit'),
            meta: {
                title: '充值',
            },
        },
        {
            name: 'billing.paymentTrades.index',
            path: route('billing.paymentTrades.index', {}, false),
            component: require('./../components/ClientArea/Billing/PaymentTradeIndex'),
            meta: {
                title: '充值历史',
            },
        },
        {
            name: 'paymentTrades.refunds.index',
            path: route('billing.paymentTrades.index', {}, false) + "/:id/refunds",
            component: require('./../components/ClientArea/Billing/PaymentTradeRefundIndex'),
            meta: {
                title: '退款',
            },
        },
        {
            name: 'billing.records.index',
            path: route('billing.records.index', {}, false),
            component: require('./../components/ClientArea/Billing/RecordIndex'),
            meta: {
                title: '收支明细',
            }
        },
        {
            path: '/paymentModules/:paymentModule/payReturn/:tradeNo?',
            component: require('./../components/ClientArea/Billing/PaymentReturn'),
            meta: {
                title: '付款返回页',
            }
        },
        {
            name: 'tickets.index',
            path: route('tickets.index', [], false),
            component: require('./../components/ClientArea/Ticket/Index'),
            meta: {
                title: '我的工单',
            }
        },
        {
            name: 'tickets.create',
            path: route('tickets.create', [], false),
            component: require('./../components/ClientArea/Ticket/Create'),
            meta: {
                title: '创建工单',
            }
        },
        {
            name: 'tickets.show',
            path: route('tickets.index', [], false) + "/:id",
            component: require('./../components/ClientArea/Ticket/Show'),
            meta: {
                title: '工单详情',
            }
        },
        {
            path: '/account',
            component: require('./../components/ClientArea/Account/Show'),
            children: [
                {
                    name: 'account.profile',
                    path: '',
                    component: require('./../components/ClientArea/Account/Profile'),
                    meta: {
                        title: '帐号信息',
                    }
                },
                {
                    name: 'account.password',
                    path: 'password',
                    component: require('./../components/ClientArea/Account/ChangePassword'),
                    meta: {
                        title: '更改帐号密码',
                    }
                },
            ],
        },
        {
            name: 'password.request',
            path: route('password.request', [], false),
            component: require('./../components/ClientArea/Account/PasswordResetRequest'),
            meta: {
                title: '重置密码',
            }
        },
        {
            name: 'password.reset',
            path: route('password.reset', [""], false) + ":token",
            component: require('./../components/ClientArea/Account/PasswordReset'),
            meta: {
                title: '重置密码',
            }
        },
    ]
};

import vueRouterCreator from "./../vue-router-creator";

var vueRouter = vueRouterCreator(vueRouterConfig);

export default vueRouter;