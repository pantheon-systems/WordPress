(function( a, b ) {
    var c = "ui-state-active", d = "ui-state-hover", e = "ui-state-disabled", f = a.ui.keyCode, g = f.UP, h = f.DOWN, i = f.RIGHT, j = f.LEFT, k = f.PAGE_UP, l = f.PAGE_DOWN, m = f.HOME, n = f.END, o = a.browser.msie, p = a.browser.mozilla ? "DOMMouseScroll" : "mousewheel", q = ".uispinner", r = [g, h, i, j, k, l, m, n, f.BACKSPACE, f.DELETE, f.TAB], s;
    a.widget(
        "ui.spinner", {
            options: {
                min: null,
                max: null,
                allowNull: false,
                group: "",
                point: ".",
                prefix: "",
                suffix: "",
                places: null,
                defaultStep: 1,
                largeStep: 10,
                mouseWheel: true,
                increment: "slow",
                className: null,
                showOn: "always",
                width: 95,
                upIconClass: "ui-icon-triangle-1-n",
                downIconClass: "ui-icon-triangle-1-s",
                format: function( a, b ) {
                    var c = this, d = /(\d+)(\d{3})/, e = (isNaN( a ) ? 0 : Math.abs( a )).toFixed( b ) + "";
                    for ( e = e.replace( ".", c.point ); d.test( e ) && c.group; e = e.replace(
                        d, "$1" + c.group + "$2"
                    ) ) {
                    }
                    return (a < 0 ? "-" : "") + c.prefix + e + c.suffix
                },
                parse: function( a ) {
                    var b = this;
                    if ( b.group == "." ) a = a.replace( ".", "" );
                    if ( b.point != "." ) a = a.replace( b.point, "." );
                    return parseFloat( a.replace( /[^0-9\-\.]/g, "" ) )
                }
            }, _create: function() {
                var a = this, b = a.element, c = b.attr( "type" );
                if ( !b.is( "input" ) || c != "text" && c != "number" ) {
                    console.error( "Invalid target for ui.spinner" );
                    return
                }
                a._procOptions( true );
                a._createButtons( b );
                if ( !b.is( ":enabled" ) ) a.disable()
            }, _createButtons: function( b ) {
                function R() {
                    if ( L ) {
                        a( this ).removeClass( c );
                        p._stopSpin();
                        L = false
                    }
                    return false
                }

                function Q() {
                    if ( !t.disabled ) {
                        var b = p.element[0], d = this === C ? 1 : -1;
                        b.focus();
                        b.select();
                        a( this ).addClass( c );
                        L = true;
                        p._startSpin( d )
                    }
                    return false
                }

                function P( a ) {
                    function b() {
                        G = 0;
                        a()
                    }

                    if ( G ) {
                        if ( a === H )return;
                        clearTimeout( G )
                    }
                    H = a;
                    G = setTimeout( b, 100 )
                }

                function O( a, b ) {
                    if ( K )return false;
                    var c = String.fromCharCode( b || a ), d = p.options;
                    if ( c >= "0" && c <= "9" || c == "-" )return false;
                    if ( p.places > 0 && c == d.point || c == d.group )return false;
                    return true
                }

                function N( a ) {
                    for ( var b = 0; b < r.length; b++ )if ( r[b] == a )return true;
                    return false
                }

                function e( a ) {
                    return a == "auto" ? 0 : parseInt( a )
                }

                var p = this, t = p.options, u = t.className, v = t.width, w = t.showOn, x = a.support.boxModel, y = b.outerHeight(), z = p.oMargin = e(
                    b.css( "margin-right" )
                ), A = p.wrapper = b.wrap( '<span class="spinner-wrpr" />' ).css(
                    {
                        width: (p.oWidth = x ? b.width() : b.outerWidth()) - v,
                        marginRight: "30px",
                        marginLeft: "30px",
                        textAlign: "center",
                        "float": "none",
                        marginTop: 0
                    }
                ).after( '<span class="ui-spinner ui-widget"></span>' ).next(), B = p.btnContainer = a(
                    '<div class="ui-spinner-buttons">' + '<div class="ui-spinner-up ui-spinner-button ui-state-default ui-corner-tr"><span class="ui-icon ' + t.upIconClass + '"> </span></div>' + '<div class="ui-spinner-down ui-spinner-button ui-state-default ui-corner-br"><span class="ui-icon ' + t.downIconClass + '"> </span></div>' + "</div>"
                ), C, D, E, F, G, H, I, J, K, L, M = b[0].dir == "rtl";
                if ( u ) A.addClass( u );
                A.append( B.css( {height: y, left: 0, top: 0} ) );
                E = p.buttons = B.find( ".ui-spinner-button" );
                E.css( {width: "30px", height: y - (x ? E.outerHeight() - E.height() : 0)} );
                E.eq( 0 ).css( {right: "0"} );
                E.eq( 1 ).css( {left: "0"} );
                C = E[0];
                D = E[1];
                F = E.find( ".ui-icon" );
                B.width( "105px" );
                if ( w != "always" ) B.css( "opacity", 0 );
                if ( w == "hover" || w == "both" ) E.add( b ).bind(
                    "mouseenter" + q, function() {
                        P(
                            function() {
                                I = true;
                                if ( !p.focused || w == "hover" ) p.showButtons()
                            }
                        )
                    }
                ).bind(
                    "mouseleave" + q, function S() {
                        P(
                            function() {
                                I = false;
                                if ( !p.focused || w == "hover" ) p.hideButtons()
                            }
                        )
                    }
                );
                E.hover(
                    function() {
                        p.buttons.removeClass( d );
                        if ( !t.disabled ) a( this ).addClass( d )
                    }, function() {
                        a( this ).removeClass( d )
                    }
                ).mousedown( Q ).mouseup( R ).mouseout( R );
                if ( o ) E.dblclick(
                    function() {
                        if ( !t.disabled ) {
                            p._change();
                            p._doSpin( (this === C ? 1 : -1) * t.step )
                        }
                        return false
                    }
                ).bind(
                    "selectstart", function() {
                        return false
                    }
                );
                b.bind(
                    "keydown" + q, function( b ) {
                        var d, e, f, o = b.keyCode;
                        if ( b.ctrl || b.alt )return true;
                        if ( N( o ) ) K = true;
                        if ( J )return false;
                        switch ( o ) {
                            case g:
                            case k:
                                d = 1;
                                e = o == k;
                                break;
                            case h:
                            case l:
                                d = -1;
                                e = o == l;
                                break;
                            case i:
                            case j:
                                d = o == i ^ M ? 1 : -1;
                                break;
                            case m:
                                f = p.options.min;
                                if ( f != null ) p._setValue( f );
                                return false;
                            case n:
                                f = p.options.max;
                                f = p.options.max;
                                if ( f != null ) p._setValue( f );
                                return false
                        }
                        if ( d ) {
                            if ( !J && !t.disabled ) {
                                keyDir = d;
                                a( d > 0 ? C : D ).addClass( c );
                                J = true;
                                p._startSpin( d, e )
                            }
                            return false
                        }
                    }
                ).bind(
                    "keyup" + q, function( a ) {
                        if ( a.ctrl || a.alt )return true;
                        if ( N( f ) ) K = false;
                        switch ( a.keyCode ) {
                            case g:
                            case i:
                            case k:
                            case h:
                            case j:
                            case l:
                                E.removeClass( c );
                                p._stopSpin();
                                J = false;
                                return false
                        }
                    }
                ).bind(
                    "keypress" + q, function( a ) {
                        if ( O( a.keyCode, a.charCode ) )return false
                    }
                ).bind(
                    "change" + q, function() {
                        p._change()
                    }
                ).bind(
                    "focus" + q, function() {
                        function a() {
                            p.element.select()
                        }

                        o ? a() : setTimeout( a, 0 );
                        p.focused = true;
                        s = p;
                        if ( !I && (w == "focus" || w == "both") ) p.showButtons()
                    }
                ).bind(
                    "blur" + q, function() {
                        p.focused = false;
                        if ( !I && (w == "focus" || w == "both") ) p.hideButtons()
                    }
                )
            }, _procOptions: function( a ) {
                var b = this, c = b.element, d = b.options, e = d.min, f = d.max, g = d.step, h = d.places, i = -1, j;
                if ( d.increment == "slow" ) d.increment = [{count: 1, mult: 1, delay: 250}, {
                    count: 3,
                    mult: 1,
                    delay: 100
                }, {count: 0, mult: 1, delay: 50}]; else if ( d.increment == "fast" ) d.increment = [{
                    count: 1,
                    mult: 1,
                    delay: 250
                }, {count: 19, mult: 1, delay: 100}, {count: 80, mult: 1, delay: 20}, {
                    count: 100,
                    mult: 10,
                    delay: 20
                }, {count: 0, mult: 100, delay: 20}];
                if ( e == null && (j = c.attr( "min" )) != null ) e = parseFloat( j );
                if ( f == null && (j = c.attr( "max" )) != null ) f = parseFloat( j );
                if ( !g && (j = c.attr( "step" )) != null )if ( j != "any" ) {
                    g = parseFloat( j );
                    d.largeStep *= g
                }
                d.step = g = g || d.defaultStep;
                if ( h == null && (j = g + "").indexOf( "." ) != -1 ) h = j.length - j.indexOf( "." ) - 1;
                b.places = h;
                if ( f != null && e != null ) {
                    if ( e > f ) e = f;
                    i = Math.max( Math.max( i, d.format( f, h, c ).length ), d.format( e, h, c ).length )
                }
                if ( a ) b.inputMaxLength = c[0].maxLength;
                j = b.inputMaxLength;
                if ( j > 0 ) {
                    i = i > 0 ? Math.min( j, i ) : j;
                    j = Math.pow( 10, i ) - 1;
                    if ( f == null || f > j ) f = j;
                    j = -(j + 1) / 10 + 1;
                    if ( e == null || e < j ) e = j
                }
                if ( i > 0 ) c.attr( "maxlength", i );
                d.min = e;
                d.max = f;
                b._change();
                c.unbind( p + q );
                if ( d.mouseWheel ) c.bind( p + q, b._mouseWheel )
            }, _mouseWheel: function( b ) {
                var c = a.data( this, "spinner" );
                if ( !c.options.disabled && c.focused && s === c ) {
                    c._change();
                    c._doSpin( ((b.wheelDelta || -b.detail) > 0 ? 1 : -1) * c.options.step );
                    return false
                }
            }, _setTimer: function( a, b, c ) {
                function e() {
                    d._spin( b, c )
                }

                var d = this;
                d._stopSpin();
                d.timer = setInterval( e, a )
            }, _stopSpin: function() {
                if ( this.timer ) {
                    clearInterval( this.timer );
                    this.timer = 0
                }
            }, _startSpin: function( a, b ) {
                var c = this, d = c.options, e = d.increment;
                c._change();
                c._doSpin( a * (b ? c.options.largeStep : c.options.step) );
                if ( e && e.length > 0 ) {
                    c.counter = 0;
                    c.incCounter = 0;
                    c._setTimer( e[0].delay, a, b )
                }
            }, _spin: function( a, b ) {
                var c = this, d = c.options.increment, e = d[c.incCounter];
                c._doSpin( a * e.mult * (b ? c.options.largeStep : c.options.step) );
                c.counter++;
                if ( c.counter > e.count && c.incCounter < d.length - 1 ) {
                    c.counter = 0;
                    e = d[++c.incCounter];
                    c._setTimer( e.delay, a, b )
                }
            }, _doSpin: function( a ) {
                var b = this, c = b.curvalue;
                if ( c == null ) c = (a > 0 ? b.options.min : b.options.max) || 0;
                b._setValue( c + a )
            }, _parseValue: function() {
                var a = this.element.val();
                return a ? this.options.parse( a, this.element ) : null
            }, _validate: function( a ) {
                var b = this.options, c = b.min, d = b.max;
                if ( a == null && !b.allowNull ) a = this.curvalue != null ? this.curvalue : c || d || 0;
                if ( d != null && a > d )return d; else if ( c != null && a < c )return c; else return a
            }, _change: function() {
                var a = this, b = a._parseValue(), c = a.options.min, d = a.options.max;
                if ( !a.selfChange ) {
                    if ( isNaN( b ) ) b = a.curvalue;
                    a._setValue( b, true )
                }
            }, _setOption: function( b, c ) {
                a.Widget.prototype._setOption.call( this, b, c );
                this._procOptions()
            }, increment: function() {
                this._doSpin( this.options.step )
            }, decrement: function() {
                this._doSpin( -this.options.step )
            }, showButtons: function( a ) {
                var b = this.btnContainer.stop();
                if ( a ) b.css( "opacity", 1 ); else b.fadeTo( "fast", 1 )
            }, hideButtons: function( a ) {
                var b = this.btnContainer.stop();
                if ( a ) b.css( "opacity", 0 ); else b.fadeTo( "fast", 0 );
                this.buttons.removeClass( d )
            }, _setValue: function( a, b ) {
                var c = this;
                c.curvalue = a = c._validate( a );
                c.element.val( a != null ? c.options.format( a, c.places, c.element ) : "" );
                if ( !b ) {
                    c.selfChange = true;
                    c.element.change();
                    c.selfChange = false
                }
            }, value: function( a ) {
                if ( arguments.length ) {
                    this._setValue( a );
                    return this.element
                }
                return this.curvalue
            }, enable: function() {
                this.buttons.removeClass( e );
                this.element[0].disabled = false;
                a.Widget.prototype.enable.call( this )
            }, disable: function() {
                this.buttons.addClass( e ).removeClass( d );
                this.element[0].disabled = true;
                a.Widget.prototype.disable.call( this )
            }, destroy: function( b ) {
                this.wrapper.remove();
                this.element.unbind( q ).css( {width: this.oWidth, marginRight: this.oMargin} );
                a.Widget.prototype.destroy.call( this )
            }
        }
    )
})( jQuery )
