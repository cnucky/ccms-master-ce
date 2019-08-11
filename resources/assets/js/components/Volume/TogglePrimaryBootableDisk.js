export default {
    data: function () {
        return {
            isTogglingBootableDisk: false,
        };
    },
    methods: {
        togglePrimaryBootableDisk: function (volume) {
            this
                .confirmModal()
                .withHeader("确认")
                .withContent("确定将卷["+ volume.unique_id +"]设为首选启动盘？")
                .withOnApprove(() => {
                    this.isTogglingBootableDisk = true;
                    axios.patch(route("localVolumes.togglePrimaryBootableDisk", [volume.id]))
                        .then((response) => {
                            var data = response.data;
                            if (data.result) {
                                this.$emit("operationRequestCreated", data.operationRequest);
                                this.positiveFloatingMessage("操作请求已提交");
                                this.filter();
                            } else {
                                this.$globalErrnoHandler(data);
                            }
                        })
                        .catch(this.$axiosCatchError2Console)
                        .then(() => {
                            this.isTogglingBootableDisk = false;
                        })
                    ;
                })
                .show()
            ;
        }
    }
}