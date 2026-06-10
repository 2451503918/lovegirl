var botui = new BotUI("botui-app");
botui.message.bot({
    delay: 200,
    content: "Hi, 欢迎你的来访"
}).then(function () {
    return botui.message.bot({
        delay: 1000,
        content: "这里是 Ki&Yi 小站"
    })
}).then(function () {
    return botui.message.bot({
        delay: 1000,
        content: "记录生活 留住感动"
    })
}).then(function () {
    return botui.action.button({
        delay: 1500,
        action: [{
            text: "听我介绍 😜",
            value: "and"
        },
            {
                text: "结束介绍 😅",
                value: "gg"
            }]
    })
}).then(function (res) {
    if (res.value == "and") {
        other()
    }
    if (res.value == "gg") {
        return botui.message.bot({
            delay: 1500,
            content: " ![告辞](/Style/img/chat-goodbye.jpg) "
        })
    }
});

var other = function () {
    botui.message.bot({
        delay: 1500,
        content: "😳"
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "在家呆着特别无聊所以用自己仅有的技术写出来了这个小站"
        })
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "如果不出意外的话会是开源的第一个小项目"
        })
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "写这个小站后端的时候脑子都要炸了"
        })
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "目前已基本完善 剩下一个恋爱列表页面"
        })
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "喜欢探索知识，热爱学习新知识，热爱开源文化"
        })
    }).then(function () {
        return botui.action.button({
            delay: 1500,
            action: [{
                text: "为什么叫 Ki？ 😠",
                value: "next"
            }]
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "不知道你有没有看过《比悲伤更悲伤的故事》"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "“嗨，我是k，如果有下辈子的话，”"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "“我想当戒指，眼镜，床和笔记本，这样的话，我就可以...”"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "当然跟这个没有关系哈哈"
        })
    }).then(function () {
        return botui.action.button({
            delay: 1500,
            action: [{
                text: "本站所有页面",
                value: "next"
            }]
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "首页 index"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "点点滴滴 little💑"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "留言板 leaving🐼"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "关于 about"
        })
    }).then(function (res) {
        return botui.message.bot({
            delay: 1500,
            content: "欢迎您的来访 IP已记录"
        })
    }).then(function () {
        return botui.message.bot({
            delay: 1500,
            content: "  "
        })
    });
}