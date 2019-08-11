export default {
    methods: {
        isNeedPromoteForTrust: function (data) {
            return data.errno === 20010;
        },
        promoteForTrust: function (data) {
            var certificateFingerprint = data.fingerprint;
            var certificateInformation = data.parsedX509;
            this.confirmModal()
                .withHeader("未知证书")
                .withContent("证书信息：<br>Subject: " + certificateInformation.name + "<br>" + "Fingerprint: " + certificateFingerprint + "<br><br><b style='color: red;'>请确保fingerprint与节点CA证书的一致，否则可能遭遇中间人攻击。</b><br>在节点上执行以下命令即可查看CA证书fingerprint：<div class='ui existing segment'>ccms-slave cert:ca-fp</div><br>请问是否要信任此证书？")
                .withCancelText("不信任")
                .withConfirmText("信任")
                .withOnApprove(() => {
                    this.$refs.computeNodeForm.temporaryItem.trust_fingerprint = certificateFingerprint;
                    this.$refs.computeNodeForm.$refs.submitButton.click();
                })
                .show()
            ;
        }
    }
}