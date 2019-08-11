<script>
    import CreditOperation from "./CreditOperation";
    export default {
        name: "RemoveCredit",
        extends: CreditOperation,
        data: function () {
            return {
                operationName: "扣除",
                routeName: "users.removeCredit",
            };
        }
    }
</script>
