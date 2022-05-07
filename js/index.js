// 后端API基础路径 调用API时可引用该路径

var baseUrl = ''
if (location.hostname == 'localhost') {
    var baseUrl = 'http://app.ouyangpeng.top/app/poncon-copyrighted-music/'
}

var webTitle = '无忧音乐网 - 海量无版权音乐共享'
var userLoginDataKeyName = 'copyrighted_music_UserLoginData'
var UserHasLogin = 0 // 用户已经登录？

var request_updateFileList
    = request_getFileInfo
    = $.ajax()

function router(hash) {
    // 获取目标界面标识
    hash = hash.split('/')
    var target = hash[1]
    // 交换界面显示
    $('.page-oyp').hide()
    var page = $('.page-' + target)
    page.fadeIn()
    $('.navbar-collapse li.active').removeClass('active')
    $('.navbar-collapse li.nav-item-' + target).addClass('active')

    // 判断目标界面标识 执行对应模块的载入事件
    if (target == 'home') {
        document.title = webTitle
    } else if (target == 'about') {
        document.title = '关于 - ' + webTitle
        // 界面载入事件
        // ...
    } else if (target == 'search') {
        document.title = '音乐搜索 - ' + webTitle
    } else if (target == 'login') {
        if (hash[2] == 'register') {
            page.find('input').val('')
            page.find('.page-sub-register').show()
            page.find('.title').html('用户注册')
            document.title = '用户注册 - ' + webTitle
        } else {
            page.find('.page-sub-login').show()
            page.find('.title').html('用户登录')
            document.title = '用户登录 - ' + webTitle
        }
    } else {
        location.hash = '/home/'
    }
}
window.addEventListener('hashchange', function (event) {
    // 监听Hash改变 通过location.hash='xxx'可触发
    var hash = new URL(event.newURL).hash
    router(hash)
})

function hasLogin() {
    // 登陆成功时执行的事件
    UserHasLogin = 1
    $('.navbar-collapse li.nav-item-login').hide()
    $('.navbar-collapse li.nav-item-logout').show()
}


function notLogin() {
    // 未登录时执行的事件
    UserHasLogin = 0
    $('.navbar-collapse li.nav-item-logout').hide()
    $('.navbar-collapse li.nav-item-login').show()
}
function ifLogin() {
    try {
        var userdata = JSON.parse(localStorage[userLoginDataKeyName])
        if (userdata.username && userdata.password && userdata.email) {
            var success = 0
            $.ajax({
                url: baseUrl + 'api/login.php',
                method: 'post',
                data: {
                    username: userdata.username,
                    password: userdata.password
                },
                success: function (data) {
                    if (data.code == 200) {
                        // 登录成功
                        hasLogin()
                        localStorage[userLoginDataKeyName] = JSON.stringify(data.result)
                        success = 1
                    } else {
                        notLogin()
                        success = 0
                    }
                },
                async: false
            })
            if (success) {
                return true
            } else {
                return false
            }
        } else {
            notLogin()
            return false
        }
    } catch (error) {
        notLogin()
        return false
    }
}





