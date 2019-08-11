<template>
    <td>
        <semantic-ui-dropdown-menu ref="dropdownMenu" text="操作">
            <router-link :to="{name: 'certificates.edit', params: {id: entry.id}}" class="item"><i
                    class="edit icon"></i> 编辑
            </router-link>
            <div class="item" v-on:click="destroy"><i class="trash icon"></i> 删除</div>
        </semantic-ui-dropdown-menu>
    </td>
</template>

<script>
    export default {
        name: "Operation",
        props: ["entry"],
        methods: {
            destroy: function () {
                var id = this.entry.id;
                var targetDropdownMenu = this.$refs.dropdownMenu;
                this.confirmModal()
                    .withHeader("注意")
                    .withContent("确定删除证书[" + this.entry.fingerprint + "]？")
                    .withOnApprove(() => {
                        targetDropdownMenu.setLoading(true, "删除中");
                        axios
                            .delete(route('certificates.destroy', [id]))
                            .then((response) => {
                                if (response.data.result) {
                                    this.positiveFloatingMessage("已删除");
                                    this.$emit("remove-entry", this.entry);
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            }).catch(this.$axiosCatchError2Console).then(() => {
                            targetDropdownMenu.clearLoading();
                        })
                        ;
                    })
                    .show()
                ;
            }
        },
    }
</script>

<style scoped>

</style>