<template>
    <td>
        <semantic-ui-dropdown-menu v-bind:class="{loading: isSubmitting}" text="操作">
            <router-link class="item" :to="{name: 'admins.edit', params: {id: entry.id}}"><i class="edit icon"></i> 详情</router-link>
            <a v-if="$store.getters.currentUser.id !== entry.id" class="item" href="#" v-on:click.prevent="destroy"><i class="trash icon"></i> 删除</a>
        </semantic-ui-dropdown-menu>
    </td>
</template>

<script>
    export default {
        name: "OperationColumn",
        props: ["entry"],
        data: function () {
            return {
                isSubmitting: false,
            };
        },
        methods: {
            destroy: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定删除管理员["+ this.entry.name +"]？")
                    .withOnApprove(() => {
                        this.isSubmitting = true;
                        this.$axiosDelete(route("admins.destroy", [this.entry.id]), (data) => {
                            this.$emit("remove-entry", this.entry);
                            this.positiveFloatingMessage("删除成功");
                        }, () => {
                            this.isSubmitting = false;
                        });
                    })
                    .show()
                ;
            }
        }
    }
</script>

<style scoped>

</style>