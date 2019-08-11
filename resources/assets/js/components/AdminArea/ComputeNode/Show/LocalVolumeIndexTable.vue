<script>
    import IndexTable from "./../../../Volume/AdminIndexTable"

    export default {
        extends: IndexTable,
        name: "LocalVolumeIndexTable",
        props: ["computeNode"],
        data: function () {
            return {
                indexRouteName: "computeNodes.localVolumes",
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