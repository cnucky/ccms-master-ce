<script>
    import PaymentTradeIndexTable from "../../PaymentTrade/IndexTable";
    export default {
        extends: PaymentTradeIndexTable,
        name: "PaymentTradeIndex",
        props: ["userData"],
        data: function () {
            return {
                indexRouteName: "users.paymentTrades.index",
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
