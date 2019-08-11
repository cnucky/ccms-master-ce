export default {
    data: function () {
        return {
            operationRoutePrefix: "",
        };
    },
    methods: {
        powerOnAction: function (instance, beforeSend = null, completed = null, onSuccess = null) {
            this.submitPowerAction("on", instance, beforeSend, completed, onSuccess);
        },
        powerResetAction: function (instance, beforeSend = null, completed = null, onSuccess = null) {
            this.submitPowerAction("reset", instance, beforeSend, completed, onSuccess);
        },
        powerOffAction: function (instance, beforeSend = null, completed = null, onSuccess = null) {
            this.submitPowerAction("off", instance, beforeSend, completed, onSuccess);
        },
        submitPowerAction: function (powerAction, instance, beforeSend = null, completed = null, onSuccess = null) {
            // this.isLoading = true;
            if (typeof beforeSend === "function")
                beforeSend();
            axios.post(route(this.operationRoutePrefix + 'computeInstances.power.' + powerAction, [instance.id]), null, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        // this.positiveFloatingMessage("操作请求已提交");
                        if (typeof onSuccess === "function")
                            onSuccess();
                        instance.processing_operation_requests.push(data.operationRequest);
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
        },
        massPowerOnAction: function (instanceList, beforeSend = null, completed = null, onSuccess = null) {
            this.submitMassPowerAction("on", instanceList, beforeSend, completed, onSuccess);
        },
        massPowerResetAction: function (instanceList, beforeSend = null, completed = null, onSuccess = null) {
            this.submitMassPowerAction("reset", instanceList, beforeSend, completed, onSuccess);
        },
        massPowerOffAction: function (instanceList, beforeSend = null, completed = null, onSuccess = null) {
            this.submitMassPowerAction("off", instanceList, beforeSend, completed, onSuccess);
        },
        submitMassPowerAction: function (powerAction, instanceList, beforeSend = null, completed = null, onSuccess = null) {
            // this.isLoading = true;
            if (typeof beforeSend === "function")
                beforeSend();
            axios.post(route(this.operationRoutePrefix + 'computeInstances.power.mass.' + powerAction), {items: instanceList}, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        // this.positiveFloatingMessage("操作请求已提交");
                        if (typeof onSuccess === "function")
                            onSuccess();
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
        },
    }
}