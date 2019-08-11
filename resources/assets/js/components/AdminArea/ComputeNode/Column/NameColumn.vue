<template>
    <td class="td-flex">
        <online-status :last-communicated-at="entry.last_communicated_at" :server-time="parentApp.serverTime"></online-status>

        <router-link :to="{name: 'computeNodes.show', params: {id: entry.id}}" style="display: inline-block;">
            {{ entry.name }}
        </router-link>
    </td>
</template>

<script>
    import OnlineStatus from "../OnlineStatus";
    export default {
        name: "NameColumn",
        components: {OnlineStatus},
        props: ["parentApp", "keyName", "entry"],
    }
</script>

<style scoped>
    .td-flex {
        line-height: 31px;
        display: flex;
        flex-direction: row;
        -webkit-box-align: center;
        align-items: center;
    }
</style>