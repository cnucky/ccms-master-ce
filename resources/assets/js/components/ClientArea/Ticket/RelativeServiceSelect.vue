<template>
    <div ref="relativeServiceSelect" class="ui search selection dropdown" v-bind:class="{loading: isLoading}">
        <i class="dropdown icon"></i>
        <div class="default text">无</div>
        <div class="menu">
            <div class="item" :data-value="null" v-on:click="$emit('selected', null, null)">无</div>
            <template v-if="computeInstances.length">
                <div class="ui divider"></div>
                <div class="header">
                    <i class="server icon"></i>
                    计算实例
                </div>
                <div v-for="computeInstance in computeInstances" class="item" :data-value="computeInstance.id" v-on:click="$emit('selected', 0, computeInstance.id)">{{ computeInstance.unique_id }} - {{ computeInstance.name }}</div>
            </template>
            <template v-if="localVolumes.length">
                <div class="ui divider"></div>
                <div class="header">
                    <i class="hdd icon"></i>
                    本地卷
                </div>
                <div v-for="localVolume in localVolumes" class="item" :data-value="localVolume.id" v-on:click="$emit('selected', 1, localVolume.id)">{{ localVolume.unique_id }} - {{ localVolume.capacity }}GiB</div>
            </template>
            <template v-if="ipv4s.length">
                <div class="ui divider"></div>
                <div class="header">
                    <i class="fa fa-globe"></i>
                    IPv4
                </div>
                <div v-for="ipv4 in ipv4s" class="item" :data-value="ipv4.id" v-on:click="$emit('selected', 2, ipv4.id)">{{ ipv4.human_readable_first_usable }}<template v-if="ipv4.human_readable_first_usable !== ipv4.human_readable_last_usable"> - {{ ipv4.human_readable_last_usable }}</template></div>
            </template>
            <template v-if="ipv6s.length">
                <div class="ui divider"></div>
                <div class="header">
                    <i class="fa fa-globe"></i>
                    IPv6
                </div>
                <div v-for="ipv6 in ipv6s" class="item" :data-value="ipv6.id" v-on:click="$emit('selected', 3, ipv6.id)">{{ ipv6.human_readable_first_usable }}<template v-if="ipv6.human_readable_first_usable !== ipv6.human_readable_last_usable"> - {{ ipv6.human_readable_last_usable }}</template></div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RelativeServiceSelect",
        data: function () {
            return {
                isLoading: false,
                computeInstances: [],
                localVolumes: [],
                ipv4s: [],
                ipv6s: [],
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("users.services"), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.computeInstances = data.computeInstances;
                        this.localVolumes = data.localVolumes;
                        this.ipv4s = data.ipv4s;
                        this.ipv6s = data.ipv6s;
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.isLoading = false;
                })
            ;
        },
        mounted: function () {
            $(this.$refs.relativeServiceSelect).dropdown({
                placeholder: false,
            });
        }
    }
</script>

<style scoped>

</style>