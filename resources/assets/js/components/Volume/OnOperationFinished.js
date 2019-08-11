export default {
    methods: {
        onOperationFinished: function (finishedItems) {
            var name;
            for (var i in finishedItems) {
                if (finishedItems[i].local_volume && finishedItems[i].local_volume.hasOwnProperty("unique_id")) {
                    name = finishedItems[i].local_volume.unique_id;
                } else if (finishedItems[i].type === 3 && finishedItems[i].data) {
                    name = finishedItems[i].data;
                } else {
                    name = "#" + finishedItems[i].resource_id;
                }
                if (finishedItems[i].operation_status === 3) {
                    if (this.$te("status.volumeOperationSubStatus." + finishedItems[i].sub_status)) {
                        if (finishedItems[i].local_volume) {
                            this.negativeFloatingMessage(this.$t("status.volumeOperationSubStatus." + finishedItems[i].sub_status, [name]));
                        } else {
                            this.negativeFloatingMessage(this.$t("status.volumeOperationSubStatus." + finishedItems[i].sub_status));
                        }
                    } else {
                        var suffix = "";
                        if (finishedItems[i].type === 5 || finishedItems[i].type === 1) {
                            suffix = "，如实例正在运行，请关闭实例后重试";
                        }
                        this.negativeFloatingMessage("卷["+ name +"]"+ this.$t("operationRequest.type.localVolume." + finishedItems[i].type) +"失败" + suffix);
                    }
                } else if (finishedItems[i].operation_status === 2) {
                    this.positiveFloatingMessage("卷["+ name +"]"+ this.$t("operationRequest.type.localVolume." + finishedItems[i].type) +"成功");
                }
            }

            this.filter(true);
        },
    }
}