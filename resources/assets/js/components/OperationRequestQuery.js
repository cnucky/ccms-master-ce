export default {
    data: function () {
        return {
            isOperationRequestQueryDestroyed: false,
            needOperationRequestResource: false,
        };
    },
    mounted: function () {
        this.startTimeoutHandler();
    },
    destroyed: function () {
        this.isOperationRequestQueryDestroyed = true;
    },
    methods: {
        startTimeoutHandler: function () {
            setTimeout(this.timeoutHandler, 2000);
        },
        timeoutHandler: function () {
            if (this.isOperationRequestQueryDestroyed)
                return;

            var operationRequestList = this.operationRequestList;
            if (operationRequestList.length) {
                axios.post(route(this.operationRoutePrefix + 'operationRequests.query'), {operationRequestList: operationRequestList, needResource: this.needOperationRequestResource}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            var finishedItems = [];

                            // Retrieve finished operation requests
                            data.operationRequests.forEach((item) => {
                                if (item.operation_status === 2 || item.operation_status === 3) {
                                    finishedItems.push(item);
                                }
                            });

                            if (finishedItems.length) {
                                // If there any operation request finished, update instance information
                                if (typeof this.onOperationFinished === "function") {
                                    this.onOperationFinished(finishedItems);
                                }
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.startTimeoutHandler();
                    })
                ;
            } else {
                this.startTimeoutHandler();
            }
        },
        isSpecificOperationRequestExists: function (resourceTypeCode, typeCode = null) {
            for (var i in this.operationRequestList) {
                if (this.operationRequestList[i].type == typeCode) {
                    if (typeCode !== null) {
                        if (this.operationRequestList[i].resource_type != resourceTypeCode)
                            return false;
                    }
                    return this.operationRequestList[i];
                }
            }
            return false;
        }
    }
}