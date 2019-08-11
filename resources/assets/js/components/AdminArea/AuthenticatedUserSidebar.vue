<template>
    <!-- Sidebar -->
    <ul class="nav navbar-nav side-nav" id="sidebar">
        <li class="navigation" id="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="#navigation">Navigation <i
                    class="fa fa-angle-up"></i></a>

            <ul class="menu">

                <router-link active-class="active" tag="li" :to="{ name: 'admin.dashboard' }">
                    <a>
                        <i class="fa fa-tachometer"></i> 总览
                    </a>
                </router-link>

                <router-link v-if="$hasAnyPermissionTo('AMIDN_PERM_R_CERTIFICATE')" active-class="active" tag="li" :to="{ name: 'certificates.index' }">
                    <a>
                        <i class="key icon"></i> {{ $t('common.certificate') }}
                    </a>
                </router-link>

                <li v-if="$hasAnyPermissionTo(['ADMIN_PERM_R_REGION', 'ADMIN_PERM_R_TRAFFIC_SHARE_GROUP', 'ADMIN_PERM_R_ZONE'])" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-globe"></i> 区域<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_REGION')" active-class="active" tag="li" :to="{ name: 'regions.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.region') }}
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_TRAFFIC_SHARE_GROUP')" active-class="active" tag="li" :to="{ name: 'trafficShareGroups.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.trafficShareGroups') }}
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_ZONE')" active-class="active" tag="li" :to="{ name: 'zones.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.zone') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <li v-if="$hasAnyPermissionTo('ADMIN_PERM_R_COMPUTE_NODE')" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-list"></i> 节点<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{ name: 'computeNodes.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.computeNode') }}
                            </a>
                        </router-link>
                        <li>
                            <a>
                                <i class="fa fa-caret-right"></i> 镜像节点
                            </a>
                        </li>
                    </ul>
                </li>

                <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_PUBLIC_IMAGE')" active-class="active" tag="li" :to="{ name: 'images.index' }">
                    <a>
                        <i class="copy icon"></i> {{ $t('common.image') }}
                    </a>
                </router-link>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-floppy-o"></i> {{ $t('common.virtualMedia') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{ name: 'publicISOs.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.publicISOImage') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{ name: 'publicFloppies.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.publicFloppyImage') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_COMPUTE_INSTANCE_PACKAGE')" active-class="active" tag="li" :to="{ name: 'computeInstancePackages.index' }">
                    <a>
                        <i class="cubes icon"></i> {{ $t('common.computeInstancePackage') }}
                    </a>
                </router-link>

                <li v-if="$hasAnyPermissionTo(['ADMIN_PERM_R_IP_POOL', 'ADMIN_PERM_R_IP_ASSIGNMENT'])" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-globe"></i> {{ $t('common.ipPool') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_IP_POOL')" active-class="active" tag="li" :to="{ name: 'ipv4Pools.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv4') }}
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_IP_ASSIGNMENT')" active-class="active" tag="li" :to="{ name: 'ipv4.assignments.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv4') }}分配
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_IP_POOL')" active-class="active" tag="li" :to="{ name: 'ipv6Pools.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv6') }}
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_IP_ASSIGNMENT')" active-class="active" tag="li" :to="{ name: 'ipv6.assignments.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv6') }}分配
                            </a>
                        </router-link>
                    </ul>
                </li>

                <li v-if="$hasAnyPermissionTo(['ADMIN_R_USER_QUOTA', 'ADMIN_R_USER_QUOTA'])" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="user icon"></i> {{ $t('common.user') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_R_USER_QUOTA')" active-class="active" tag="li" :to="{ name: 'userQuotas.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.quota') }}
                            </a>
                        </router-link>
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_R_USER_QUOTA')" active-class="active" tag="li" :to="{ name: 'users.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.user') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_COMPUTE_INSTANCE')" active-class="active" tag="li" :to="{ name: 'admin.computeInstances.index' }">
                    <a>
                        <i class="server icon"></i> {{ $t('common.computeInstance') }}
                    </a>
                </router-link>

                <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_LOCAL_VOLUME')" active-class="active" tag="li" :to="{ name: 'admin.volumes.index' }">
                    <a>
                        <i class="hdd icon"></i> {{ $t('common.volume') }}
                    </a>
                </router-link>

                <li v-if="$hasAnyPermissionTo(['ADMIN_PERM_R_CURRENCY', 'ADMIN_PERM_CRUD_PAYMENT_MODULE_CONFIGURATION'])" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="money bill alternate icon"></i> {{ $t('common.billingManagement') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_BULLING_STATISTICS')" active-class="active" tag="li" :to="{name: 'billing.statistics.dashboard'}">
                            <a>
                                <i class="fa fa-caret-right"></i> 统计
                            </a>
                        </router-link>

                        <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_CURRENCY')" active-class="active" tag="li" :to="{name: 'currencies.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.currency') }}
                            </a>
                        </router-link>
                        <template v-if="$hasAnyPermissionTo('ADMIN_PERM_CRUD_PAYMENT_MODULE_CONFIGURATION')">
                            <router-link active-class="active" tag="li" :to="{name: 'paymentModules.availableModules'}">
                                <a>
                                    <i class="fa fa-caret-right"></i> {{ $t('common.availablePaymentModule') }}
                                </a>
                            </router-link>
                            <router-link active-class="active" tag="li" :to="{name: 'paymentModules.index'}">
                                <a>
                                    <i class="fa fa-caret-right"></i> {{ $t('common.paymentModuleSettingList') }}
                                </a>
                            </router-link>
                        </template>
                        <template v-if="$hasAnyPermissionTo('ADMIN_PERM_R_PAYMENT_TRADE')">
                            <router-link active-class="active" tag="li" :to="{name: 'admin.paymentTrades.index'}">
                                <a>
                                    <i class="fa fa-caret-right"></i> {{ $t('common.paymentTrade') }}
                                </a>
                            </router-link>
                            <router-link active-class="active" tag="li" :to="{name: 'admin.paymentTradeRefunds.index'}">
                                <a>
                                    <i class="fa fa-caret-right"></i> 退款订单
                                </a>
                            </router-link>
                        </template>
                    </ul>
                </li>

                <router-link v-if="$hasAnyPermissionTo('ADMIN_PERM_R_TICKET')" active-class="active" tag="li" :to="{name: 'admin.tickets.index'}">
                    <a>
                        <i class="sticky note icon"></i> {{ $t('common.ticket') }}
                    </a>
                </router-link>

                <li v-if="$isSuperAdministrator()" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="cogs icon"></i> {{ $t('common.systemSetting') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{name: 'systemConfigurations.show'}">
                            <a>
                                <i class="fa fa-caret-right"></i> 一般设置
                            </a>
                        </router-link>

                        <router-link active-class="active" tag="li" :to="{name: 'ticketDepartments.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ticketDepartment') }}
                            </a>
                        </router-link>

                        <router-link active-class="active" tag="li" :to="{name: 'admin.roles.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.role') }}
                            </a>
                        </router-link>


                        <router-link active-class="active" tag="li" :to="{name: 'admins.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.admin') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

            </ul>
        </li>

    </ul>
    <!-- Sidebar end -->
</template>

<script>
    export default {
        name: "AuthenticatedUserSidebar"
    }
</script>

<style scoped>

</style>