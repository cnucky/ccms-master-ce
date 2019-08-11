<template>
    <td>
        <semantic-ui-dropdown-menu text="操作" ref="dropdownMenu" :item-id="entry.id">
            <router-link class="item" :to="{name: 'computeNodes.show', params: {id: entry.id}}"><i class="list icon"></i> 详情</router-link>
            <a class="item" v-on:click.prevent="$emit('edit-item', entry)"><i class="edit icon"></i> 编辑</a>
            <a class="item" v-on:click.prevent="destroy(entry.id)"><i class="delete icon"></i> 删除</a>
        </semantic-ui-dropdown-menu>
    </td>
</template>

<script>
    export default {
        name: "Operation",
        props: ["entry"],
        methods: {
            destroy: function (id) {
                var targetDropdownMenu = this.$refs.dropdownMenu;
                this.confirmModal()
                    .withHeader("注意")
                    .withContent("确定删除"+ this.$t("common.computeNode") +"“"+ this.entry.name +"”？")
                    .withOnApprove(() => {
                        targetDropdownMenu.setLoading(true, "删除中");
                        axios
                            .delete(route('computeNodes.destroy', [id]))
                            .then((response) => {
                                this.positiveFloatingMessage(this.$t("common.computeNode") +"["+ this.entry.name +"]已删除");
                                this.$emit("remove-entry", this.entry);
                                // targetDropdownMenu.setDisabled();
                            }).catch((error) => {
                            console.log(error);
                        }).then(() => {
                            targetDropdownMenu.clearLoading();
                        })
                        ;
                    })
                    .show()
                ;
            },
        }
    }
</script>

<style scoped>

</style>