<template>
    <tr>
        <td></td>
        <td>{{ ipAddress.human_readable_first_usable }} <template v-if="ipAddress.human_readable_first_usable !== ipAddress.human_readable_last_usable"><b style="color: red;"> - </b>{{ ipAddress.human_readable_last_usable }}</template></td>
        <td v-if="isV4">{{ cidr2Netmask[ipAddress.pool.network_bits] }} ({{ ipAddress.pool.network_bits }})</td>
        <td v-else>{{ ipAddress.pool.network_bits }}</td>
        <td>{{ ipAddress.pool.human_readable_gateway }}</td>
        <td class="three wide column">
            <template v-if="ipAddress.unbindable">
                <button class="ui tiny button" v-on:click="$emit('unbind', ipAddress.pool_id, ipAddress.position, interfaceIndex, ipAddressIndex)">解绑</button>
                <button class="ui tiny red button" v-on:click="$emit('release', ipAddress.pool_id, ipAddress.position, interfaceIndex, ipAddressIndex)">释放</button>
            </template>
        </td>
    </tr>
</template>

<script>
    import CIDR2Netmask from "./../../../IPAddress/CIDR2Netmask";

    export default {
        name: "IPAddressTableRow",
        props: ["interfaceIndex", "ipAddressIndex", "ipAddress", "isV4"],
        data: function () {
            return {
                cidr2Netmask: CIDR2Netmask.cidr2Netmask,
            };
        }
    }
</script>

<style scoped>

</style>