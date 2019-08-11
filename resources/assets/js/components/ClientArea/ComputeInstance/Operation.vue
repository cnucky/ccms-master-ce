<template>
    <td>
        <semantic-ui-dropdown-menu text="操作" ref="dropdownMenu" :item-id="entry.id" v-bind:class="{disabled: entry.processing_operation_requests.length}">
            <router-link class="item" :to="{name: this.operationRoutePrefix + 'computeInstances.dashboard', params: {id: entry.id}}"><i class="edit icon"></i> 详情</router-link>
            <div class="divider"></div>
            <div class="item" v-bind:class="{disabled: entry.power_status === 1}" v-on:click="powerOnAction(entry)"><i class="play icon"></i> 启动</div>
            <div class="item" v-bind:class="{disabled: entry.power_status === 0}" v-on:click="powerResetAction(entry)"><i class="redo icon"></i> 重启</div>
            <div class="item" v-bind:class="{disabled: entry.power_status === 0}" v-on:click="powerOffAction(entry)"><i class="stop icon"></i> 关机</div>
            <div class="divider"></div>
            <a class="item" style="color: red;" v-on:click.prevent="destroy"><i class="delete icon"></i> 销毁</a>
        </semantic-ui-dropdown-menu>
    </td>
</template>

<script>
    import PowerAction from "./PowerAction";

    export default {
        name: "InstanceOperation",
        mixins: [PowerAction],
        props: ["entry", "isLoading"],
        data: function () {
            return {
                operationRoutePrefix: "",
            };
        },
        methods: {
            destroy: function () {
                this.$router.push({name: this.operationRoutePrefix + 'computeInstances.settings', params: {id: this.entry.id}}, () => {
                    location.href = "#destroy-instance";
                });
            },
        }
    }
</script>

<style scoped>

</style>