$(document).ready(function () {
    console.log('%cHello Poncon 2022-05-07', 'color: orange; border: 2px solid orange; padding: 2px 4px; font-size: 16px;')
    if (!location.hash.split('/')[1]) {
        history.replaceState({}, null, '#/home/')
    }
    router(location.hash)

    ifLogin()
    function validation_register(data, mode) {
        // mode 1:验证整个注册页表单 2: 跳过验证码字段
        data.username = $('.page-login .page-sub-register .input-username').val()
        data.email = $('.page-login .page-sub-register .input-email').val()
        data.password = $('.page-login .page-sub-register .input-password').val()
        var password2 = $('.page-login .page-sub-register .input-password2').val()
        data.verCode = $('.page-login .page-sub-register .input-verCode').val()
        if (!data.username.match(/^\w{4,20}$/)) {
            alert('用户名要求长度为4-20，只能包含数字、字母和下划线')
            return false
        } else if (!data.email.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/)) {
            alert('邮箱不合法')
            return false
        } else if (!data.password.match(/^\w{8,20}$/)) {
            alert('密码要求长度为8-20，只能包含数字、字母和下划线')
            return false
        } else if (data.password != password2) {
            alert('两次输入的密码不一致')
            return
        } else if (!data.verCode && mode == 1) {
            alert('请输入验证码')
            return false
        }
        return true
    }

    $('.page-login .page-sub-register button.register').click(function () {
        var data = {}
        if (!validation_register(data, 1)) {
            return
        }
        var username = data.username
        var password = data.password
        $.ajax({
            method: 'post',
            url: baseUrl + './api/register.php',
            data: {
                username: username,
                password: md5(password),
                email: data.email,
                verCode: data.verCode
            },
            contentType: 'application/x-www-form-urlencoded',
            dataType: 'json',
            success: function (data) {
                if (data.code == 200) {
                    location.hash = '/login/'
                    console.log(username, password)
                    $('.page-login .page-sub-login .input-username').val(username)
                    $('.page-login .page-sub-login .input-password').val(password)
                } else {

                }
                alert(data.msg)
            }
        })
    })

    $('.page-login .page-sub-register button.getVerCode').click(function () {
        var data = {}
        if (!validation_register(data, 2)) {
            return
        }
        var ele = $(this)
        ele.attr('disabled', 'disabled')
        $.ajax({
            method: 'post',
            url: baseUrl + './api/getCode.php',
            data: {
                email: data.email
            },
            contentType: 'application/x-www-form-urlencoded',
            dataType: 'json',
            success: function (data) {
                if (data.code == 200) {
                    for (var i = 0; i < 60; i++) {
                        (function (i) {
                            setTimeout(function () {
                                ele.html(60 - i + '秒后重新获取')
                            }, 1000 * i)
                        }(i))
                    }
                    setTimeout(() => {
                        ele.removeAttr('disabled')
                        ele.html('获取验证码')
                    }, 1000 * 60);
                } else {
                    alert(data.msg)
                    ele.removeAttr('disabled')
                    ele.html('获取验证码')
                }
            }
        })
    })

    $('.page-login .page-sub-login button.login').click(function () {
        var username = $('.page-login .page-sub-login .input-username').val()
        var password = $('.page-login .page-sub-login .input-password').val()
        if (!username || !password) {
            alert('账号和密码不能为空')
            return
        } else if ((!username.match(/^\w{4,20}$/)
            && !username.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/))
            || !password.match(/^\w{8,20}$/)) {
            alert('账号或密码格式错误')
            return
        }
        $.ajax({
            method: 'post',
            url: baseUrl + './api/login.php',
            data: {
                username: username,
                password: md5(password)
            },
            contentType: 'application/x-www-form-urlencoded',
            dataType: 'json',
            success: function (data) {
                if (data.code == 200) {
                    // 登录成功
                    localStorage[userLoginDataKeyName] = JSON.stringify(data.result)
                    location.hash = '/home/'
                    hasLogin()
                } else {
                    alert(data.msg)
                }
            }
        })
    })
    $('.page-login .page-sub-login .input-password').bind('keyup', function (event) {
        if (event.keyCode == 13) {
            $(this).blur()
            $('.page-login .page-sub-login button.login').click()
        }
    })
    $('.page-login .page-sub-register .input-verCode').bind('keyup', function (event) {
        if (event.keyCode == 13) {
            $(this).blur()
            $('.page-login .page-sub-register button.getVerCode').click()
        }
    })
    $('.page-home button.btn-input-file').click(function () {
        $('.page-home input.input-file').click()
    })
    $('.page-home input.input-file').change(function () {
        // 没有登录，无法添加文件，转到登录
        if (!UserHasLogin) {
            location.hash = '/login/'
            $('.page-home input.input-file').val('')
            return
        }
        if (isWorking) {
            $('.page-home input.input-file').val('')
            return
        }
        $('.page-home .btns').show()
        var fileDatas = $(this).prop('files')
        if (!fileDatas.length) {
            $('.page-home input.input-file').val('')
            return
        }

        if (isWorkFinished) {
            isWorkFinished = 0
            // 刚刚完成任务，此时新增文件，直接清空原列表，并恢复按钮
            $('.page-home .workList').html('')
            fileList = []
            $('.page-home button.allUpload').show()
            $('.page-home button.allCancel').html('全部取消')
        }

        addFile(fileDatas)
    })

    $('.nav-item-logout').click(function () {
        if (!confirm('确定要退出登录吗？')) {
            return
        }
        localStorage[userLoginDataKeyName] = ''
        notLogin()
    })

    $('body').show()
    $('a').attr('draggable', 'false')
    new ClipboardJS('.copybtn')
})