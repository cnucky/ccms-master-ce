<script>
    import ChargeStatistics from "../Statistics/ChargeStatistics";
    export default {
        name: "Charge",
        extends: ChargeStatistics,
        data: function () {
            return {
                useMonthInput: true,
            };
        }
    }
</script>