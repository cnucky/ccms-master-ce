export default {
    methods: {
        $generateDateList: function (items) {
            var dateMap = {};
            for (var itemIndex in items) {
                for (var date in items[itemIndex]) {
                    dateMap[this.$moment(date).unix()] = date;
                }
            }

            var dateList = [];
            for (var i in dateMap) {
                dateList.push(dateMap[i]);
            }

            return dateList;
        },
        $propertyValueOrZero: function (object, key) {
            return object.hasOwnProperty(key) ? object[key] : "0";
        }
    }
}