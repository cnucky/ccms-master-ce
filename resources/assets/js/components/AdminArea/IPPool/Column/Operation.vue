<template>
    <td>
        <dropdown-menu
                text="操作"
                ref="dropdownMenu"
        >
            <router-link class="item" :to="{name: parentApp.modelInternalName + '.show', params: {id: entry.id}}"><i class="file alternate outline icon"></i> 详情</router-link>
            <router-link class="item" :to="{name: parentApp.modelInternalName + '.edit', params: {id: entry.id}}"><i class="edit icon"></i> 编辑</router-link>
            <a class="item" v-on:click.prevent="destroy"><i class="delete icon"></i> 删除</a>
        </dropdown-menu>
    </td>
</template>

<script>
    import DropdownMenu from "../../../SemanticUI/DropdownMenu";
    export default {
        name: "Operation",
        components: {DropdownMenu},
        props: ["parentApp", "entry"],
        methods: {
            destroy: function () {
                var targetDropdownMenu = this.$refs.dropdownMenu;
                this.confirmModal()
                    .withHeader("注意")
                    .withContent("确定删除？")
                    .withOnApprove(() => {
                        targetDropdownMenu.setLoading(true, "删除中");
                        axios.delete(route(this.parentApp.modelInternalName + ".destroy", [this.entry.id]), {vueInstance: this})
                            .then((response) => {
                                if (response.data.result) {
                                    this.positiveFloatingMessage("已删除");
                                    this.$emit("remove-entry", this.entry);
                                } else {
                                    this.negativeFloatingMessage(response.data.message);
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            })
                            .then(() => {
                                targetDropdownMenu.clearLoading();
                            })
                        ;
                    })
                    .show()
                ;
            }
        }
    }
</script>

<style scoped>

</style>