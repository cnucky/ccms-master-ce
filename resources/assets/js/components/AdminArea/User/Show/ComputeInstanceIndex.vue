<script>
    import Index from "./../../ComputeInstance/IndexTable";

    export default {
        extends: Index,
        name: "ComputeInstanceIndex",
        props: ["userData"],
        data: function () {
            return {
                indexRouteName: "users.computeInstances.index",
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