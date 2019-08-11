<script>
    import Trade from "../Statistics/Trade";
    export default {
        name: "Trade",
        extends: Trade,
        data: function () {
            return {
                useMonthInput: true,
                routeName: "billing.statistics.monthly.trade",
            };
        },
    }
</script>