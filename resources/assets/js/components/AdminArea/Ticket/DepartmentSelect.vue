<template>
    <div ref="departmentSelect" class="ui mini selection dropdown" v-bind:class="{loading: isChangingDepartment}">
        <i class="dropdown icon"></i>
        <div class="text">{{ selectedDepartment ? selectedDepartment.name : '' }}</div>
        <div class="menu">
            <div v-for="department in departments" class="item" v-on:click="$emit('input', department)">{{ department.name }}</div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "DepartmentSelect",
        props: ["isChangingDepartment", "selectedDepartment", "departments"],
        mounted: function () {
            $(this.$refs.departmentSelect).dropdown({
                action: "hide",
            });
        },
    }
</script>

<style scoped>

</style>