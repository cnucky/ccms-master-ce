<template>
    <td>
        <semantic-ui-dropdown-menu text="操作" ref="dropdownMenu" :item-id="entry.id">
            <router-link class="item" :to="{name: 'zones.dashboard', params: {id: entry.id}}"><i class="file alternate icon"></i>详情</router-link>
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
                    .withContent("确定删除"+ this.$t("common.zone") +"“"+ this.entry.name +"”？")
                    .withOnApprove(() => {
                        targetDropdownMenu.setLoading(true, "删除中");
                        this.$axiosDelete(route('zones.destroy', [id]), (data) => {
                            this.positiveFloatingMessage(this.$t("common.zone") +"["+ this.entry.name +"]已删除");
                            this.$emit("remove-entry", this.entry);
                            // targetDropdownMenu.setDisabled();
                        }, () => {
                            targetDropdownMenu.clearLoading();
                        });
                    })
                    .show()
                ;
            },
        }
    }
</script>

<style scoped>

</style>