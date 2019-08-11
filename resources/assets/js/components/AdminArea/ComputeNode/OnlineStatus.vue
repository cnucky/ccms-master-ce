<template>
    <div style="display: inline;">
        <span ref="instanceStatusIcon" class="running-label" v-bind:class="[runningLabelStatusClass]" :data-content="popupText" data-variation="inverted"></span>
        <template v-if="withText">{{ popupText }}</template>
    </div>
</template>

<script>
    export default {
        name: "OnlineStatus",
        props: ["lastCommunicatedAt", "serverTime", "withText"],
        mounted: function () {
            $(this.$refs.instanceStatusIcon).popup({
                distanceAway: 50,
                hoverable: true,
            });
        },
        updated: function () {
            $(this.$refs.instanceStatusIcon).popup({
                distanceAway: 50,
                hoverable: true,
            });
        },
        computed: {
            popupText: function () {
                if (this.lastCommunicatedAt === null)
                    return "待上线";
                if (this.timeDiffWithServerTime > 600)
                    return "离线";
                return "在线";
            },
            runningLabelStatusClass: function () {
                if (this.lastCommunicatedAt === null)
                    return "unknown";
                if (this.timeDiffWithServerTime > 600)
                    return "stopped";
                return "running";
            },
            serverTimeAsUnixTimestamp: function () {
                return Math.floor((new Date(this.serverTime)).getTime() / 1000);
            },
            lastCommunicatedAsUnixTimestamp: function () {
                return Math.floor((new Date(this.lastCommunicatedAt)).getTime() / 1000);
            },
            timeDiffWithServerTime: function () {
                return this.serverTimeAsUnixTimestamp - this.lastCommunicatedAsUnixTimestamp;
            }
        }
    }
</script>

<style scoped>
    .running-label {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        animation: none 0s ease 0s 1 normal none running;
        margin-right: 15px;
        flex: 0 0 auto;
    }

    .running-label.running {
        background-color: rgb(69, 214, 181);
    }

    .running-label.stopped {
        background-color: rgb(212, 218, 231);
    }

    .running-label.unknown {
        background-color: red;
    }
</style>