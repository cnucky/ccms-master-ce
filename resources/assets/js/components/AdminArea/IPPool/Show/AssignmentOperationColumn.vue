<template>
    <delete-button v-on:buttonClick="deleteAssignment" :is-deleting="isDeleting"></delete-button>
</template>

<script>
    import DeleteButton from "../Column/DeleteButton";
    export default {
        name: "AssignmentOperationColumn",
        components: {DeleteButton},
        props: ["parentApp", "entry"],
        data: function () {
            return {
                isDeleting: false,
            };
        },
        methods: {
            deleteAssignment: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定删除此项？")
                    .withOnApprove(() => {
                        this.isDeleting = true;
                        axios.delete(route(this.parentApp.modelInternalName + "."+ this.parentApp.assignmentName +".delete", [this.parentApp.pool.id, this.entry.id]), {vueInstance: this})
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