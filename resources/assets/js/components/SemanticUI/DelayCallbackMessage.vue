<template>
    <semantic-ui-basic-message v-on:mouseenter="mouseenter" v-on:mouseleave="startCountdownTimer" v-bind="$props" v-on:close="close"></semantic-ui-basic-message>
</template>

<script>
    export default {
        name: "DelayCallbackMessage",
        props: ["delay", "id", "type", "messages", "header", "closable"],
        data: function () {
            return {
                timeoutHandler: null,
                mouseEntered: false,
            }
        },
        mounted: function () {
            this.startCountdownTimer();
        },
        methods: {
            startCountdownTimer: function () {
                this.timeoutHandler = setTimeout(() => {
                    this.timeoutHandler = null;
                    if (this.mouseEntered)
                        return this.startCountdownTimer();
                    this.$emit('timeout', this.id);
                }, this.delay);
            },
            close: function () {
                if (this.timeoutHandler) {
                    clearTimeout(this.timeoutHandler);
                }
                this.timeoutHandler = null;
                this.$emit('timeout', this.id);
            },
            mouseenter: function () {
                clearTimeout(this.timeoutHandler);
                this.timeoutHandler = null;
            }
        }
    }
</script>

<style scoped>

</style>