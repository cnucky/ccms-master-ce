<template>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top navbar-transparent-white mm-fixed-top collapsed" role="navigation"
         id="navbar">


        <!-- Branding -->
        <div class="navbar-header col-md-2">
            <div class="navbar-brand">
                <strong>CLOUD</strong>&nbsp;COMPUTING
            </div>
            <div class="sidebar-collapse">
                <a href="#">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
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
        name: "NavigationContainer",
    }
</script>

<style scoped>
    @import "./../../sass/navigation.css";
</style>