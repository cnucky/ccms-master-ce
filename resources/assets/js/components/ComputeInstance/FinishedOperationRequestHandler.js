export default {
    methods: {
        finishedOperationRequestHandler: function (finishedItems) {
            for (var i in finishedItems) {
                var finishedItem = finishedItems[i];
                var prefix;
                if (finishedItem.instance && finishedItem.instance.hasOwnProperty("name")) {
                    prefix = "实例["+ finishedItem.instance.name +"]";
                } else {
                    try {
                        var object = JSON.parse(finishedItem.data);
                        if (!object.hasOwnProperty("name")) {
                            throw "";
                        }
                        prefix = "实例[" + object.name + "]";
                    } catch (e) {
                        prefix = "实例#" + finishedItem.resource_id;
                    }
                }
                var operationText = this.$t("operationRequest.type.computeInstance." + finishedItem.type);
                if (finishedItem.operation_status === 3) {
                    this.negativeFloatingMessage(prefix + operationText + "失败");
                } else if (finishedItem.operation_status === 2) {
                    this.positiveFloatingMessage(prefix + operationText + "成功");
                }
            }
        }
    }
}