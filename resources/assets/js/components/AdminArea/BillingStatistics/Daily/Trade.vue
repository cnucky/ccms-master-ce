<script>
    import Trade from "../Statistics/Trade";
    export default {
        name: "Trade",
        extends: Trade,
        data: function () {
            return {
                routeName: "billing.statistics.daily.trade",
            };
        },
    }
</script>