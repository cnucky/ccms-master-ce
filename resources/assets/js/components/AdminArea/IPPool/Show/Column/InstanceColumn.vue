<template>
    <td>
        <template v-if="entry.network_interface && entry.network_interface.instance">
            <router-link :to="{name: 'admin.computeInstances.dashboard', params: {id: entry.network_interface.instance.id}}" style="display: inline-block;">
                <div class="instance-name">{{ entry.network_interface.instance.name }}</div>
                <div class="instance-size">
                    {{ entry.network_interface.instance.unique_id }} - {{ entry.network_interface.instance.client_instance_size.vCPU }} vCPU/ {{entry.network_interface.instance.client_instance_size.memory }} MiB
                </div>
            </router-link>
        </template>
        <template v-else>-</template>
    </td>
</template>

<script>
    export default {
        name: "InstanceColumn",
        props: ["entry"],
    }
</script>

<style scoped>
    .instance-name {
        font-size: 1.1em;
        line-height: unset;
        font-weight: 600;
        color: #0069ff;
        margin-bottom: 2px;

    }

    .instance-size {
        color: #676767;
        line-height: .8rem;
        font-size: .8rem;
        clear: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-bottom: 1px;
    }
</style>