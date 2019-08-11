export default {
    data: function () {
        return {
            isImporting: false,

            autoImport: {
                cidr: "",
                autoRemoveUnusableAddress: false,
            },
        }
    },
    methods: {
        importFromCIDR: function () {
            this.isImporting = true;
            axios.post(route("CIDR.parser"), this.autoImport, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        var parsedResult = data.parsed;
                        this.pool.human_readable_first_usable_ip = parsedResult.first;
                        this.pool.human_readable_last_usable_ip = parsedResult.last;
                        if (parsedResult.hasOwnProperty("networkBits"))
                            this.pool.network_bits = parsedResult.networkBits;
                        if (parsedResult.hasOwnProperty("gateway"))
                            this.pool.human_readable_gateway = parsedResult.gateway;
                        this.positiveFloatingMessage("导入成功，请根据实际情况进行修改");
                    } else {
                        this.negativeFloatingMessage("CIDR解析失败，请检查格式");
                    }
                })
                .catch((error) => {
                })
                .then(() => {
                    this.isImporting = false;
                })
            ;
        },
    }
}