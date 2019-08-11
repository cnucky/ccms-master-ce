var Decimal = require('decimal.js-light');

export default {
    computed: {
        volumePricePerHour: function () {
            if (Number.isNaN(parseInt(this.volumeCapacity)))
                return new Decimal(0);
            var unitPrice = new Decimal(this.volumePricePerGiBPerHour);
            return unitPrice.mul(this.volumeCapacity);
        },
        volumePricePerMonth: function () {
            return this.volumePricePerHour.mul(24).mul(30);
        },
        volumePricePerHourString: function () {
            return this.volumePricePerHour.toString();
        },
        volumePricePerMonthString: function () {
            return this.volumePricePerMonth.toString();
        },
    }
}