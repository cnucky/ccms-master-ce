<template>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top navbar-transparent-white mm-fixed-top"
         role="navigation"
         id="navbar">
        <!-- Branding -->
        <div class="navbar-header col-md-2">
            <router-link class="navbar-brand" :to="{ name: brandRouteName ? brandRouteName : 'welcome' }">
                <strong>CLOUD</strong>&nbsp;COMPUTING
            </router-link>
        </div>
        <!-- Branding end -->

        <!-- .nav-collapse -->
        <div class="navbar-collapse">
            <slot></slot>
        </div>
        <!--/.nav-collapse -->
    </div>
    <!-- Fixed navbar end -->
</template>

<script>
    export default {
        name: "GuestNavigation",
        props: ['brandRouteName'],
    }
</script>

<style scoped>
    @import "./../../sass/navigation.css";

    .navbar-header {
        background: white !important;
    }

    .navbar-header > a {
        background: none !important;
        color: #324558 !important;
    }

    .navbar-collapse {
        background-color: white;
    }

    .nav.navbar-nav.quick-actions {
        line-height: 50px;
        float: right;
        margin-right: 30px;
    }
</style>