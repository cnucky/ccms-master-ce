export default {
    methods: {
        $convert2HumanReadableUnit: function (value, unitCode = 0, maxUnit = 4) {
            value = parseFloat(value);
            while (value > 1024 && unitCode < maxUnit) {
                ++unitCode;
                value /= 1024;
            }

            return {
                value: value,
                unit: unitCode,
            };
        },
        $unitCode2UnitChar: function (unitCode) {
            switch (unitCode) {
                case 0:
                    return "B";
                case 1:
                    return "KB";
                case 2:
                    return "MB";
                case 3:
                    return "GB";
                case 4:
                    return "TB";
                case 5:
                    return "PB";
                case 6:
                    return "EB";
                case 7:
                    return "ZB";
                case 8:
                    return "YB";
                default:
                    throw new Error("Unit overflow");
            }
        },
        $convertResult2Text: function (result, suffix = "") {
            return result.value.toFixed(2) + " " + this.$unitCode2UnitChar(result.unit) + suffix;
        }
    }
}