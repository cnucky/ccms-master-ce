<template>
    <!-- Sidebar -->
    <ul class="nav navbar-nav side-nav" id="sidebar">
        <li class="navigation" id="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="#navigation">Navigation <i
                    class="fa fa-angle-up"></i></a>

            <ul class="menu">

                <router-link active-class="active" tag="li" :to="{ name: 'dashboard' }">
                    <router-link :to="{ name: 'dashboard' }">
                        <i class="fa fa-tachometer"></i> 总览
                    </router-link>
                </router-link>

                <router-link active-class="active" tag="li" :to="{ name: 'computeInstances.index' }">
                    <a>
                        <i class="server icon"></i> {{ $t('common.computeInstance') }}
                    </a>
                </router-link>

                <router-link active-class="active" tag="li" :to="{ name: 'volumes.index' }">
                    <a>
                        <i class="hdd icon"></i> {{ $t('common.volume') }}
                    </a>
                </router-link>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-globe"></i> {{ $t('common.publicIPAddress') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{ name: 'publicIPv4s.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv4') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{ name: 'publicIPv6s.index' }">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.ipv6') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="credit card icon"></i> {{ $t('common.billingCenter') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{name: 'billing.dashboard'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.billingDashboard') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{name: 'billing.addCredit'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.addCredit') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{name: 'billing.paymentTrades.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.addCreditHistory') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{name: 'billing.records.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.billingRecord') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="sticky note icon"></i> {{ $t('common.ticket') }}<b class="fa fa-plus dropdown-plus"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <router-link active-class="active" tag="li" :to="{name: 'tickets.index'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.myTickets') }}
                            </a>
                        </router-link>
                        <router-link active-class="active" tag="li" :to="{name: 'tickets.create'}">
                            <a>
                                <i class="fa fa-caret-right"></i> {{ $t('common.createTicket') }}
                            </a>
                        </router-link>
                    </ul>
                </li>

                <router-link active-class="active" tag="li" :to="{ name: 'account.profile' }">
                    <a>
                        <i class="user icon"></i> {{ $t('common.account') }}
                    </a>
                </router-link>
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