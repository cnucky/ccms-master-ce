<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">创建工单部门</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <department-form
                        v-on:submit="store"
                        :is-submitting="isSubmitting"
                        :name="department.name"
                        :order="department.order"
                        :status="department.status"
                        :show-relative-service-select="department.showRelativeServiceSelect"
                        :description="department.description"
                        v-on:name="(value) => { department.name = value }"
                        v-on:order="(value) => { department.order = value }"
                        v-on:status="(value) => { department.status = value }"
                        v-on:showRelativeServiceSelect="(value) => { department.showRelativeServiceSelect = value }"
                        v-on:description="(value) => { department.description = value }"
                ></department-form>
            </div>
        </div>
    </div>
</template>

<script>
    import DepartmentForm from "./DepartmentForm";
    export default {
        name: "Create",
        components: {DepartmentForm},
        data: function () {
            return {
                isSubmitting: false,
                department: {
                    name: "",
                    order: "0",
                    status: "1",
                    showRelativeServiceSelect: "0",
                    description: "",
                },
            };
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                axios.post(route("ticketDepartments.store"), this.department, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$router.push({name: "ticketDepartments.edit", params: {id: data.department.id}});
                            this.positiveFloatingMessage("工单部门创建成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>