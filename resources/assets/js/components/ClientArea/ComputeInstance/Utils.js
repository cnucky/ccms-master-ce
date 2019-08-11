export default {
    data: function () {
        return {
            utilsRoutePrefix: "",
        };
    },
    methods: {
        getSystemPasswordAndShow: function (instance, beforeSend, completed) {
            if (typeof beforeSend === "function")
                beforeSend();
            axios.get(route(this.utilsRoutePrefix + "computeInstances.show.password", [instance.id]), null, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.confirmModal()
                            .init()
                            .withConfirmClass("green")
                            .withIconClass("check icon")
                            .withHeader("系统密码")
                            .withContent("<div class='ui input' style='width: 100%;'><input type='text' value='"+ data.password +"' readonly></div>")
                            .withModalClass("mini")
                            .withCancelClass("")
                            .withCancelText("关闭")
                            .withConfirmText("复制并关闭")
                            .withOnApprove(() => {
                                this.$copyText(data.password);
                            })
                            .show()
                        ;
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    if (typeof completed === "function")
                        completed();
                })
            ;
        }
    }
}