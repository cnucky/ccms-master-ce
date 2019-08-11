<template>
    <transition-group :tag="tag" name="slide-fade" mode="out-in">
        <slot></slot>
    </transition-group>
</template>

<script>
    export default {
        name: "slide-fade-transition-group",
        props: ["tag"],
    }
</script>

<style>
    .slide-fade-enter-active {
        transition: all .3s ease;
    }

    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(50px);
        opacity: 0;
    }

    .slide-fade-move {
        transition: transform .5s;
    }
</style>