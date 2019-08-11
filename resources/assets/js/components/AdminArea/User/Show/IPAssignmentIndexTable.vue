<script>
    import Assignment from "./../../IPAssignment/IndexTable";
    export default {
        extends: Assignment,
        name: "IPAssignmentIndexTable",
        props: ["userData"],
        data: function () {
            return {
                indexRouteName: "users." + this.modelInternalName + "s.index",
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
