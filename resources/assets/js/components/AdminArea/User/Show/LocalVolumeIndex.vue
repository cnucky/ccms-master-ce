<script>
    import IndexTable from "./../../../Volume/AdminIndexTable"

    export default {
        extends: IndexTable,
        name: "LocalVolumeIndex",
        props: ["userData"],
        data: function () {
            return {
                indexRouteName: "users.localVolumes.index",
                operationRoutePrefix: "admin.",
            };
        },
        methods: {
            additionalFilterArguments: function (filterArguments) {
                filterArguments.user = this.userData.user.id;
                return filterArguments;
            },
        }
    }
</script>