<script>
    import CreditOperation from "./CreditOperation";
    export default {
        name: "AddCredit",
        extends: CreditOperation,
        data: function () {
            return {
                operationName: "添加",
                routeName: "users.addCredit",
            };
        }
    }
</script>
