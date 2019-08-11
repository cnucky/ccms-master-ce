var vueRouterConfig = {
    mode: 'history',
    routes: [
        {
            name: 'admin.welcome',
            path: route('admin.welcome', {}, false),
            component: require('./../components/ClientArea/Welcome.vue'),
            meta: {
                title: '欢迎页',
            }
        },
        {
            name: 'admin.login',
            path: route('admin.login', {}, false),
            component: { template: '<login-form-container><admin-login-form v-once></admin-login-form></login-form-container>' },
            meta: {
                title: '登录',
            }
        },
        {
            name: 'admin.dashboard',
            path: route('admin.dashboard', {}, false),
            component: require('./../components/AdminArea/Dashboard'),
            meta: {
                title: '总览',
            }
        },
        {
            name: 'regions.index',
            path: route('regions.index', {}, false),
            component: require('./../components/AdminArea/Region/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.region') + '管理';
                },
            }
        },
        {
            name: 'zones.index',
            path: route('zones.index', {}, false),
            component: require('./../components/AdminArea/Zone/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.zone') + '管理';
                }
            }
        },
        {
            path: route('zones.index', {}, false) + '/:id',
            component: require('./../components/AdminArea/Zone/Show'),
            children: [
                {
                    name: 'zones.dashboard',
                    path: '',
                    component: require('./../components/AdminArea/Zone/Show/Dashboard'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.zone') + ' - 总览';
                        }
                    }
                },
                {
                    name: 'zones.computeNodes',
                    path: 'computeNodes',
                    component: require('./../components/AdminArea/Zone/Show/NodeIndex'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.zone') + ' - 计算节点';
                        }
                    }
                },
                {
                    name: 'zones.packages.index',
                    path: 'packages',
                    component: require('./../components/AdminArea/Zone/Show/PackageIndex'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.zone') + ' - 计算实例规格';
                        }
                    }
                },
            ],
        },
        {
            name: 'computeNodes.index',
            path: route('computeNodes.index', {}, false),
            component: require('./../components/AdminArea/ComputeNode/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.computeNode') + '管理';
                }
            }
        },
        {
            // name: 'computeNodes.index',
            path: route('computeNodes.show', [''], false) + ":id",
            component: require('./../components/AdminArea/ComputeNode/Show'),
            children: [
                {
                    name: 'computeNodes.show',
                    path: '',
                    component: require('./../components/AdminArea/ComputeNode/Show/Dashboard'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - 详情';
                        }
                    }
                },
                {
                    name: 'computeNodes.statistics',
                    path: 'statistics',
                    component: require('./../components/AdminArea/ComputeNode/Show/Statistics'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - 资源统计图';
                        }
                    }
                },
                {
                    name: 'computeNodes.computeInstances',
                    path: 'computeInstances',
                    component: require('./../components/AdminArea/ComputeNode/Show/ComputeInstanceIndex'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - 计算实例';
                        }
                    }
                },
                {
                    name: 'computeNodes.localVolumes',
                    path: 'localVolumes',
                    component: require('./../components/AdminArea/ComputeNode/Show/LocalVolumeIndex'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - 本地卷';
                        }
                    }
                },
                {
                    name: 'computeNodes.showNOVNCBasicSetting',
                    path: 'noVNC',
                    component: require('./../components/AdminArea/ComputeNode/Show/NoVNC'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - noVNC设置';
                        }
                    }
                },
                {
                    name: 'computeNodes.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/ComputeNode/Show/Edit'),
                    meta: {
                        title: function (vueRouterInstance) {
                            return vueRouterInstance.app.$t('common.computeNode') + ' - 编辑';
                        }
                    }
                },
            ]
        },
        {
            name: 'images.index',
            path: route('images.index', {}, false),
            component: require('./../components/AdminArea/Image/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.image') + '管理';
                }
            }
        },
        {
            name: 'computeInstancePackages.index',
            path: route('computeInstancePackages.index', {}, false),
            component: require('./../components/AdminArea/ComputeInstancePackage/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.computeInstancePackage') + '管理'
                }
            }
        },
        {
            name: 'publicISOs.index',
            path: route('publicISOs.index', {}, false),
            component: require('./../components/AdminArea/PublicISO/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.publicISOImage') + '管理';
                }
            }
        },
        {
            name: 'publicFloppies.index',
            path: route('publicFloppies.index', {}, false),
            component: require('./../components/AdminArea/PublicFloppy/Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.publicFloppyImage') + '管理';
                }
            }
        },
        {
            name: 'ipv4Pools.index',
            path: route('ipv4Pools.index', {}, false),
            component: require('./../components/AdminArea/IPPool/IPv4Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.ipv4Pool') + '管理';
                }
            }
        },
        {
            name: 'ipv6Pools.index',
            path: route('ipv6Pools.index', {}, false),
            component: require('./../components/AdminArea/IPPool/IPv6Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.ipv6Pool') + '管理';
                }
            }
        },
        {
            name: 'ipv4.assignments.index',
            path: route('ipv4.assignments.index', {}, false),
            component: require('./../components/AdminArea/IPAssignment/IPv4Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.ipv4Pool') + '分配管理';
                }
            }
        },
        {
            name: 'ipv6.assignments.index',
            path: route('ipv6.assignments.index', {}, false),
            component: require('./../components/AdminArea/IPAssignment/IPv6Index'),
            meta: {
                title: function (vueRouterInstance) {
                    return vueRouterInstance.app.$t('common.ipv6Pool') + '分配管理';
                }
            }
        },
        {
            name: 'ipv4Pools.create',
            path: route('ipv4Pools.create', {}, false),
            component: require('./../components/AdminArea/IPPool/IPv4Create'),
            meta: {
                title: function (vueRouterInstance) {
                    return "创建" + vueRouterInstance.app.$t('common.ipv4Pool');
                }
            }
        },
        {
            name: 'ipv6Pools.create',
            path: route('ipv6Pools.create', {}, false),
            component: require('./../components/AdminArea/IPPool/IPv6Create'),
            meta: {
                title: function (vueRouterInstance) {
                    return "创建" + vueRouterInstance.app.$t('common.ipv6Pool');
                }
            }
        },
        {
            name: 'ipv4Pools.edit',
            path: route('ipv4Pools.show', [''], false) + ':id/edit',
            component: require('./../components/AdminArea/IPPool/IPv4Edit'),
            meta: {
                title: function (vueRouterInstance) {
                    return "编辑" + vueRouterInstance.app.$t('common.ipv4Pool');
                }
            }
        },
        {
            name: 'ipv6Pools.edit',
            path: route('ipv6Pools.show', [''], false) + ':id/edit',
            component: require('./../components/AdminArea/IPPool/IPv6Edit'),
            meta: {
                title: function (vueRouterInstance) {
                    return "编辑" + vueRouterInstance.app.$t('common.ipv6Pool');
                }
            }
        },
        {
            path: route('ipv4Pools.show', [''], false) + ':id',
            component: require('./../components/AdminArea/IPPool/IPv4Show'),
            children: [
                {
                    name: 'ipv4Pools.show',
                    path: '',
                    component: require('./../components/AdminArea/IPPool/Show/Dashboard'),
                    meta: {
                        title: 'IPv4地址池概览'
                    }
                },
                {
                    name: 'ipv4Pools.zoneAssignment',
                    path: 'zoneAssignments',
                    component: require('./../components/AdminArea/IPPool/Show/ZoneAssignment'),
                    meta: {
                        title: '可用区分配详情'
                    }
                },
                {
                    name: 'ipv4Pools.nodeAssignment',
                    path: 'nodeAssignments',
                    component: require('./../components/AdminArea/IPPool/Show/NodeAssignment'),
                    meta: {
                        title: '节点分配详情'
                    }
                },
                {
                    name: 'ipv4Pools.assignments.index',
                    path: 'assignments',
                    component: require('./../components/AdminArea/IPPool/Show/Assignment'),
                    meta: {
                        title: '分配详情',
                    }
                },
            ],
        },
        {
            path: route('ipv6Pools.show', [''], false) + ':id',
            component: require('./../components/AdminArea/IPPool/IPv6Show'),
            children: [
                {
                    name: 'ipv6Pools.show',
                    path: '',
                    component: require('./../components/AdminArea/IPPool/Show/Dashboard'),
                    meta: {
                        title: 'IPv6地址池概览'
                    }
                },
                {
                    name: 'ipv6Pools.zoneAssignment',
                    path: 'zoneAssignments',
                    component: require('./../components/AdminArea/IPPool/Show/ZoneAssignment'),
                    meta: {
                        title: '可用区分配详情'
                    }
                },
                {
                    name: 'ipv6Pools.nodeAssignment',
                    path: 'nodeAssignments',
                    component: require('./../components/AdminArea/IPPool/Show/NodeAssignment'),
                    meta: {
                        title: '节点分配详情'
                    }
                },
                {
                    name: 'ipv6Pools.assignments.index',
                    path: 'assignments',
                    component: require('./../components/AdminArea/IPPool/Show/Assignment'),
                    meta: {
                        title: '分配详情',
                    }
                },
            ],
        },
        {
            name: 'admin.computeInstances.index',
            path: route('admin.computeInstances.index', [], false),
            component: require('./../components/AdminArea/ComputeInstance/Index'),
            meta: {
                title: '计算实例列表',
            }
        },
        {
            path: route('admin.computeInstances.show', [''], false) + ":id",
            component: require('./../components/AdminArea/ComputeInstance/Show'),
            children: [
                {
                    name: 'admin.computeInstances.dashboard',
                    path: '',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Dashboard'),
                    meta: {
                        title: '总览 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.statistics',
                    path: 'statistics',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Statistics'),
                    meta: {
                        title: '资源统计图 - 计算实例详情',
                    },
                },
                {
                    name: 'admin.computeInstances.volumes',
                    path: 'volumes',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Volumes'),
                    meta: {
                        title: '卷 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.virtualMedias',
                    path: 'virtualMedias',
                    component: require('./../components/ClientArea/ComputeInstance/Show/VirtualMedia'),
                    meta: {
                        title: '虚拟介质 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.snapshots',
                    path: 'snapshots',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Snapshot'),
                    meta: {
                        title: '快照 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.network',
                    path: 'network',
                    component: require('./../components/AdminArea/ComputeInstance/Show/Network'),
                    meta: {
                        title: '网络 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.settings',
                    path: 'settings',
                    component: require('./../components/ClientArea/ComputeInstance/Show/Setting'),
                    meta: {
                        title: '设置 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.histories',
                    path: 'histories',
                    component: require('./../components/ClientArea/ComputeInstance/Show/History'),
                    meta: {
                        title: '操作历史 - 计算实例详情'
                    }
                },
                {
                    name: 'admin.computeInstances.administrator',
                    path: 'administrator',
                    component: require('./../components/AdminArea/ComputeInstance/Show/Administrator'),
                    meta: {
                        title: '管理选项 - 计算实例详情'
                    }
                },
            ]
        },
        {
            name: 'admin.volumes.index',
            path: route('admin.volumes.index', [], false),
            component: require('./../components/Volume/AdminIndex'),
            meta: {
                title: '卷'
            }
        },
        {
            name: 'currencies.index',
            path: route('currencies.index', {}, false),
            component: require('./../components/AdminArea/Currency/Index'),
            meta: {
                title: '货币列表',
            }
        },
        {
            name: 'currencies.create',
            path: route('currencies.create', {}, false),
            component: require('./../components/AdminArea/Currency/New'),
            meta: {
                title: '创建货币',
            }
        },
        {
            name: 'currencies.edit',
            path: route('currencies.index', {}, false) + "/:id/edit",
            component: require('./../components/AdminArea/Currency/Edit'),
            meta: {
                title: '编辑货币',
            }
        },
        {
            name: 'paymentModules.availableModules',
            path: route('paymentModules.availableModules', {}, false),
            component: require('./../components/AdminArea/PaymentModule/AvailableModule'),
            meta: {
                title: '可用模块列表',
            }
        },
        {
            name: 'paymentModules.index',
            path: route('paymentModules.index', {}, false),
            component: require('./../components/AdminArea/PaymentModule/Index'),
            meta: {
                title: '模块配置列表',
            }
        },
        {
            name: 'paymentModules.edit',
            path: route('paymentModules.index', {}, false) + '/:id/edit',
            component: require('./../components/AdminArea/PaymentModule/Edit'),
            meta: {
                title: '更改模块设置',
            }
        },
        {
            name: 'admin.paymentTrades.index',
            path: route('admin.paymentTrades.index', [], false),
            component: require('./../components/AdminArea/PaymentTrade/Index'),
            meta: {
                title: '充值订单',
            }
        },
        {
            name: 'admin.paymentTrades.show',
            path: route('admin.paymentTrades.show', [''], false) + ':id',
            component: require('./../components/AdminArea/PaymentTrade/Show'),
            meta: {
                title: '充值订单详情',
            }
        },
        {
            name: 'admin.paymentTradeRefunds.index',
            path: route('admin.paymentTradeRefunds.index', [], false),
            component: require('./../components/AdminArea/RefundTrade/Index'),
            meta: {
                title: '退款订单',
            }
        },
        {
            name: 'ticketDepartments.index',
            path: route('ticketDepartments.index', {}, false),
            component: require('./../components/AdminArea/Ticket/Department/Index'),
            meta: {
                title: '工单部门管理',
            }
        },
        {
            name: 'ticketDepartments.create',
            path: route('ticketDepartments.create', {}, false),
            component: require('./../components/AdminArea/Ticket/Department/Create'),
            meta: {
                title: '创建工单部门',
            }
        },
        {
            name: 'ticketDepartments.edit',
            path: route('ticketDepartments.index', {}, false) + "/:id/edit",
            component: require('./../components/AdminArea/Ticket/Department/Edit'),
            meta: {
                title: '编辑工单部门',
            }
        },
        {
            name: 'admin.tickets.index',
            path: route('admin.tickets.index', {}, false),
            component: require('./../components/AdminArea/Ticket/Index'),
            meta: {
                title: '工单列表',
            },
        },
        {
            name: 'admin.tickets.show',
            path: route('admin.tickets.index', {}, false) + "/:id",
            component: require('./../components/AdminArea/Ticket/Show'),
            meta: {
                title: '工单详情',
            },
        },
        {
            name: 'certificates.index',
            path: route('certificates.index', [], false),
            component: require('./../components/AdminArea/Certificate/Index'),
            meta: {
                title: '证书列表',
            }
        },
        {
            name: 'certificates.create',
            path: route('certificates.create', [], false),
            component: require('./../components/AdminArea/Certificate/Create'),
            meta: {
                title: '添加证书',
            }
        },
        {
            name: 'certificates.edit',
            path: route('certificates.index', [], false) + '/:id/edit',
            component: require('./../components/AdminArea/Certificate/Edit'),
            meta: {
                title: '编辑证书',
            }
        },
        {
            name: 'admin.roles.index',
            path: route('admin.roles.index', [], false),
            component: require('./../components/AdminArea/Role/Index'),
            meta: {
                title: '角色',
            },
        },
        {
            name: 'admin.roles.create',
            path: route('admin.roles.create', [], false),
            component: require('./../components/AdminArea/Role/Create'),
            meta: {
                title: '角色',
            },
        },
        {
            name: 'admin.roles.show',
            path: route('admin.roles.show', [''], false) + ':id',
            component: require('./../components/AdminArea/Role/Show'),
            children: [
                {
                    name: 'admin.roles.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/Role/Edit'),
                    meta: {
                        title: '编辑角色',
                    },
                },
                {
                    name: 'admin.roles.admins.index',
                    path: 'admins',
                    component: require('./../components/AdminArea/Role/AdminIndex'),
                    meta: {
                        title: '管理员',
                    },
                },
            ],
        },
        {
            name: 'admins.index',
            path: route('admins.index', [], false),
            component: require('./../components/AdminArea/Admin/Index'),
            meta: {
                title: '管理员',
            },
        },
        {
            name: 'admins.create',
            path: route('admins.create', [], false),
            component: require('./../components/AdminArea/Admin/Create'),
            meta: {
                title: '添加管理员',
            },
        },
        {
            name: 'admins.show',
            path: route('admins.show', [''], false) + ':id',
            component: require('./../components/AdminArea/Admin/Show'),
            children: [
                {
                    name: 'admins.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/Admin/Edit'),
                    meta: {
                        title: '编辑管理员',
                    },
                }
            ],
        },
        {
            name: 'userQuotas.index',
            path: route('userQuotas.index', [], false),
            component: require('./../components/AdminArea/UserQuota/Index'),
            meta: {
                title: '用户配额',
            }
        },
        {
            name: 'userQuotas.create',
            path: route('userQuotas.create', [], false),
            component: require('./../components/AdminArea/UserQuota/Create'),
            meta: {
                title: '添加用户配额',
            }
        },
        {
            name: 'userQuotas.show',
            path: route('userQuotas.show', [''], false) + ':id',
            component: require('./../components/AdminArea/UserQuota/Show'),
            meta: {
                title: '用户配额详情',
            },
            children: [
                {
                    name: 'userQuotas.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/UserQuota/Edit'),
                    meta: {
                        title: '编辑用户配额',
                    },
                },
                {
                    name: 'userQuotas.users.index',
                    path: 'users',
                    component: require('./../components/AdminArea/UserQuota/UserIndex'),
                    meta: {
                        title: '用户',
                    },
                },
            ],
        },
        {
            name: 'users.index',
            path: route('users.index', [], false),
            component: require('./../components/AdminArea/User/Index'),
            meta: {
                title: '用户',
            }
        },
        {
            path: route('users.show', [''], false) + ':id',
            component: require('./../components/AdminArea/User/Show'),
            children: [
                {
                    name: 'users.show',
                    path: '',
                    component: require('./../components/AdminArea/User/Show/Dashboard'),
                    meta: {
                        title: '用户详情',
                    },
                },
                {
                    name: 'users.credit',
                    path: 'credit',
                    component: require('./../components/AdminArea/User/Show/Credit'),
                    meta: {
                        title: '余额明细',
                    },
                },
                {
                    name: 'users.addCredit',
                    path: 'credit/add',
                    component: require('./../components/AdminArea/User/Show/AddCredit'),
                    meta: {
                        title: '添加余额',
                    },
                },
                {
                    name: 'users.removeCredit',
                    path: 'credit/add',
                    component: require('./../components/AdminArea/User/Show/RemoveCredit'),
                    meta: {
                        title: '扣除余额',
                    },
                },
                {
                    path: 'consumption',
                    component: require('./../components/AdminArea/User/Show/Consumption'),
                    children: [
                        {
                            name: 'users.consumption',
                            path: '',
                            component: require('./../components/AdminArea/User/Show/Consumption/Dashboard'),
                            meta: {
                                title: '历史消费概览',
                            },
                        },
                        {
                            name: 'users.dailyConsumption',
                            path: 'daily',
                            component: require('./../components/AdminArea/User/Show/Consumption/Daily'),
                            meta: {
                                title: '每日消费明细',
                            },
                        },
                        {
                            name: 'users.monthlyConsumption',
                            path: 'monthly',
                            component: require('./../components/AdminArea/User/Show/Consumption/Monthly'),
                            meta: {
                                title: '每月消费明细',
                            },
                        },
                    ],
                },
                {
                    name: 'users.computeInstances.index',
                    path: 'computeInstances',
                    component: require('./../components/AdminArea/User/Show/ComputeInstanceIndex'),
                    meta: {
                        title: '计算实例',
                    }
                },
                {
                    name: 'users.localVolumes.index',
                    path: 'localVolumes',
                    component: require('./../components/AdminArea/User/Show/LocalVolumeIndex'),
                    meta: {
                        title: '本地卷',
                    }
                },
                {
                    name: 'users.ipv4s.index',
                    path: 'ipv4s',
                    component: require('./../components/AdminArea/User/Show/IPv4AssignmentIndex'),
                    meta: {
                        title: 'IPv4',
                    }
                },
                {
                    name: 'users.ipv6s.index',
                    path: 'ipv6s',
                    component: require('./../components/AdminArea/User/Show/IPv6AssignmentIndex'),
                    meta: {
                        title: 'IPv6',
                    }
                },
                {
                    name: 'admin.tickets.indexByUser',
                    path: 'tickets',
                    component: require('./../components/AdminArea/User/Show/TicketIndex'),
                    meta: {
                        title: '工单',
                    }
                },
                {
                    name: 'users.paymentTrades.index',
                    path: 'paymentTrades',
                    component: require('./../components/AdminArea/User/Show/PaymentTradeIndex'),
                    meta: {
                        title: '充值订单',
                    }
                },
                {
                    name: 'users.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/User/Edit'),
                    meta: {
                        title: '编辑用户',
                    }
                },
            ],
        },
        {
            name: 'trafficShareGroups.index',
            path: route('trafficShareGroups.index', [], false),
            component: require('./../components/AdminArea/TrafficShareGroup/Index'),
            meta: {
                title: '流量共享组',
            },
        },
        {
            name: 'trafficShareGroups.create',
            path: route('trafficShareGroups.create', [], false),
            component: require('./../components/AdminArea/TrafficShareGroup/Create'),
            meta: {
                title: '创建流量共享组',
            },
        },
        {
            path: route('trafficShareGroups.show', [''], false) + ':id',
            component: require('./../components/AdminArea/TrafficShareGroup/Show'),
            meta: {
                title: '流量共享组详情',
            },
            children: [
                {
                    name: 'trafficShareGroups.show',
                    path: '',
                    component: require('./../components/AdminArea/TrafficShareGroup/Show/Redirect'),
                },
                {
                    name: 'trafficShareGroups.zones.index',
                    path: 'zones',
                    component: require('./../components/AdminArea/TrafficShareGroup/Show/Zone'),
                },
                {
                    name: 'trafficShareGroups.edit',
                    path: 'edit',
                    component: require('./../components/AdminArea/TrafficShareGroup/Edit'),
                },
            ],
        },
        {
            name: 'systemConfigurations.show',
            path: route('systemConfigurations.show', [], false),
            component: require('./../components/AdminArea/System/Setting'),
            meta: {
                title: '一般设置',
            }
        },
        {
            path: route('billing.statistics.dashboard', [], false),
            component: require('./../components/AdminArea/BillingStatistics/Show'),
            children: [
                {
                    name: 'billing.statistics.dashboard',
                    path: '',
                    component: require('./../components/AdminArea/BillingStatistics/Dashboard'),
                    meta: {
                        title: '财务概览',
                    },
                },
                {
                    path: 'daily',
                    component: require('../components/AdminArea/BillingStatistics/Daily'),
                    children: [
                        {
                            name: 'billing.statistics.daily.trade',
                            path: '',
                            component: require('./../components/AdminArea/BillingStatistics/Daily/Trade'),
                            meta: {
                                title: '充值退款',
                            }
                        },
                        {
                            name: 'billing.statistics.daily.charge',
                            path: 'charge',
                            component: require('./../components/AdminArea/BillingStatistics/Daily/Charge'),
                            meta: {
                                title: '收费',
                            }
                        },
                    ],
                },
                {
                    path: 'monthly',
                    component: require('../components/AdminArea/BillingStatistics/Monthly'),
                    children: [
                        {
                            name: 'billing.statistics.monthly.trade',
                            path: '',
                            component: require('./../components/AdminArea/BillingStatistics/Monthly/Trade'),
                            meta: {
                                title: '充值退款',
                            }
                        },
                        {
                            name: 'billing.statistics.monthly.charge',
                            path: 'charge',
                            component: require('./../components/AdminArea/BillingStatistics/Monthly/Charge'),
                            meta: {
                                title: '收费',
                            }
                        },
                    ],
                },
                {
                    path: 'history',
                    component: require('../components/AdminArea/BillingStatistics/History'),
                    children: [
                        {
                            name: 'billing.statistics.charge.history.byComputeInstancePackages',
                            path: '',
                            component: require('./../components/AdminArea/BillingStatistics/History/ComputeInstancePackage'),
                            meta: {
                                title: '计算实例收费历史',
                            },
                        },
                        {
                            name: 'billing.statistics.charge.history.byComputeNodes',
                            path: 'computeNodes',
                            component: require('./../components/AdminArea/BillingStatistics/History/ComputeNode'),
                            meta: {
                                title: '计算节点收费历史',
                            }
                        },
                        {
                            name: 'billing.statistics.charge.history.byZone',
                            path: 'zones',
                            component: require('./../components/AdminArea/BillingStatistics/History/Zone'),
                            meta: {
                                title: '可用区收费历史',
                            }
                        },
                        {
                            name: 'billing.statistics.charge.history.byIPv4Pools',
                            path: 'ipv4Pools',
                            component: require('./../components/AdminArea/BillingStatistics/History/IPv4Pool'),
                            meta: {
                                title: 'IPv4地址池收费历史',
                            }
                        },
                        {
                            name: 'billing.statistics.charge.history.byIPv6Pools',
                            path: 'ipv6Pools',
                            component: require('./../components/AdminArea/BillingStatistics/History/IPv6Pool'),
                            meta: {
                                title: 'IPv6地址池收费历史',
                            }
                        },
                        {
                            name: 'billing.statistics.charge.history.byTrafficShareGroups',
                            path: 'trafficShareGroups',
                            component: require('./../components/AdminArea/BillingStatistics/History/TrafficShareGroup'),
                            meta: {
                                title: '流量共享组收费历史',
                            }
                        },
                    ],
                }
            ],
        },
    ]
};

import vueRouterCreator from "./../vue-router-creator";

var vueRouter = vueRouterCreator(vueRouterConfig);

export default vueRouter;