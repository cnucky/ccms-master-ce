<template>
    <div class="ui modal">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "QRCodeModal",
        data: function () {
            return {
                stopQuery: false,
            };
        },
        props: {
            tradeId: {
                default: null,
            },
        },
        created: function () {
            this.queryTradeStatus();
        },
        mounted: function () {
            $(this.$el)
                .modal({
                    onHidden: () => {
                        this.stopQuery = true;
                    }
                })
                .modal("show")
            ;
        },
        destroyed: function () {
            this.stopQuery = true;
        },
        methods: {
            queryTradeStatus: function () {
                if (!this.tradeId || this.stopQuery)
                    return;
                this.$axiosPost(route("billing.paymentTrades.status", [this.tradeId]), null, (data) => {
                    if (data.result && data.status !== 0) {
                        this.stopQuery = true;
                        this.$emit('tradeStatusChanged', data);
                    }
                }, () => {
                    setTimeout(this.queryTradeStatus, 3000);
                });
            },
            hide: function () {
                $(this.$el).modal("hide");
            }
        }
    }
</script>

<style scoped>

</style>