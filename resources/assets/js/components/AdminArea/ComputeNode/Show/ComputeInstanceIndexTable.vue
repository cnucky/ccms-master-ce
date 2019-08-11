<script>
    import Index from "./../../ComputeInstance/IndexTable";

    export default {
        extends: Index,
        name: "ComputeInstanceIndexTable",
        props: ["computeNode"],
        data: function () {
            return {
                indexRouteName: "computeNodes.computeInstances",
            };
        },
        methods: {
            additionalFilterArguments: function (filterArguments) {
                filterArguments.computeNode = this.computeNode.id;
                return filterArguments;
            },
        }
    }
</script>