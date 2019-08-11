<template>
    <span ref="instanceStatusIcon" class="running-label" v-bind:class="[runningLabelStatusClass]" :data-content="popupText" data-variation="inverted"></span>
</template>

<script>
    export default {
        name: "PowerStatusIconLabel",
        props: ["powerStatus"],
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
                switch (this.powerStatus) {
                    case 0:
                        return "已关机";
                    case 1:
                        return "运行中";
                    default:
                        return "未知";
                }
            },
            runningLabelStatusClass: function () {
                switch (this.powerStatus) {
                    case 0:
                        return "stopped";
                    case 1:
                        return "running";
                    default:
                        return "unknown";
                }
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