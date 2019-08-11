<template>
    <div ref="statusSelect" class="ui mini selection dropdown" v-bind:class="{loading: isChangingStatus}">
        <i class="dropdown icon"></i>
        <div class="text">{{ $te('status.ticket.' + value) ? $t('status.ticket.' + value) : '' }}</div>
        <div class="menu">
            <div class="item" v-on:click="$emit('input', 0)" style="color: green;">{{ $t('status.ticket.0') }}</div>
            <div class="item" v-on:click="$emit('input', 1)" style="color: black;">{{ $t('status.ticket.1') }}</div>
            <div class="item" v-on:click="$emit('input', 2)" style="color: orange;">{{ $t('status.ticket.2') }}</div>
            <div class="item" v-on:click="$emit('input', 3)" style="color: blue;">{{ $t('status.ticket.3') }}</div>
            <div class="item" v-on:click="$emit('input', 5)" style="color: teal;">{{ $t('status.ticket.5') }}</div>
            <div class="item" v-on:click="$emit('input', 6)" style="color: grey;">{{ $t('status.ticket.6') }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StatusSelect",
        props: ["isChangingStatus", "value"],
        mounted: function () {
            $(this.$refs.statusSelect).dropdown({
                action: "hide",
            });
        },
        watch: {
            value: function (newValue, oldValue) {
                // $(this.$refs.statusSelect).dropdown("set text", this.$t('status.ticket.' + newValue));
            }
        }
    }
</script>

<style scoped>

</style>