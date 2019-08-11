<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">编辑工单部门</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: !isLoaded}">
                <department-form
                        v-if="isLoaded"
                        v-on:submit="update"
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
        name: "Edit",
        components: {DepartmentForm},
        data: function () {
            return {
                isLoaded: false,
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
        created: function () {
            axios.get(route("ticketDepartments.edit", [this.departmentId]), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.department = data.department;
                        this.department.showRelativeServiceSelect = data.department.show_relative_service_select;
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.isLoaded = true;
                })
            ;
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                axios.patch(route("ticketDepartments.update", [this.departmentId]), this.department, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("工单部门属性更新成功");
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
        },
        computed: {
            departmentId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>