<template>
    <div ref="instancePowerToggle" class="ui fitted toggle checkbox power-toggle" v-bind:class="{ checked: instance.power_status }" style="float: right;">
        <semantic-ui-loader :loader-class="'small'" v-bind:is-active="processing"></semantic-ui-loader>
        <input type="checkbox" class="hidden" v-on:click.prevent="() => {}" :checked="instance.power_status">
        <label v-on:click="$emit('click')"></label>
    </div>

</template>

<script>
    export default {
        name: "PowerToggle",
        props: ["instance", "processing"],
    }
</script>

<style scoped>
    .ui.toggle.checkbox.power-toggle {
        height: 1.5rem;
        margin-left: 16px;
        width: 72px;
    }

    .ui.toggle.checkbox label:before {
        width: auto;
    }

    .ui.toggle.checkbox.power-toggle >>> input:checked~label:before {
        content: 'ON';
        color: #fff;
        position: absolute;
        left: 14px;
        top: 6px;
        opacity: 1;
    }

    .ui.toggle.checkbox.power-toggle >>> input:not(:checked)~label:before {
        color: #999;
        position: absolute;
        content: "OFF";
        left: 2.375rem;
        top: 6px;
    }

    .ui.toggle.checkbox.power-toggle >>> label {
        height: 32px;
        min-width: 72px;
        background-color: rgba(0,0,0,.15);
        border-radius: 500rem;
    }

    .ui.toggle.checkbox.power-toggle >>> label:before {
        background: none !important;
    }

    .ui.toggle.checkbox.power-toggle >>> input:checked~label {
        background-color: #15CD72 !important;
    }

    .ui.toggle.checkbox.power-toggle >>> label:after {
        height: 32px;
        width: 32px;
    }

    .ui.toggle.checkbox.power-toggle >>> input:checked~label:after {
        left: 41px;
    }

    .ui.fitted.toggle >>> .ui.dimmer {
        border-radius: 500rem;
        height: 32px;
        margin-left: -1px;
        width: 74px;
    }

    .ui.fitted.toggle >>> .ui.loader {
        margin-top: 5px;
    }
</style>