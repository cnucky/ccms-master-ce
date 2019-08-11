<script>
    import IPPool from "./IPPool";
    export default {
        name: "IPv6Pool",
        extends: IPPool,
        data: function () {
            return {
                modelRouteName: "ipv6Pools",
            };
        }
    }
</script>
