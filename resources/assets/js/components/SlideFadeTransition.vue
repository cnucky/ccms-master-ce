<template>
    <transition name="slide-fade" :mode="mode">
        <slot></slot>
    </transition>
</template>

<script>
    export default {
        name: "slide-fade-transition",
        props: {
            mode: {
                type: String,
                default: "out-in",
            }
        }
    }
</script>

<style>
    .slide-fade-enter-active {
        transition: all .23s ease;
    }

    .slide-fade-leave-active {
        transition: all .23s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }
</style>