<template>
    <td>
        <template v-if="parentApp.pool.type === 0">
            <semantic-ui-dropdown-menu v-bind:class="{loading: isLoading}" text="操作">
                <div class="item" v-on:click="convert"><i class="sync icon"></i> 转为<template v-if="entry.unbindable">非</template>弹性</div>
                <div class="divider"></div>
                <div v-if="entry.nic_id" class="item" v-on:click="unbind"><i class="unlink icon"></i> 解绑</div>
                <div class="item" v-on:click="release" style="color: red;"><i class="trash icon"></i> 释放</div>
            </semantic-ui-dropdown-menu>
        </template>
        <template v-else>-</template>
    </td>
</template>

<script>
    export default {
        name: "OperationColumn",
        props: ["entry", "parentApp"],
        data: function () {
            return {
                isLoading: false,
            };
        },
        methods: {
            convert: function () {
                this.isLoading = true;
                axios.post(route("admin.publicIPv"+ this.ipType +"Addresses.convert", [this.entry.id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("转换成功");
                            this.$set(this.entry, "unbindable", data.unbindable);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            unbind: function () {
                this.ipOperation("unbind");
            },
            release: function () {
                this.ipOperation("release");
            },
            ipOperation: function (operation) {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定" + (operation === "unbind" ? "解绑" : "释放") + "指定IP？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.post(route("admin.publicIPv"+ this.ipType +"Addresses" + "." + operation, [this.entry.id]), null, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.parentApp.filter();
                                    this.positiveFloatingMessage("成功");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isLoading = false;
                            })
                        ;
                    })
                    .show()
                ;
            },
        },
        computed: {
            ipType: function () {
                if (this.parentApp.modelInternalName === "ipv4Pools")
                    return "4";
                return "6";
            }
        }
    }
</script>

<style scoped>

</style>