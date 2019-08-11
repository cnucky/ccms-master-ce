<script>
    import IPPool from "./IPPool";
    export default {
        name: "IPv4Pool",
        extends: IPPool,
        data: function () {
            return {
                modelRouteName: "ipv4Pools",
            };
        }
    }
</script>
