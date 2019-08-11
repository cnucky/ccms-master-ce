<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">基本设置</h3>
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoadingCertificates}">
                <form class="ui form" v-on:submit.prevent="store">
                    <div class="ui two fields">
                        <div class="ui field">
                            <label>Host（留空使用节点Host）</label>
                            <input type="text" :placeholder="computeNode.host" v-model="noVNCSettings.no_vnc_host">
                        </div>
                        <div class="ui field">
                            <label>服务器端口（留空默认为6080）</label>
                            <input type="number" min="1025" max="65535" placeholder="6080" v-model="noVNCSettings.no_vnc_port">
                        </div>
                    </div>
                    <div class="ui two fields">
                        <div class="ui field">
                            <label>客户端连接端口（默认为服务器端口）</label>
                            <input type="number" min="1" max="65535" :placeholder="noVNCSettings.no_vnc_port ? noVNCSettings.no_vnc_port : 6080" v-model="noVNCSettings.no_vnc_client_connect_port">
                        </div>
                        <div class="ui field">
                            <label>证书</label>
                            <certificate-select v-if="!isLoadingCertificates" v-model="noVNCSettings.certificate_id" :certificates="certificates"></certificate-select>
                        </div>
                    </div>
                    <div class="ui field">
                        <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存并应用</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import CertificateSelect from "./CertificateSelect";
    export default {
        name: "NoVNC",
        components: {CertificateSelect},
        props: ["computeNode"],
        data: function () {
            return {
                isSubmitting: false,
                isLoadingCertificates: false,
                certificates: [],
                noVNCSettings: {},
            };
        },
        created: function () {
            this.noVNCSettings = {
                no_vnc_host: this.computeNode.no_vnc_host,
                no_vnc_port: this.computeNode.no_vnc_port,
                certificate_id: this.computeNode.certificate_id,
                no_vnc_client_connect_port: this.computeNode.no_vnc_client_connect_port,
            };
        },
        mounted: function () {
            this.loadCertificates();
        },
        methods: {
            loadCertificates: function () {
                this.isLoadingCertificates = true;
                axios.get(route("certificates.list"), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.certificates = data.certificates;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$globalErrnoHandler)
                    .then(() => {
                        this.isLoadingCertificates = false;
                    })
                ;
            },
            store: function () {
                this.isSubmitting = true;
                axios.patch(route("computeNodes.changeNOVNCBasicSetting", [this.$router.currentRoute.params.id]), this.noVNCSettings, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("data-updated", data);
                            this.positiveFloatingMessage("应用成功")
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        },
    }
</script>

<style scoped>

</style>