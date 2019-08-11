<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">#{{ ticket.id }} 工单详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <div class="ui one column grid">
                    <column name="标题" :inline="true" :dt-style="{width: '90px'}">{{ ticket.title }}</column>
                    <column name="状态" :inline="true" :dt-style="{width: '90px'}">
                        <status :status="ticket.status"></status>
                        <button v-if="ticket.status !== 6" type="button" class="ui tiny red button" style="margin-left: 30px;" v-on:click="closeTicket">关闭工单</button>
                        <span v-else style="margin-left: 20px;">如需重启开启本工单，请提交新回复。</span>
                    </column>
                    <column name="创建于" :inline="true" :dt-style="{width: '90px'}"><local-time :time="ticket.created_at"></local-time></column>
                    <column name="最后回复于" :inline="true" :dt-style="{width: '90px'}"><local-time :time="ticket.replied_at"></local-time></column>
                </div>
                <relative-service :dt-style="{width: '90px'}" :product-type="ticket.product_type" :relative-service="relativeService"></relative-service>
                <div class="ui divider" style="margin-top: 30px;"></div>
                <form class="ui form" style="margin-top: 30px;" v-on:submit.prevent="makeReply">
                    <div class="ui field">
                        <label>添加回复</label>
                        <textarea rows="3" v-model="replyContent" required></textarea>
                    </div>
                    <div class="ui field">
                        <button type="submit" class="ui tiny button" style="display: block; margin-left: auto; margin-right: auto;" v-bind:class="{loading: isSubmittingReply}" :disable="isSubmittingReply">提交</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-for="reply in replies" class="sixteen wide column">
            <div v-if="reply.admin_name" class="ui top attached warning message">
                {{ reply.admin_name }} - <b>客服</b>
                <span style="float: right"><local-time :time="reply.created_at"></local-time></span>
            </div>
            <div v-else class="ui top attached message">
                {{ $store.getters.user.name }} - <b>客户</b>
                <span style="float: right"><local-time :time="reply.created_at"></local-time></span>
            </div>
            <div class="ui very padded bottom attached segment">
                <div style="white-space: pre;">{{ reply.content }}</div>
            </div>
        </div>

        <div class="sixteen wide column" style="margin-bottom: 100px;"></div>
    </div>
</template>

<script>
    import Status from "../../Ticket/Status";
    import Column from "../ComputeInstance/Show/Information/Column";
    import LocalTime from "../../LocalTime";
    import RelativeService from "../../Ticket/UserRelativeService";
    export default {
        name: "Show",
        components: {RelativeService, LocalTime, Column, Status},
        data: function () {
            return {
                isLoading: false,
                ticket: {},
                replies: [],
                relativeService: null,
                replyContent: "",
                isSubmittingReply: false,
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("tickets.show", [this.ticketId]), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.ticket = data.ticket;
                        this.replies = data.replies;
                        this.relativeService = data.relativeService;
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
        methods: {
            makeReply: function () {
                this.isSubmittingReply = true;
                axios.post(route("tickets.makeReply", [this.ticketId]), {content: this.replyContent}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.replies.unshift(data.reply);
                            this.replyContent = "";
                            this.ticket.status = 2;
                            this.positiveFloatingMessage("回复成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingReply = false;
                    })
                ;
            },
            closeTicket: function () {
                this.isLoading = true;
                axios.patch(route("tickets.close", [this.ticketId]))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.ticket.status = 6;
                            this.positiveFloatingMessage("工单关闭成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            }
        },
        computed: {
            ticketId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>