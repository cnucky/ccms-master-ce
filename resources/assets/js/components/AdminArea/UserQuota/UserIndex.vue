<script>
    import IndexTable from "../User/IndexTable";
    export default {
        extends: IndexTable,
        name: "UserIndex",
        props: ["userQuotaData"],
        data: function () {
            return {
                indexRouteName: "userQuotas.users.index",
            };
        },
        methods: {
            additionalFilterArguments: function (filterArguments) {
                filterArguments.userQuota = this.userQuotaData.userQuota.id;
                return filterArguments;
            },
        },
    }
</script>