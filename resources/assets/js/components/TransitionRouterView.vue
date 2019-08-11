<template>
    <div id="router-view">
        <transition name="slide-fade" mode="out-in" :appear="true">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "transition-router-view"
    }
</script>

<style scoped>
    #router-view {
        max-width: 1600px;
        width: 100%;
        height: 100%;
        /* overflow: auto; */
        min-width: 1200px;
        padding-top: 30px;
        padding-bottom: 80px;
        margin: 0 auto;
    }

    .slide-fade-enter-active {
        transition: all .15s ease;
    }

    .slide-fade-leave-active {
        transition: all .15s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }
</style>