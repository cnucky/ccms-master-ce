<template>
    <transition-group
            name="staggered-fade"
            :tag="tag"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
    >
        <slot></slot>
    </transition-group>
</template>

<script>
    export default {
        name: "TransitionList",
        props: ["tag"],
        methods: {
            beforeEnter: function (el) {
                el.style.opacity = 0;
                el.style.height = 0
            },
            enter: function (el, done) {
                var delay = el.dataset.index * 150;
                setTimeout(function () {
                    window.Velocity(
                        el,
                        { opacity: 1, height: '1.6em' },
                        { complete: done }
                    )
                }, delay)
            },
            leave: function (el, done) {
                var delay = el.dataset.index * 150;
                setTimeout(function () {
                    window.Velocity(
                        el,
                        { opacity: 0, height: 0 },
                        { complete: done }
                    )
                }, delay)
            }
        }
    }
</script>

<style>
    .staggered-fade-move {
        transition: transform .5s;
    }
</style>