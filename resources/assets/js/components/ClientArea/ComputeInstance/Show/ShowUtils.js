export default {
    methods: {
        isOperationRequestTypeExists: function (typeCode, resourceTypeCode = 0) {
            for (var i in this.instance.processing_operation_requests) {
                if (this.instance.processing_operation_requests[i].type == typeCode && this.instance.processing_operation_requests[i].resource_type == resourceTypeCode)
                    return this.instance.processing_operation_requests[i];
            }
            return false;
        }
    }
}