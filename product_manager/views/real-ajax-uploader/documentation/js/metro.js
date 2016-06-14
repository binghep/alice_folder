/*
 *  metro.js - Win8 Metro UI onclick effect [v1.0]
 *  Distributed under the Do-wathever-the-hell-you-want-with-it License
 *
 *  Web site:   http://claudiobonifazi.com
 *  Blog:       http://claudiobonifazi.com?p=4
 *  Email:      claudio.bonifazi@gmail.com
 *  Twitter:    @ClaudioBonifazi
 */
 
(function($){
    $.fn.metroClick = function( e, callback ){
 
                var el = $(this), origin = 0, ang = 10, orizorvert = 0, duration = 200, anim,
                        mouse = {x:e.pageX-el.offset().left,y:e.pageY-el.offset().top};
 
                // for a better antialiasing
                if(el.css('box-shadow')=='none')
                        el.css({'box-shadow':'0 0 1px transparent'})
 
                // needed to define how much links should tilt
                el.parent().css({'-webkit-perspective':el.outerWidth()*5})
 

 
                return $.each(el,function(i,e){
                        if( orizorvert > 0 && $.browser.webkit)
                                $(e).css({'-webkit-transform-origin':(orizorvert==1 ? origin+'% 0%' : '0% '+origin+'%')})
                                .animate({'z-index':$(e).css('z-index')},{duration:duration,step:function(now,fx){
                                        anim = ang*Math.sin((fx.pos*Math.PI))
                                        $(e).css({'-webkit-transform' : 'rotate'+(orizorvert==1 ? 'Y':'X')+'('+anim+'deg)'})
                                },queue:false}).delay(duration)
                        else if(orizorvert==0 || !$.browser.webkit)
                                $(e).animate({'z-index':$(e).css('z-index')},{duration:duration,step:function(now,fx){
                                        anim = 1-Math.sin(fx.pos*Math.PI)/10
                                        $(e).css({'-webkit-transform' : 'scale('+anim+')',
                                                '-moz-transform' : 'scale('+anim+')',
                                                '-o-transform' : 'scale('+anim+')'})
                                },queue:false}).delay(duration)
                })
        }
})(jQuery)