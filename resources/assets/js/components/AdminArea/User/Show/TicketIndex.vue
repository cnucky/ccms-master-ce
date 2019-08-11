<script>
    import IndexTable from "../../Ticket/IndexTable";
    export default {
        extends: IndexTable,
        name: "TicketIndex",
        props: ["userData"],
        data: function () {
            return {
                indexRouteName: "admin.tickets.indexByUser",
            };
        },
        methods: {
            additionalFilterArguments: function (filterArguments) {
                filterArguments.user = this.userData.user.id;
                return filterArguments;
            },
        }
    }
</script>
