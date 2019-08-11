<template>
    <tr>
        <td></td>
        <td>{{ ipAddress.human_readable_first_usable }} <template v-if="ipAddress.human_readable_first_usable !== ipAddress.human_readable_last_usable"><b style="color: red;"> - </b>{{ ipAddress.human_readable_last_usable }}</template></td>
        <td v-if="isV4">{{ cidr2Netmask[ipAddress.pool.network_bits] }} ({{ ipAddress.pool.network_bits }})</td>
        <td v-else>{{ ipAddress.pool.network_bits }}</td>
        <td>{{ ipAddress.pool.human_readable_gateway }}</td>
        <td>
            <template v-if="!isPrivate">
                <template v-if="ipAddress.unbindable">
                    <button class="ui tiny button" v-on:click="$emit('unbind', ipAddress.id, interfaceIndex, ipAddressIndex)">解绑</button>
                    <button class="ui tiny red button" v-on:click="$emit('release', ipAddress.id, interfaceIndex, ipAddressIndex)">释放</button>
                    <button class="ui tiny blue button" v-on:click="$emit('convert', ipAddress.id, interfaceIndex, ipAddressIndex)">转为非弹性</button>
                </template>
                <template v-else>
                    <button class="ui tiny blue button" v-on:click="$emit('convert', ipAddress.id, interfaceIndex, ipAddressIndex)">转为弹性</button>
                </template>
            </template>
            <router-link v-if="isV4" class="ui tiny button" :to="{name: 'ipv4Pools.show', params: {id: ipAddress.pool_id}}">地址池</router-link>
            <router-link v-else class="ui tiny button" :to="{name: 'ipv6Pools.show', params: {id: ipAddress.pool_id}}">地址池</router-link>
        </td>
    </tr>
</template>

<script>
    import CIDR2Netmask from "./../../../IPAddress/CIDR2Netmask";

    export default {
        name: "IPAddressTableRow",
        props: ["isPrivate", "interfaceIndex", "ipAddressIndex", "ipAddress", "isV4"],
        data: function () {
            return {
                cidr2Netmask: CIDR2Netmask.cidr2Netmask,
            };
        }
    }
</script>

<style scoped>

</style>