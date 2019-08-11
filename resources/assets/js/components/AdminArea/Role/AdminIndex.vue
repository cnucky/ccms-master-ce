<script>
    import IndexTable from "../Admin/IndexTable";
    export default {
        extends: IndexTable,
        name: "AdminIndex",
        props: ["roleData"],
        data: function () {
            return {
                indexRouteName: "admin.roles.admins.index",
            };
        },
        methods: {
            additionalFilterArguments: function (filterArguments) {
                filterArguments.role = this.roleData.role.id;
                return filterArguments;
            },
        }
    }
</script>