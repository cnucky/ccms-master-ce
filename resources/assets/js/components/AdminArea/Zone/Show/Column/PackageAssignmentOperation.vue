<template>
    <td>
        <button class="ui tiny red button" v-on:click="deleteAssignment">删除</button>
    </td>
</template>

<script>
    export default {
        name: "PackageAssignmentOperation",
        props: ["entry"],
        methods: {
            deleteAssignment: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定删除此项？")
                    .withOnApprove(() => {
                        this.isDeleting = true;
                        axios.delete(route("zones.packages.delete", [this.entry.pivot.id]), {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.$emit("remove-entry", this.entry);
                                    this.positiveFloatingMessage("删除成功")
                                } else {
                                    this.negativeFloatingMessage(data.message);
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            })
                            .then(() => {
                                this.isDeleting = false;
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