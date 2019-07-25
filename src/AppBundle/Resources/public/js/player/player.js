/*
 * jPlayer Plugin for jQuery JavaScript Library
 * http://www.happyworm.com/jquery/jplayer
 *
 * Copyright (c) 2009 - 2010 Happyworm Ltd
 * Dual licensed under the MIT and GPL licenses.
 *  - http://www.opensource.org/licenses/mit-license.php
 *  - http://www.gnu.org/copyleft/gpl.html
 *
 * Author: Mark J Panaghiston
 * Version: 1.2.0
 * Date: 11th July 2010
 */

(function(c) {
        function k(a, b) {
            var d = function(e) {
                e = c[a][e] || [];
                return typeof e == "string" ? e.split(/,?\s+/) : e
            }("getter");
            return c.inArray(b, d) != -1
        }
        c.fn.jPlayer = function(a) {
            var b = typeof a == "string"
                , d = Array.prototype.slice.call(arguments, 1);
            if (b && a.substring(0, 1) == "_")
                return this;
            if (b && k("jPlayer", a, d)) {
                var e = c.data(this[0], "jPlayer");
                return e ? e[a].apply(e, d) : undefined
            }
            return this.each(function() {
                var h = c.data(this, "jPlayer");
                !h && !b && c.data(this, "jPlayer", new c.jPlayer(this,a))._init();
                h && b && c.isFunction(h[a]) && h[a].apply(h, d)
            })
        }
        ;
        c.jPlayer = function(a, b) {
            this.options = c.extend({}, b);
            this.element = c(a)
        }
        ;
        c.jPlayer.getter = "jPlayerOnProgressChange jPlayerOnSoundComplete jPlayerVolume jPlayerReady getData jPlayerController";
        c.jPlayer.defaults = {
            cssPrefix: "jqjp",
            swfPath: "js",
            volume: 80,
            oggSupport: false,
            nativeSupport: true,
            preload: "none",
            customCssIds: false,
            graphicsFix: true,
            errorAlerts: false,
            warningAlerts: false,
            position: "absolute",
            width: "0",
            height: "0",
            top: "0",
            left: "0",
            quality: "high",
            bgcolor: "#ffffff"
        };
        c.jPlayer._config = {
            version: "1.2.0",
            swfVersionRequired: "1.2.0",
            swfVersion: "unknown",
            jPlayerControllerId: undefined,
            delayedCommandId: undefined,
            isWaitingForPlay: false,
            isFileSet: false
        };
        c.jPlayer._diag = {
            isPlaying: false,
            src: "",
            loadPercent: 0,
            playedPercentRelative: 0,
            playedPercentAbsolute: 0,
            playedTime: 0,
            totalTime: 0
        };
        c.jPlayer._cssId = {
            play: "jplayer_play",
            pause: "jplayer_pause",
            stop: "jplayer_stop",
            loadBar: "jplayer_load_bar",
            playBar: "jplayer_play_bar",
            volumeMin: "jplayer_volume_min",
            volumeMax: "jplayer_volume_max",
            volumeBar: "jplayer_volume_bar",
            volumeBarValue: "jplayer_volume_bar_value"
        };
        c.jPlayer.count = 0;
        c.jPlayer.timeFormat = {
            showHour: false,
            showMin: true,
            showSec: true,
            padHour: false,
            padMin: true,
            padSec: true,
            sepHour: ":",
            sepMin: ":",
            sepSec: ""
        };
        c.jPlayer.convertTime = function(a) {
            var b = new Date(a)
                , d = b.getUTCHours();
            a = b.getUTCMinutes();
            b = b.getUTCSeconds();
            d = c.jPlayer.timeFormat.padHour && d < 10 ? "0" + d : d;
            a = c.jPlayer.timeFormat.padMin && a < 10 ? "0" + a : a;
            b = c.jPlayer.timeFormat.padSec && b < 10 ? "0" + b : b;
            return (c.jPlayer.timeFormat.showHour ? d + c.jPlayer.timeFormat.sepHour : "") + (c.jPlayer.timeFormat.showMin ? a + c.jPlayer.timeFormat.sepMin : "") + (c.jPlayer.timeFormat.showSec ? b + c.jPlayer.timeFormat.sepSec : "")
        }
        ;
        c.jPlayer.prototype = {
            _init: function() {
                var a = this
                    , b = this.element;
                this.config = c.extend({}, c.jPlayer.defaults, this.options, c.jPlayer._config);
                this.config.diag = c.extend({}, c.jPlayer._diag);
                this.config.cssId = {};
                this.config.cssSelector = {};
                this.config.cssDisplay = {};
                this.config.clickHandler = {};
                this.element.data("jPlayer.config", this.config);
                c.extend(this.config, {
                    id: this.element.attr("id"),
                    swf: this.config.swfPath + (this.config.swfPath != "" && this.config.swfPath.slice(-1) != "/" ? "/" : "") + "Jplayer.swf",
                    fid: this.config.cssPrefix + "_flash_" + c.jPlayer.count,
                    aid: this.config.cssPrefix + "_audio_" + c.jPlayer.count,
                    hid: this.config.cssPrefix + "_force_" + c.jPlayer.count,
                    i: c.jPlayer.count,
                    volume: this._limitValue(this.config.volume, 0, 100),
                    autobuffer: this.config.preload != "none"
                });
                c.jPlayer.count++;
                if (this.config.ready != undefined)
                    if (c.isFunction(this.config.ready))
                        this.jPlayerReadyCustom = this.config.ready;
                    else
                        this._warning("Constructor's ready option is not a function.");
                this.config.audio = document.createElement("audio");
                this.config.audio.id = this.config.aid;
                c.extend(this.config, {
                    canPlayMP3: !!(this.config.audio.canPlayType ? "" != this.config.audio.canPlayType("audio/mpeg") && "no" != this.config.audio.canPlayType("audio/mpeg") : false),
                    canPlayOGG: !!(this.config.audio.canPlayType ? "" != this.config.audio.canPlayType("audio/ogg") && "no" != this.config.audio.canPlayType("audio/ogg") : false),
                    aSel: c("#" + this.config.aid)
                });
                c.extend(this.config, {
                    html5: !!(this.config.oggSupport ? this.config.canPlayOGG ? true : this.config.canPlayMP3 : this.config.canPlayMP3)
                });
                c.extend(this.config, {
                    usingFlash: !(this.config.html5 && this.config.nativeSupport),
                    usingMP3: !(this.config.oggSupport && this.config.canPlayOGG && this.config.nativeSupport)
                });
                var d = {
                    setButtons: function(g, f) {
                        a.config.diag.isPlaying = f;
                        if (a.config.cssId.play != undefined && a.config.cssId.pause != undefined)
                            if (f) {
                                a.config.cssSelector.play.css("display", "none");
                                a.config.cssSelector.pause.css("display", a.config.cssDisplay.pause)
                            } else {
                                a.config.cssSelector.play.css("display", a.config.cssDisplay.play);
                                a.config.cssSelector.pause.css("display", "none")
                            }
                        if (f)
                            a.config.isWaitingForPlay = false
                    }
                }
                    , e = {
                    setFile: function(g, f) {
                        try {
                            a._getMovie().fl_setFile_mp3(f);
                            a.config.autobuffer && b.trigger("jPlayer.load");
                            a.config.diag.src = f;
                            a.config.isFileSet = true;
                            b.trigger("jPlayer.setButtons", false)
                        } catch (j) {
                            a._flashError(j)
                        }
                    },
                    clearFile: function() {
                        try {
                            b.trigger("jPlayer.setButtons", false);
                            a._getMovie().fl_clearFile_mp3();
                            a.config.diag.src = "";
                            a.config.isFileSet = false
                        } catch (g) {
                            a._flashError(g)
                        }
                    },
                    load: function() {
                        try {
                            a._getMovie().fl_load_mp3()
                        } catch (g) {
                            a._flashError(g)
                        }
                    },
                    play: function() {
                        try {
                            a._getMovie().fl_play_mp3() && b.trigger("jPlayer.setButtons", true)
                        } catch (g) {
                            a._flashError(g)
                        }
                    },
                    pause: function() {
                        try {
                            a._getMovie().fl_pause_mp3() && b.trigger("jPlayer.setButtons", false)
                        } catch (g) {
                            a._flashError(g)
                        }
                    },
                    stop: function() {
                        try {
                            a._getMovie().fl_stop_mp3() && b.trigger("jPlayer.setButtons", false)
                        } catch (g) {
                            a._flashError(g)
                        }
                    },
                    playHead: function(g, f) {
                        try {
                            a._getMovie().fl_play_head_mp3(f) && b.trigger("jPlayer.setButtons", true)
                        } catch (j) {
                            a._flashError(j)
                        }
                    },
                    playHeadTime: function(g, f) {
                        try {
                            a._getMovie().fl_play_head_time_mp3(f) && b.trigger("jPlayer.setButtons", true)
                        } catch (j) {
                            a._flashError(j)
                        }
                    },
                    volume: function(g, f) {
                        a.config.volume = f;
                        try {
                            a._getMovie().fl_volume_mp3(f)
                        } catch (j) {
                            a._flashError(j)
                        }
                    }
                }
                    , h = {
                    setFile: function(g, f, j) {
                        a.config.diag.src = a.config.usingMP3 ? f : j;
                        a.config.isFileSet && !a.config.isWaitingForPlay && b.trigger("jPlayer.pause");
                        a.config.audio.autobuffer = a.config.autobuffer;
                        a.config.audio.preload = a.config.preload;
                        if (a.config.autobuffer) {
                            a.config.audio.src = a.config.diag.src;
                            a.config.audio.load()
                        } else
                            a.config.isWaitingForPlay = true;
                        a.config.isFileSet = true;
                        a.jPlayerOnProgressChange(0, 0, 0, 0, 0);
                        clearInterval(a.config.jPlayerControllerId);
                        if (a.config.autobuffer)
                            a.config.jPlayerControllerId = window.setInterval(function() {
                                a.jPlayerController(false)
                            }, 100);
                        clearInterval(a.config.delayedCommandId)
                    },
                    clearFile: function() {
                        a.setFile("", "");
                        a.config.isWaitingForPlay = false;
                        a.config.isFileSet = false
                    },
                    load: function() {
                        if (a.config.isFileSet)
                            if (a.config.isWaitingForPlay) {
                                a.config.audio.autobuffer = true;
                                a.config.audio.preload = "auto";
                                a.config.audio.src = a.config.diag.src;
                                a.config.audio.load();
                                a.config.isWaitingForPlay = false;
                                clearInterval(a.config.jPlayerControllerId);
                                a.config.jPlayerControllerId = window.setInterval(function() {
                                    a.jPlayerController(false)
                                }, 100)
                            }
                    },
                    play: function() {
                        if (a.config.isFileSet) {
                            if (a.config.isWaitingForPlay) {
                                a.config.audio.src = a.config.diag.src;
                                a.config.audio.load()
                            }
                            a.config.audio.play();
                            b.trigger("jPlayer.setButtons", true);
                            clearInterval(a.config.jPlayerControllerId);
                            a.config.jPlayerControllerId = window.setInterval(function() {
                                a.jPlayerController(false)
                            }, 100);
                            clearInterval(a.config.delayedCommandId)
                        }
                    },
                    pause: function() {
                        if (a.config.isFileSet) {
                            a.config.audio.pause();
                            b.trigger("jPlayer.setButtons", false);
                            clearInterval(a.config.delayedCommandId)
                        }
                    },
                    stop: function() {
                        if (a.config.isFileSet)
                            try {
                                b.trigger("jPlayer.pause");
                                a.config.audio.currentTime = 0;
                                clearInterval(a.config.jPlayerControllerId);
                                a.config.jPlayerControllerId = window.setInterval(function() {
                                    a.jPlayerController(true)
                                }, 100)
                            } catch (g) {
                                clearInterval(a.config.delayedCommandId);
                                a.config.delayedCommandId = window.setTimeout(function() {
                                    a.stop()
                                }, 100)
                            }
                    },
                    playHead: function(g, f) {
                        if (a.config.isFileSet)
                            try {
                                b.trigger("jPlayer.load");
                                if (typeof a.config.audio.buffered == "object" && a.config.audio.buffered.length > 0)
                                    a.config.audio.currentTime = f * a.config.audio.buffered.end(a.config.audio.buffered.length - 1) / 100;
                                else if (a.config.audio.duration > 0 && !isNaN(a.config.audio.duration))
                                    a.config.audio.currentTime = f * a.config.audio.duration / 100;
                                else
                                    throw "e";
                                b.trigger("jPlayer.play")
                            } catch (j) {
                                b.trigger("jPlayer.play");
                                b.trigger("jPlayer.pause");
                                a.config.delayedCommandId = window.setTimeout(function() {
                                    a.playHead(f)
                                }, 100)
                            }
                    },
                    playHeadTime: function(g, f) {
                        if (a.config.isFileSet)
                            try {
                                b.trigger("jPlayer.load");
                                a.config.audio.currentTime = f / 1E3;
                                b.trigger("jPlayer.play")
                            } catch (j) {
                                b.trigger("jPlayer.play");
                                b.trigger("jPlayer.pause");
                                a.config.delayedCommandId = window.setTimeout(function() {
                                    a.playHeadTime(f)
                                }, 100)
                            }
                    },
                    volume: function(g, f) {
                        a.config.volume = f;
                        a.config.audio.volume = f / 100;
                        a.jPlayerVolume(f)
                    }
                };
                this.config.usingFlash ? c.extend(d, e) : c.extend(d, h);
                for (var i in d) {
                    e = "jPlayer." + i;
                    this.element.unbind(e);
                    this.element.bind(e, d[i])
                }
                if (this.config.usingFlash)
                    if (this._checkForFlash(8))
                        if (c.browser.msie) {
                            i = '<object id="' + this.config.fid + '"';
                            i += ' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"';
                            i += ' codebase="' + document.URL.substring(0, document.URL.indexOf(":")) + '://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab"';
                            i += ' type="application/x-shockwave-flash"';
                            i += ' width="' + this.config.width + '" height="' + this.config.height + '">';
                            i += "</object>";
                            d = [];
                            d[0] = '<param name="movie" value="' + this.config.swf + '" />';
                            d[1] = '<param name="quality" value="high" />';
                            d[2] = '<param name="FlashVars" value="id=' + escape(this.config.id) + "&fid=" + escape(this.config.fid) + "&vol=" + this.config.volume + '" />';
                            d[3] = '<param name="allowScriptAccess" value="always" />';
                            d[4] = '<param name="bgcolor" value="' + this.config.bgcolor + '" />';
                            i = document.createElement(i);
                            for (e = 0; e < d.length; e++)
                                i.appendChild(document.createElement(d[e]));
                            this.element.html(i)
                        } else {
                            d = '<embed name="' + this.config.fid + '" id="' + this.config.fid + '" src="' + this.config.swf + '"';
                            d += ' width="' + this.config.width + '" height="' + this.config.height + '" bgcolor="' + this.config.bgcolor + '"';
                            d += ' quality="high" FlashVars="id=' + escape(this.config.id) + "&fid=" + escape(this.config.fid) + "&vol=" + this.config.volume + '"';
                            d += ' allowScriptAccess="always"';
                            d += ' type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
                            this.element.html(d)
                        }
                    else
                        this.element.html("<p>Flash 8 or above is not installed. <a href='http://get.adobe.com/flashplayer'>Get Flash!</a></p>");
                else {
                    this.config.audio.autobuffer = this.config.autobuffer;
                    this.config.audio.preload = this.config.preload;
                    this.config.audio.addEventListener("canplay", function() {
                        var g = 0.1 * Math.random();
                        a.config.audio.volume = (a.config.volume + (a.config.volume < 50 ? g : -g)) / 100
                    }, false);
                    this.config.audio.addEventListener("ended", function() {
                        clearInterval(a.config.jPlayerControllerId);
                        a.jPlayerOnSoundComplete()
                    }, false);
                    this.element.append(this.config.audio)
                }
                this.element.css({
                    position: this.config.position,
                    top: this.config.top,
                    left: this.config.left
                });
                if (this.config.graphicsFix) {
                    this.element.append('<div id="' + this.config.hid + '"></div>');
                    c.extend(this.config, {
                        hSel: c("#" + this.config.hid)
                    });
                    this.config.hSel.css({
                        "text-indent": "-9999px"
                    })
                }
                this.config.customCssIds || c.each(c.jPlayer._cssId, function(g, f) {
                    a.cssId(g, f)
                });
                if (!this.config.usingFlash) {
                    this.element.css({
                        left: "-9999px"
                    });
                    window.setTimeout(function() {
                        a.volume(a.config.volume);
                        a.jPlayerReady()
                    }, 100)
                }
            },
            jPlayerReady: function(a) {
                if (this.config.usingFlash) {
                    this.config.swfVersion = a;
                    this.config.swfVersionRequired != this.config.swfVersion && this._error("jPlayer's JavaScript / SWF version mismatch!\n\nJavaScript requires SWF : " + this.config.swfVersionRequired + "\nThe Jplayer.swf used is : " + this.config.swfVersion)
                } else
                    this.config.swfVersion = "n/a";
                this.jPlayerReadyCustom()
            },
            jPlayerReadyCustom: function() {},
            setFile: function(a, b) {
                this.element.trigger("jPlayer.setFile", [a, b])
            },
            clearFile: function() {
                this.element.trigger("jPlayer.clearFile")
            },
            load: function() {
                this.element.trigger("jPlayer.load")
            },
            play: function() {
                this.element.trigger("jPlayer.play")
            },
            pause: function() {
                this.element.trigger("jPlayer.pause")
            },
            stop: function() {
                this.element.trigger("jPlayer.stop")
            },
            playHead: function(a) {
                this.element.trigger("jPlayer.playHead", [a])
            },
            playHeadTime: function(a) {
                this.element.trigger("jPlayer.playHeadTime", [a])
            },
            volume: function(a) {
                a = this._limitValue(a, 0, 100);
                this.element.trigger("jPlayer.volume", [a])
            },
            cssId: function(a, b) {
                var d = this;
                if (typeof b == "string")
                    if (c.jPlayer._cssId[a]) {
                        this.config.cssId[a] != undefined && this.config.cssSelector[a].unbind("click", this.config.clickHandler[a]);
                        this.config.cssId[a] = b;
                        this.config.cssSelector[a] = c("#" + b);
                        this.config.clickHandler[a] = function(h) {
                            d[a](h);
                            c(this).blur();
                            return false
                        }
                        ;
                        this.config.cssSelector[a].click(this.config.clickHandler[a]);
                        var e = this.config.cssSelector[a].css("display");
                        if (a == "play")
                            this.config.cssDisplay.pause = e;
                        if (!(a == "pause" && e == "none")) {
                            this.config.cssDisplay[a] = e;
                            a == "pause" && this.config.cssSelector[a].css("display", "none")
                        }
                    } else
                        this._warning("Unknown/Illegal function in cssId\n\njPlayer('cssId', '" + a + "', '" + b + "')");
                else
                    this._warning("cssId CSS Id must be a string\n\njPlayer('cssId', '" + a + "', " + b + ")")
            },
            loadBar: function(a) {
                if (this.config.cssId.loadBar != undefined) {
                    var b = this.config.cssSelector.loadBar.offset();
                    a = a.pageX - b.left;
                    b = this.config.cssSelector.loadBar.width();
                    this.playHead(100 * a / b)
                }
            },
            playBar: function(a) {
                this.loadBar(a)
            },
            onProgressChange: function(a) {
                if (c.isFunction(a))
                    this.onProgressChangeCustom = a;
                else
                    this._warning("onProgressChange parameter is not a function.")
            },
            onProgressChangeCustom: function() {},
            jPlayerOnProgressChange: function(a, b, d, e, h) {
                this.config.diag.loadPercent = a;
                this.config.diag.playedPercentRelative = b;
                this.config.diag.playedPercentAbsolute = d;
                this.config.diag.playedTime = e;
                this.config.diag.totalTime = h;
                this.config.cssId.loadBar != undefined && this.config.cssSelector.loadBar.width(a + "%");
                this.config.cssId.playBar != undefined && this.config.cssSelector.playBar.width(b + "%");
                this.onProgressChangeCustom(a, b, d, e, h);
                this._forceUpdate()
            },
            jPlayerController: function(a) {
                var b = 0
                    , d = 0
                    , e = 0
                    , h = 0
                    , i = 0;
                if (this.config.audio.readyState >= 1) {
                    b = this.config.audio.currentTime * 1E3;
                    d = this.config.audio.duration * 1E3;
                    d = isNaN(d) ? 0 : d;
                    e = d > 0 ? 100 * b / d : 0;
                    if (typeof this.config.audio.buffered == "object" && this.config.audio.buffered.length > 0) {
                        h = 100 * this.config.audio.buffered.end(this.config.audio.buffered.length - 1) / this.config.audio.duration;
                        i = 100 * this.config.audio.currentTime / this.config.audio.buffered.end(this.config.audio.buffered.length - 1)
                    } else {
                        h = 100;
                        i = e
                    }
                }
                !this.config.diag.isPlaying && h >= 100 && clearInterval(this.config.jPlayerControllerId);
                a ? this.jPlayerOnProgressChange(h, 0, 0, 0, d) : this.jPlayerOnProgressChange(h, i, e, b, d)
            },
            volumeMin: function() {
                this.volume(0)
            },
            volumeMax: function() {
                this.volume(100)
            },
            volumeBar: function(a) {
                if (this.config.cssId.volumeBar != undefined) {
                    var b = this.config.cssSelector.volumeBar.offset();
                    a = a.pageX - b.left;
                    b = this.config.cssSelector.volumeBar.width();
                    this.volume(100 * a / b)
                }
            },
            volumeBarValue: function(a) {
                this.volumeBar(a)
            },
            jPlayerVolume: function(a) {
                if (this.config.cssId.volumeBarValue != null) {
                    this.config.cssSelector.volumeBarValue.width(a + "%");
                    this._forceUpdate()
                }
            },
            onSoundComplete: function(a) {
                if (c.isFunction(a))
                    this.onSoundCompleteCustom = a;
                else
                    this._warning("onSoundComplete parameter is not a function.")
            },
            onSoundCompleteCustom: function() {},
            jPlayerOnSoundComplete: function() {
                this.element.trigger("jPlayer.setButtons", false);
                this.onSoundCompleteCustom()
            },
            getData: function(a) {
                for (var b = a.split("."), d = this.config, e = 0; e < b.length; e++)
                    if (d[b[e]] != undefined)
                        d = d[b[e]];
                    else {
                        this._warning("Undefined data requested.\n\njPlayer('getData', '" + a + "')");
                        return
                    }
                return d
            },
            _getMovie: function() {
                return document[this.config.fid]
            },
            _checkForFlash: function(a) {
                var b = false, d;
                if (window.ActiveXObject)
                    try {
                        new ActiveXObject("ShockwaveFlash.ShockwaveFlash." + a);
                        b = true
                    } catch (e) {}
                else if (navigator.plugins && navigator.mimeTypes.length > 0)
                    if (d = navigator.plugins["Shockwave Flash"])
                        if (navigator.plugins["Shockwave Flash"].description.replace(/.*\s(\d+\.\d+).*/, "$1") >= a)
                            b = true;
                return b
            },
            _forceUpdate: function() {
                this.config.graphicsFix && this.config.hSel.text("" + Math.random())
            },
            _limitValue: function(a, b, d) {
                return a < b ? b : a > d ? d : a
            },
            _flashError: function(a) {
                this._error("Problem with Flash component.\n\nCheck the swfPath points at the Jplayer.swf path.\n\nswfPath = " + this.config.swfPath + "\nurl: " + this.config.swf + "\n\nError: " + a.message)
            },
            _error: function(a) {
                this.config.errorAlerts && this._alert("Error!\n\n" + a)
            },
            _warning: function(a) {
                this.config.warningAlerts && this._alert("Warning!\n\n" + a)
            },
            _alert: function(a) {
                alert("jPlayer " + this.config.version + " : id='" + this.config.id + "' : " + a)
            }
        }
    }
)(jQuery);
