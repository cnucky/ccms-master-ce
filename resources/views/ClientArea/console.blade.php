<html lang="en">
<head>
    <title>Console - Compute Instance</title>
    <link rel="stylesheet" href="{{ asset('static/js/novnc/base.css') }}" title="plain">
    <script src="{{ asset('static/js/novnc/util.js') }}"></script>
</head>
<body style="margin: 0;">
<div id="noVNC_screen">
    <div id="noVNC_status_bar" class="noVNC_status_bar" style="margin-top: 0;">
        <table border=0 width="100%">
            <tr>
                <td width="32%" style="text-align:right;"></td>
                <td>
                    <div id="noVNC_status">Loading...</div>
                </td>
                <td width="32%" style="text-align:right;">
                    <div id="noVNC_buttons">
                        <!-- dirty fix for keyboard on iOS devices -->
                        <input type="button" id="showKeyboard" value="Keyboard" title="Show Keyboard"/>
                        <!-- Note that Google Chrome on Android doesn\'t respect any of these,
                        html attributes which attempt to disable text suggestions on the
                        on-screen keyboard. Let\'s hope Chrome implements the ime-mode
                        style for example -->
                        <!-- TODO: check if this is needed on iOS -->
                        <textarea id="keyboardinput" autocapitalize="off"
                                  autocorrect="off" autocomplete="off" spellcheck="false"
                                  mozactionhint="Enter" onsubmit="return false;"
                                  style="ime-mode: disabled;">
                        </textarea>

                        <input type=button value="Ctrl+Alt+Del" id="sendCtrlAltDelButton">
                        <input type=button value="Fullscreen" id="askFullscreen">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <canvas id="noVNC_canvas" width="640px" height="20px">
        Canvas not supported.
    </canvas>
</div>
<script>
    /*jslint white: false */
    /*global window, $, Util, RFB, */
    "use strict";
    // dirty fix for keyboard on iOS devices
    var keyboardVisible = false;
    var isTouchDevice = false;
    isTouchDevice = 'ontouchstart' in document.documentElement;
    // Load supporting scripts
    Util.load_scripts(["webutil.js", "base64.js", "websock.js", "des.js",
        "input.js", "display.js", "jsunzip.js", "rfb.js"]);
    var rfb;
    function passwordRequired(rfb) {
        var msg;
        msg = '<form onsubmit="return setPassword();"';
        msg += 'role="form"';
        msg += '  style="margin-bottom: 0px">';
        msg += 'Password Required: ';
        msg += '<input type=password size=10 id="password_input" class="noVNC_status">';
        msg += '<\/form>';
        $D('noVNC_status_bar').setAttribute("class", "noVNC_status_warn");
        $D('noVNC_status').innerHTML = msg;
    }
    function setPassword() {
        rfb.sendPassword($D('password_input').value);
        return false;
    }
    function sendCtrlAltDel() {
        rfb.sendCtrlAltDel();
        return false;
    }
    // dirty fix for keyboard on iOS devices
    function showKeyboard() {
        var kbi, skb, l;
        kbi = $D('keyboardinput');
        skb = $D('showKeyboard');
        l = kbi.value.length;
        if (keyboardVisible === false) {
            kbi.focus();
            try {
                kbi.setSelectionRange(l, l);
            } // Move the caret to the end
            catch (err) {
            } // setSelectionRange is undefined in Google Chrome
            keyboardVisible = true;
            //skb.className = "noVNC_status_button_selected";
        } else if (keyboardVisible === true) {
            kbi.blur();
            //skb.className = "noVNC_status_button";
            keyboardVisible = false;
        }
    }
    function updateState(rfb, state, oldstate, msg) {
        var s, sb, cad, af, level;
        s = $D('noVNC_status');
        sb = $D('noVNC_status_bar');
        cad = $D('sendCtrlAltDelButton');
        af = $D('askFullscreen');
        switch (state) {
            case 'failed':
                level = "error";
                break;
            case 'fatal':
                level = "error";
                break;
            case 'normal':
                level = "normal";
                break;
            case 'disconnected':
                level = "normal";
                break;
            case 'loaded':
                level = "normal";
                break;
            default:
                level = "warn";
                break;
        }
        if (state === "normal") {
            cad.disabled = false;
            af.disabled = false;
        }
        else {
            cad.disabled = true;
            af.disabled = true;
        }
        if (typeof(msg) !== 'undefined') {
            sb.setAttribute("class", "noVNC_status_" + level);
            s.innerHTML = msg;
        }
    }
    function fullscreen() {
        var screen=document.getElementById('noVNC_canvas');
        if(screen.requestFullscreen) {
            screen.requestFullscreen();
        } else if(screen.mozRequestFullScreen) {
            screen.mozRequestFullScreen();
        } else if(screen.webkitRequestFullscreen) {
            screen.webkitRequestFullscreen();
        } else if(screen.msRequestFullscreen) {
            screen.msRequestFullscreen();
        }
    }
    window.onscriptsload = function () {
        var host, port, password, path;
        $D('sendCtrlAltDelButton').style.display = "inline";
        $D('sendCtrlAltDelButton').onclick = sendCtrlAltDel;
        $D('askFullscreen').style.display = "inline";
        $D('askFullscreen').onclick = fullscreen;
        // dirty fix for keyboard on iOS devices
        if (isTouchDevice) {
            $D('showKeyboard').onclick = showKeyboard;
            // Remove the address bar
            setTimeout(function () {
                window.scrollTo(0, 1);
            }, 100);
        } else {
            $D('showKeyboard').style.display = "none";
        }
        WebUtil.init_logging(WebUtil.getQueryVar('logging', 'warn'));
        document.title = unescape('{{ $id }} - console - {{ config('app.name') }}');
        // By default, use the host and port of server that served this file
        host = '{{ $host }}';
        port = '{{ $port }}';
        password = '{{ $password }}';
        path = "?id={{ $id }}&salt={{ $salt }}&serial={{ $serial }}&expire_at={{ $expire_at }}&signature={{ urlencode($signature) }}";
        if ((!host) || (!port)) {
            updateState('failed',
                "Must specify host and port in URL");
            return;
        }
        rfb = new RFB({'target': $D('noVNC_canvas'),
            'repeaterID': WebUtil.getQueryVar('repeaterID', ''),
            'true_color': WebUtil.getQueryVar('true_color', true),
            'local_cursor': WebUtil.getQueryVar('cursor', true),
            'shared': WebUtil.getQueryVar('shared', true),
            'encrypt': location.protocol === 'https:',
            'view_only': WebUtil.getQueryVar('view_only', false),
            'updateState': updateState,
            'onPasswordRequired': passwordRequired});
        rfb.connect(host, port, password, path);
    };
</script>
</body>
</html>