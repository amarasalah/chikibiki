/*
 * Open source under the BSD License.
 *
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

 ( function( $ ) {
    $( function() {
        if ( $( window ).width() <= 1024 || ( ! $( 'body' ).hasClass( 'elementor-editor-active' ) && 0 === $( '.cs-parallax-on-scroll' ).length ) ) {
            $( '.cs-parallax-on-scroll' ).css( 'background-image', '' );
        } else {
            ParallaxScroll.init();
        }
    } );

    var ParallaxScroll = {
        /* PUBLIC VARIABLES */
        showLogs: false,
        round: 1000,
        editorMode: false,
        parallaxs: $(),

        /* PUBLIC FUNCTIONS */
        init: function() {
            if ( this._inited ) {
                this._inited = true;
                return;
            }
            this._requestAnimationFrame = ( function() {
              return  window.requestAnimationFrame       ||
                      window.webkitRequestAnimationFrame ||
                      window.mozRequestAnimationFrame    ||
                      window.oRequestAnimationFrame      ||
                      window.msRequestAnimationFrame     ||
                      function ( callback, element ) {
                          window.setTimeout( callback, 1000 / 60 );
                      };
            } ) ();
            var self = this;
            this.editorMode = $('body').hasClass( 'elementor-editor-active' );
            $( 'body' ).hasClass( 'elementor-editor-active' ) ? $( 'body' ).on( 'add.loftoceanParallax', function( e, el ) {
                if ( $( el ).length && ( self.parallaxs.index( $( el ) ) < 0 ) ) {
                    var $wrap = $( el ), $remove = $(), settings = self._getBackgroundSettings( el );
                    if ( false !== settings ) {
                        $wrap.data( 'cs-parrallax-type', settings[ 'type' ] );
                        self.parallaxs = self.parallaxs.add( settings[ 'el' ] );
                    }
                    self.parallaxs.each( function() {
                        var $self = $( this );
                        if ( ( ! $self.length ) || ( ! $self.closest( '.cs-parallax-on-scroll' ).length ) ) {
                            $remove = $remove.add( $self );
                        }
                    } );
                    if ( $remove.length ) {
                        self.parallaxs = self.parallaxs.not( $remove );
                    }
                    self._onScroll( true );
                }
            } ) : this._initSettings();
            this._onScroll( true );
        },
        /* PRIVATE VARIABLES */
        _inited: false,
        _properties: [ 'y' ],
        _requestAnimationFrame: null,
        _initSettings: function() {
            var self = this;
            $( '.cs-parallax-on-scroll' ).each( function() {
                var settings = self._getBackgroundSettings( this );
                if ( false !== settings ) {
                    var $wrap = $( this );
                    $wrap.data( 'cs-parrallax-type', settings[ 'type' ] );
                    self.parallaxs = self.parallaxs.add( settings[ 'el' ] );
                }
            } );
        },
        _isClassicBackground: function( wrap ) {
            var $wrap = $( wrap ), image = false;
            if ( this.editorMode ) {
                $wrap.css( 'background-image', '' );
                if ( $wrap.css( 'background-image' ) && $wrap.css( 'background-image' ).includes( 'url(' ) ) {
                    image = $wrap.css( 'background-image' ).substr( 5 );
                    image = image.substr( 0, image.length - 2 );
                }
                $wrap.css( 'background-image', 'none' );
            } else {
                if ( $wrap.data( 'cs-background-image' ) ) {
                    image = $wrap.data( 'cs-background-image' );
                }
            }
            return image;
        },
        _getBackgroundSettings: function( wrap ) {
            var $wrap = $( wrap ), offsetY = $wrap.attr( 'class' ).match( /cs_scroll_y_(\d+)/ ), elHeight, dataStyle, styles, image;
            offsetY = offsetY && offsetY[1] ? offsetY[1] : 0;
            elHeight = parseInt( $wrap.outerHeight(), 10 ) + parseInt( offsetY * 1.2, 10 );
            dataStyle = 'height: ' + elHeight + 'px; margin-top:' + Math.min( - offsetY, offsetY ) + 'px;';
            styles = { 'height': elHeight + 'px', 'margin-top': Math.min( - offsetY, offsetY ) + 'px' };

            if ( image = this._isClassicBackground( wrap ) ) {
                var $el = false;
                if ( $wrap.find( '.parallax-img-container img' ).length ) {
                    $el = $wrap.find( '.parallax-img-container img' );
                    $el.attr( 'src', image ).parent().show();
                } else {
                    $el = $( '<img>', { 'src': image, 'alt': $wrap.data( 'cs-background-image-alt-text' ) } );
                    $( '<div>', { 'class': 'parallax-img-container' } ).append( $el ).prependTo( $wrap );
                }
                return {
                    'type': 'image',
                    'el': $el.data( 'style', dataStyle ).css( styles )
                };
            } else {
                $wrap.children( '.parallax-img-container' ).length ? $wrap.children( '.parallax-img-container' ).hide() : '';
                if ( $wrap.children( '.elementor-background-slideshow' ).length ) {
                    return {
                        'type': 'slider',
                        'el': $wrap.children( '.elementor-background-slideshow' ).data( 'style', dataStyle ).css( styles )
                    };
                } else if ( $wrap.children( '.elementor-background-video-container' ).length ) {
                    return {
                        'type': 'video',
                        'el': $wrap.children( '.elementor-background-video-container' ).data( 'style', dataStyle ).css( styles )
                    };
                }
            }
            return false;
        },
        _onScroll: function( noSmooth ) {
            var self = this, scroll = $( document ).scrollTop(), windowHeight = $( window ).height();
            this.parallaxs.each( $.proxy( function( index, el ) {
                if ( ( ! $( el ).length ) || ( ! $( el ).closest( '.cs-parallax-on-scroll' ).length ) ) return true;

                var $el = $( el ), $wrap = $el.closest( '.cs-parallax-on-scroll' ), properties = [], applyProperties = false, style = $el.data( 'style' ) || '';
                if ( self.editorMode ) {
                    var settings = self._getBackgroundSettings( $wrap );
                    if ( false === settings ) {
                        return true;
                    } else {
                        $wrap.data( 'cs-parrallax-type', settings[ 'type' ] );
                        $el = settings[ 'el' ];
                    }
                }

                var datas = [[]];
                var classes = $wrap.attr( 'class' ).split( ' ' );
                for ( var index = 0; index < classes.length; index++ ) {
                    if ( classes[ index ].indexOf( 'cs_scroll' ) >= 0 ) {
                        var data = classes[ index ].split( '_' );
                        datas[0][ data[2] ] = data[3]
                    }
                }
                var iData, datasLength = datas.length;
                for ( iData = 0; iData < datasLength; iData ++ ) {
                    var data = datas[ iData ];
                    var scrollFrom = Math.max( 0, $wrap.offset().top - windowHeight );
                    scrollFrom = scrollFrom | 0;
                    var scrollDistance = windowHeight + $wrap.outerHeight();
                    scrollDistance = Math.max( scrollDistance | 0, 1 );
                    var scrollTo = scrollFrom + scrollDistance;
                    scrollTo = scrollTo | 0;
                    var smoothness = data[ 'smoothness' ];
                    if ( smoothness == undefined ) smoothness = 30;
                    smoothness = smoothness | 0;
                    if ( noSmooth || smoothness == 0 ) smoothness = 1;
                    smoothness = smoothness | 0;
                    var scrollCurrent = scroll;
                    scrollCurrent = Math.max( scrollCurrent, scrollFrom );
                    scrollCurrent = Math.min( scrollCurrent, scrollTo );
                    this._properties.map( $.proxy( function( prop ) {
                        var defaultProp = 0;
                        var to = data[ prop ];
                        if ( to == undefined ) return;
                        to = to | 0;
                        var prev = $el.data( "_" + prop );
                        if ( prev == undefined ) prev = defaultProp;
                        var next = ( ( to - defaultProp ) * ( ( scrollCurrent - scrollFrom ) / ( scrollTo - scrollFrom ) ) ) + defaultProp;
                        var val = prev + ( next - prev ) / smoothness;
                        val = Math.ceil( val * this.round ) / this.round;
                        if ( val == prev && next == to ) val = to;
                        if ( ! properties[ prop ] ) properties[ prop ] = 0;
                        properties[ prop ] += val;
                        if ( prev != properties[ prop ] ) {
                            $el.data( "_" + prop, properties[ prop ] );
                            applyProperties = true;
                        }
                    }, this ) );
                }
                if ( applyProperties ) {
                    var translate3d = 'translate3d(0px, ' + ( properties[ "y" ] ? properties[ "y" ] : 0 ) + 'px, 0px)';
                    $el.attr( 'style', 'transform: ' + translate3d + '; -webkit-transform: ' + translate3d + '; ' + style );
                }
            }, this ) );
            if ( window.requestAnimationFrame ) {
                window.requestAnimationFrame( $.proxy( this._onScroll, this, true ) );
            } else {
                this._requestAnimationFrame( $.proxy( this._onScroll, this, true ) );
            }
        }
    };
} ) ( jQuery );
