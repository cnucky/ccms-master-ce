export default {
    methods: {
        toggleProtectMode: function (volume) {
            var prefix;
            if (typeof this.operationRoutePrefix === "string") {
                prefix = this.operationRoutePrefix;
            } else if (this.isAdmin) {
                prefix = "admin.";
            }

            axios.patch(route(prefix + "localVolumes.toggleProtectMode", [volume.id]), null, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        volume.protected = data.protected;
                        this.positiveFloatingMessage("操作成功");
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                })
            ;
        },
    }
}