/**
 * jQuery Plugin CubeSlider 1.3.1
 * http://www.albanx.com/
 *
 * Copyright 2013, www.albanx.com
 *
 * Date: 15-01-2013
 */

(function($, undefined ){

	'use strict';	
	/****************
	 * From jquery site, test css3 proprety support
	 */
	function styleSupport( prop ) 
	{
	    var vendorProp, supportedProp,
	        // capitalize first character of the prop to test vendor prefix
	        capProp = prop.charAt(0).toUpperCase() + prop.slice(1),
	        prefixes = [ "Moz", "Webkit", "O", "ms" ],
	        div = document.createElement( "div" );

	    if ( prop in div.style ) {
	      // browser supports standard CSS property name
	      supportedProp = prop;
	    } else {
	      // otherwise test support for vendor-prefixed property names
	      for ( var i = 0; i < prefixes.length; i++ ) {
	        vendorProp = prefixes[i] + capProp;
	        if ( vendorProp in div.style ) {
	          supportedProp = vendorProp;
	          break;
	        }
	      }
	    }

	    // avoid memory leak in IE
	    div = null;
	    
	    // add property to $.support so it can be accessed elsewhere
	    $.support[ prop ] = supportedProp;
	    
	    return supportedProp;
	}
	
	function whichTransitionEvent(){
	    var t;
	    var el = document.createElement('div');
	    var transitions = {
	      'transition':'transitionend',
	      'OTransition':'oTransitionEnd',
	      'MSTransition':'msTransitionEnd',
	      'MozTransition':'transitionend',
	      'WebkitTransition':'webkitTransitionEnd'
	    };

	    for(t in transitions){
	        if( el.style[t] !== undefined ){
	        	el = null;
	            return transitions[t];
	        }
	    }
	    el = null;
	    return false;
	}
	
	
	/**
	 * Cube Slider class
	 */
	var CubeSlider = function($this, settings)
	{
		this.mode3d		= (styleSupport('Perspective')!==undefined && settings.mode3d==='auto');
		this.slider		= $this;
		this.elements	= $this.find('img');//images to load
		this.items		= $this.children();//mix maybe <a>
		this.settings 	= settings;
		
		//set position absolute to elements
		$this.children().css({position:'absolute', top:'0px', left:'0px' });
		
		//display a loader div during image load
		$this.append('<div class="cs-loader" />').addClass('cs-slider').css({overflow:'hidden', width:'100%', 'height':'auto'});
		
		//wait image loads then exec create
		this.imageLoad();
	};
	
	
	CubeSlider.prototype = {
		init:function()
		{
			//remove loader div
			this.slider.css('overflow', 'visible').children('.cs-loader').remove();
					
			var width 	= this.elements.get(0).width;//get the size of one of images for the size of the stage
			var height	= this.elements.get(0).height;
			
			var resW = this.slider.width();
			if( width > resW )
			{
				var rt 	= width/height;
				width 	= resW;
				height 	= width/rt;
			}
			
			this.elements.css({width:width, height:height});
			this.slider.css({ width:width, height:height });//adapt stage to image size
			this.width = width;
			this.height = height;
			
			$(window).resize(this, function(e){
				//console.log('resize');
				//e.data.box.container.remove();
				//e.data.box = new Cubes(e.data.slider, e.data.settings, e.data.elements, e.data.items, e.data.width, e.data.height);
			});
			
			//if 3d is supported start it
			if (this.mode3d)
			{
				this.box = new Cubes(this.slider, this.settings, this.elements, this.items, width, height);

			}
			else
			{
				//make a standard slider for old browsers
				this.box = new Box(this.settings, this.elements, this.slider);
			}
		},
		imageLoad: function(){
			var CS 			= this;
			var imgs 		= CS.elements;
			var settings 	= CS.settings;
			for(var i=0; i<imgs.length; i++)
			{
				var img = imgs[i];
				if(img.width === undefined || ( img.complete !== undefined && !img.complete ))
				{
					setTimeout(function(){ CS.imageLoad(); }, 50);
					return false;
				}
			}
			CS.init();
		}
	};
	
	
	var Cubes = function($this, settings, elements, items, width, height) 
	{
		this.width 		= width;
		this.height		= height;
		this.settings	= settings;
		this.wrapper 	= $this;
		this.elements 	= elements;
		this.items		= items;
		this.cubes 		= [];
		this.animating 	= false;
		this.numItems 	= elements.length;
		this.currItem 	= 0;
		this.info 		= false;
		this.orientation= this.settings.orientation;
		
		var backFace	= styleSupport('backfaceVisibility');
		var transDur 	= styleSupport('transitionDuration');
		var transOri 	= styleSupport('transformOrigin');
		var transSty	= styleSupport('transformStyle');
		var perspect	= styleSupport('perspective');
		var transFor	= styleSupport('transform');
		var transEnd	= whichTransitionEvent();
		
		
		this.css3Props	= {perspect:perspect, backFace:backFace, transDur:transDur, transOri:transOri, transEnd:transEnd, transFor:transFor, 
							transSty:transSty};		
		
		//container of the images
		this.container = $('<div/>').css(perspect, settings.perspective).css({width:width, height: height, 'z-index':10, position:'relative'}).appendTo($this);
		
		items.css({width:width, height:height, 'z-index':1000}).appendTo(this.container);
		
		$('<div class="cs-shadow"/>').appendTo($this);//Add shadow

		// add navigation arrows
		this.prevNav = $('<span class="cs-nav-prev">Previous Slide</span>');
		this.nextNav = $('<span class="cs-nav-next">Next Slide</span>');
		var arrWrapper = $('<div class="cs-nav">').append(this.prevNav).append(this.nextNav).appendTo(this.wrapper);//navigation container
		if(settings.arrows !== true) arrWrapper.hide();
		
		
		//play button
		this.playButton = $('<span class="cs-nav-play">Autoplay</span>');
		var optWrapper = $('<div class="cs-options">').append(this.playButton).appendTo(this.wrapper);//info container
		if(settings.play !== true) optWrapper.hide();
		
		
		//nav buttons
		var navs = '';
		this.navContainer = $('<div class="cs-nav-cont" />').appendTo(this.wrapper);
		for(var i=0; i<this.numItems; i++)
		{
			navs+='<span></span>';
		}
		this.navContainer.append(navs).children(':first').addClass('current');
		if(settings.navigation !== true) this.navContainer.hide();
		
		//bind events to buttons
		this.bindEvents();

		//create cubes and slice them		
		if( typeof(settings.cubesNum)=='object' )
		{
			this.rows = settings.cubesNum['rows'];
			this.cols = settings.cubesNum['cols'];
		}
		else
		{
			if(this.orientation=='h')
			{
				this.rows = settings.cubesNum;
				this.cols = 1;
			}
			else
			{
				this.rows = 1;
				this.cols = settings.cubesNum;
			}
		}

		
		this.rows = this.rows % 2 === 0?this.rows++:this.rows;
		this.cols = this.cols % 2 === 0?this.cols++:this.cols;
				 
		//this.wrapper.css({ width:this.width, height:this.height });
		this.container.css({ width:this.width, height:this.height	});
		
		for(var i=0; i<this.rows;i++)
		{
			for(var j=0;j<this.cols;j++)
			{
				var cube = new Cube(this, i, j);
				var faces = cube.createCube(this.elements, settings);
				this.cubes.push(cube);
				faces.appendTo(this.container);
			}
		}
			
		this.toggleTitle(true);
		
		//activate autoplay
		if (settings.autoplay) 
		{
			this.autoPlaying = true;
			this.autoPlay();
			this.playButton.removeClass('cs-nav-play');
		}
	};
	
	//set size and responsive function
	Cubes.prototype.refresh = function()
	{
		
	};

	//bind all click events
	Cubes.prototype.bindEvents = function()
	{
		var settings = this.settings;
		
		//navigation click
		this.nextNav.bind('click.cs', this, function(e) {
			e.data.navigate(1);
		});
		
		this.prevNav.bind('click.cs', this, function(e) {
			e.data.navigate(-1);
		});
		
		//play pause button
		this.playButton.bind('click.cs', this, function(e) {
			var cb = e.data;
			if (!cb.autoPlaying) 
			{
				if (cb.animating) return false;
				cb.autoPlaying = true;
				cb.autoPlay();
				cb.playButton.removeClass('cs-nav-play');
			} 
			else 
			{
				cb.stopAutoPlay();
			}
		});
		
		//navigator
		this.navContainer.children('span').bind('click.cs', this, function(e){
			var cb = e.data;
			if (cb.animating)	return false;
			
			var nextItem = cb.navContainer.children('span').index(this);
			
			var navdir = (nextItem-cb.currItem)>0?1:-1;
			cb.currItem = nextItem-navdir;
			cb.navigate(navdir);

		});
	};
	
	Cubes.prototype.toggleTitle = function(toggle)
	{
		if (this.animating)	return false;
		if(toggle)
		{
			var title = this.elements.eq(this.currItem).attr('title');
			if(title === undefined) return false;
			
			var tit = $('<div class="cs-title"><span>' + title + '</span></div>').appendTo(this.wrapper);
			var tit_h = tit.find('span').height();
			tit.stop().animate({height : tit_h,bottom : '0px'}, this.settings.titleSpeed, this.settings.titleEasing);
			this.info = true;
		}
		else
		{
			this.wrapper.find('div.cs-title').remove();
			this.info = false;
		}
	};
					
	Cubes.prototype.navigate = function(dir) 
	{
		if (this.animating)	return false;//do not allow change until animation finish
		
		this.stopAutoPlay();//stop autoplay
		
		//hide info
		if (this.info)	this.toggleTitle(false);

		this.animating = true;
		
		//animate and show image
		var me = this;
		this.rotateCubes(dir, function() { me.toggleTitle(true); } );
		return true;
	};
	
	Cubes.prototype.rotateCubes = function(dir, callback) 
	{
		var me = this;
		me.container.css(me.css3Props.perspect, me.settings.perspective);
		me.items.hide();
		//get the next image to show
		me.currItem = me.currItem + dir;
		if(me.currItem>me.numItems-1) 	me.currItem = 0;//limits ovios
		if(me.currItem<0) 				me.currItem = me.numItems-1;
		
		this.navContainer.children('span').removeClass('current').eq(me.currItem).addClass('current');
		
		//rotate animate each cube peices
		for ( var i = 0; i< me.cubes.length; i++)
		{
			var cubeobj = me.cubes[i];
			cubeobj.rotate(dir, i, me.settings, me.elements, me.currItem, function(i) {
				if (i === me.cubes.length - 1)
				{
					me.animating = false;
					me.container.css(me.css3Props.perspect, '');
					if (callback) callback.call();
					$(me.items[me.currItem]).show();
				}
			});
		}
	};
	
	Cubes.prototype.autoPlay = function()
	{
		if (!this.autoPlaying)	return false;

		clearTimeout(this.slideshowT);
		
		//first start
		var me = this;
		this.slideshowT = setTimeout(function() {//TODO add loader bar timer
		
			if (me.info)	me.toggleTitle(false);
			me.animating = true;
			me.rotateCubes(1, function() {
				me.toggleTitle(true);
				me.autoPlay();
			});
			
		}, me.settings.autoplayInterval);
	};
	
	Cubes.prototype.stopAutoPlay = function() {
		this.autoPlaying = false;
		clearTimeout(this.slideshowT);
		this.playButton.addClass('cs-nav-play');
	};
	
	//single Cube and his faces
	var Cube = function(cubeset, r, c) 
	{
		this.css3Props 		= cubeset.css3Props;
		this.orientation 	= cubeset.settings.orientation;	
		this.bugFixer		= cubeset.bugFixer;
		this.container		= cubeset.container;
		
		var cubew = Math.floor(cubeset.width / cubeset.cols);
		var cubeh = Math.floor(cubeset.height / cubeset.rows);
		
		this.y = cubeh * r;
		this.x = cubew * c;
		
		//full fill the last width of cube
		this.width = (cubeset.cols - 1 == c) ? cubeset.width-(c*cubew) : cubew;
		this.height = (cubeset.rows - 1 == r) ? cubeset.height-(r*cubeh) : cubeh;
			
		this.oWidth = cubeset.width;
		this.oHeight= cubeset.height;
		
		//set cube position in row and columns
		this.row = r;
		this.col = c;
		
		this.rows = cubeset.rows;
		this.cols = cubeset.cols;
		this.face = 1;
		this.setCubeStyle(cubeset.settings.backfacesColor);
	};
	
	Cube.prototype.setCubeStyle = function(backfacesColor)
	{
		// style for the slice
		var halfw = this.width/2;
		var halfh = this.height/2;
		var rotateAx = 'X';
		var hw = halfh, rotZ = 'rotateZ( 180deg )', top=0, left=0, hw2=this.width;
		if (this.orientation === 'v') 
		{
			hw = halfh;
			left= (halfw-halfh)+'px';
			rotateAx = 'X';
			hw2=this.height;
		}
		else
		{
			hw = halfw;
			rotZ = '';
			top = (halfh - halfw)+ 'px';
			rotateAx = 'Y';
			hw2=this.width;
		}
		
		//set cubes faces styles
		this.facesStyles = 
		{
			frontFace : {
				css:{
					'width' : this.width + 'px',
					'height' : this.height + 'px',
					'background-color' : backfacesColor
				},
				transform : 'rotate3d( 0, 1, 0, 0deg ) translate3d( 0, 0, '+hw+ 'px )'
			},			
			backFace : {
				css:{
					'width' : this.width + 'px',
					'height' : this.height + 'px',
					'background-color' : backfacesColor
				},
				transform : 'rotate3d( 0, 1, 0, 180deg ) translate3d( 0, 0, '+hw+ 'px ) '+rotZ
			},
			rightFace : {
				css:{
					'width' : hw2 + 'px',
					'height' : this.height + 'px',
					'left' : left,
					'background-color' : backfacesColor
				},
				transform : 'rotate3d( 0, 1, 0, 90deg ) translate3d( 0, 0, '+halfw+ 'px )'
			},
			leftFace : {
				css:{
					'width' : hw2 + 'px',
					'height' : this.height + 'px',
					'left' : left,
					'background-color' : backfacesColor
				},
				transform : 'rotate3d( 0, 1, 0, -90deg ) translate3d( 0, 0, '+halfw+ 'px )'
			},
			topFace : {
				css:{
					'width' : this.width + 'px',
					'height' : hw2 + 'px',
					'background-color' : backfacesColor,
					'top':top
				},
				transform : 'rotate3d( 1, 0, 0, 90deg ) translate3d( 0, 0, '+halfh+ 'px )'
			},
			bottomFace : {
				css:{
					'width' : this.width + 'px',
					'height' : hw2 + 'px',
					'background-color' :backfacesColor,
					'top':top
				},
				transform : 'rotate3d( 1, 0, 0, -90deg ) translate3d( 0, 0, '+halfh+'px )'
			}
		};			

		//face of cube to show, we show always 4 of 6 faces of the cube 
		this.showFace = [
			'translateZ(-'+hw+'px )',
			'translateZ(-'+hw+'px ) rotate'+rotateAx+'(-90deg )',
			'translateZ(-'+hw+'px ) rotate'+rotateAx+'(-180deg )',
			'translateZ(-'+hw+'px ) rotate'+rotateAx+'(-270deg )'
		];
	};
	
	
	Cube.prototype.createCube = function($imgs, settings)
	{
		//create cube faces
		var transform 	= this.css3Props.transFor;
		var trstyle 	= this.css3Props.transSty;		
		var bfv			= this.css3Props.backFace; //styleSupport('backfaceVisibility');
		var faceStyles 	= this.facesStyles;
		
		var faces = $('<div/>').css({ width:this.width+'px', height:this.height +'px', position: 'absolute' })
			.css(trstyle, 'preserve-3d')
			.css(bfv, 'hidden')
			.css(transform, this.showFace[0])
			.append( $('<div/>').addClass('cs-side').css(faceStyles.frontFace.css).css(transform, faceStyles.frontFace.transform) )
			.append( $('<div/>').addClass('cs-side').css(faceStyles.backFace.css).css(transform, faceStyles.backFace.transform))
			.append( $('<div/>').addClass('cs-side').css(faceStyles.rightFace.css).css(transform, faceStyles.rightFace.transform))
			.append( $('<div/>').addClass('cs-side').css(faceStyles.leftFace.css).css(transform, faceStyles.leftFace.transform))
			.append( $('<div/>').addClass('cs-side').css(faceStyles.topFace.css).css(transform, faceStyles.topFace.transform))
			.append( $('<div/>').addClass('cs-side').css(faceStyles.bottomFace.css).css(transform, faceStyles.bottomFace.transform))
			.addClass('cs-transform-all');
		faces.children('div').css('background-size', this.oWidth+'px, '+this.oHeight+'px');
		this.faces = faces;
		this.changeImage(0, $imgs);
		var half_r = Math.ceil(this.rows / 2);
		var half_c = Math.ceil(this.cols / 2);

		var r_z = (this.row < half_r)? (this.row + 1) * 100 : (this.rows - this.row) * 100;
		var c_z = (this.col < half_c)? (this.col + 1) * 100 : (this.cols - this.col) * 100;
			
		faces.css({ zIndex:r_z+c_z, top:this.y, left:this.x });
		this.spreadPixel = settings.spreadPixel*((this.col+this.row + 2) - half_c-half_r);
		
		return faces;
	};
	
	Cube.prototype.changeImage = function(imgPos, $imgs){
		var face;
		switch (this.face) 
		{
			case 1:
				face = 0;
				break;
			case 2:
				face = (this.orientation === 'h')? 2 :4;
				break;
			case 3:
				face = 1;
				break;
			case 4:
				face = (this.orientation === 'h')?3:5;
				break;
		}

		var imgCss = {};
		imgCss.backgroundImage = 'url('+ $imgs.eq(imgPos).attr('src') + ')';//set the image to show
		
		//set the position of image in the cube
		imgCss.backgroundPosition = '-'+this.x + 'px -' + this.y + 'px';
		
		//set the image to the face to show
		this.faces.children().eq(face).css(imgCss);
	};
	
	Cube.prototype.rotate = function(dir, i, options, $imgs, imgCurrent, callback) 
	{
		var currCube 	= this;
		var seq 		= options.cubeSync*i;
		var css3Props 	= this.css3Props;

		setTimeout(function(){
			//calculate the fine to show on rotate
			var face2show = currCube.face + dir;
			if(face2show > 4) face2show = 1;
			if(face2show < 1) face2show = 4;
			currCube.face 	= face2show;//store current face shown
			
			//get the css style animation of the face
			var transformCss 	= currCube.showFace[face2show-1];
			
			var tr1 = '';
			var tr2 = '';
			
			//switch the image
			currCube.changeImage(imgCurrent, $imgs);

			var startMove = {}, endMove = {};
			
			if (currCube.orientation === 'v') 
			{
				startMove.left 	= '+=' + currCube.spreadPixel + 'px';
				endMove.left 	= '-=' + currCube.spreadPixel + 'px';
			} 
			else if (currCube.orientation === 'h') 
			{
				startMove.top 	= '+=' + currCube.spreadPixel + 'px';
				endMove.top 	= '-=' + currCube.spreadPixel + 'px';
			}

			//run animation
			currCube.faces.css(css3Props.transDur, options.animationSpeed+'ms').animate(startMove, options.animationSpeed / 2 - 50).animate(endMove, options.animationSpeed / 2 - 50, function() {
				if (callback)
					callback.call(this, i);
			}).css(css3Props.transFor, transformCss);

		}, seq);
	};


	

	/**
	 * ********************************* Box Fallback no css3 support
	 * *******************************************************
	 */

	var Box = function(options, elements, container) {
		this.width 		= elements.get(0).width;
		this.height		= elements.get(0).height;

		this.animating 	= false;
		this.$images 	= elements;
		this.numItems 	= elements.length;
		this.currItem = 0;
		this.orientation = options.orientation;
		this.wrapper = container;
		this.info = false;
		this.settings = options;
		this.createBox(options);
		var instance = this;

		elements.each(function(i) {
			var $img = $(this);

			if (i === 0) 
			{
				$img.css({left : '0px',top : '0px'});
			}
			else 
			{
				if (options.orientation === 'h')
					$img.css({left: instance.width + 'px',	top:0});
				else
					$img.css({left:0, top:-instance.height + 'px'});
			}
		});

		//activate autoplay
		if (options.autoplay) 
		{
			this.autoPlaying = true;
			this.autoPlay();
			this.playButton.removeClass('cs-nav-play');
		}
	};

	Box.prototype = 
	{
		createBox : function(options) 
		{
			var boxStyle = 
			{
				'width' : this.width + 'px',
				'height' : this.height + 'px',
				'z-index' : 10,
				'position' : 'relative',
				'overflow' : 'hidden'
			};

			this.$box = $('<div>').css(boxStyle).appendTo(this.wrapper.css({width : boxStyle.width,height : boxStyle.height})
						.addClass('cs-slider-fb')).append(this.$images.show());

			// add navigation and options buttons
			this.prevNav = $('<span class="cs-nav-prev">Previous Slide</span>');
			this.nextNav = $('<span class="cs-nav-next">Next Slide</span>');
			var arrWrapper = $('<div class="cs-nav">').appendTo(this.wrapper).append(this.prevNav).append(this.nextNav);
			if(options.arrows!==true) arrWrapper.hide();
			
			
			this.playButton = $('<span class="cs-nav-play">Autoplay</span>');
			var optWrapper = $('<div class="cs-options">').appendTo(this.wrapper).append(this.playButton);
			if(options.play!==true) optWrapper.hide();
			
			$('<div class="cs-shadow"/>').appendTo(this.wrapper);
			
			
			//nav buttons
			var navs = '';
			this.navContainer = $('<div class="cs-nav-cont" />').appendTo(this.wrapper);
			for(var i=0; i<this.numItems; i++){
				navs+='<span></span>';
			}
			this.navContainer.append(navs).children(':first').addClass('current');
			if(options.navigation!==true) this.navContainer.hide();
			
			this.bindEvents(options);
			this.toggleTitle(true);
		},
		bindEvents : function(options) 
		{
			this.nextNav.bind('click.cs', this, function(e) {
				e.data.navigate(1, options);
			});
			
			this.prevNav.bind('click.cs', this, function(e) {
				e.data.navigate(-1, options);
			});
			
			this.playButton.bind('click.cs', this, function(e) {
				var me = e.data;
				if (!me.isSlideshowActive) 
				{
					if (me.animating) 	return false;
					me.isSlideshowActive = true;
					me.autoPlay(options, true);
					me.playButton.addClass('rb-nav-pause').removeClass('cs-nav-play');
				} 
				else 
				{
					me.stopAutoPlay();
				}
			});

			this.navContainer.children('span').bind('click.cs', this, function(e){
				var cb = e.data;
				if (cb.animating)	return false;
				
				var nextItem = cb.navContainer.children('span').index(this);
				
				var navdir = (nextItem-cb.currItem)>0?1:-1;
				cb.navigate(navdir, options, nextItem);
				
			});
		},
		toggleTitle : function(bool) 
		{
			if (this.animating)	return false;

			if(bool)
			{
				var title = this.$images.eq(this.currItem).attr('title');
				if(title ===undefined) return false;
				var tit = $('<div class="cs-title"><span>' + title + '</span></div>').appendTo(this.wrapper);
				var tit_h = tit.find('span').height();
				tit.stop().animate({height : tit_h,bottom : '0px'}, this.settings.titleSpeed, this.settings.titleEasing);
				this.info = true;
			}
			else
			{
				this.wrapper.find('div.cs-title').remove();
				this.info = false;
			}
		},
		navigate : function(dir, options, nextItem) 
		{
			var instance = this;
			if (instance.animating)	return false;

			instance.stopAutoPlay();

			if (instance.info) instance.toggleTitle(false);

			instance.animating = true;

			this.slide(dir, options, function(){
				instance.toggleTitle(true);
			}, nextItem);
		},
		
		slide : function(dir, options, callback, nextItem) 
		{
			var instance = this, $current = instance.$images.eq(instance.currItem);

			//get the next image to show
			instance.currItem = instance.currItem + dir;
			if(instance.currItem>instance.numItems-1) 	instance.currItem = 0;//limits ovios
			if(instance.currItem<0) 					instance.currItem = instance.numItems-1;
			
			if(nextItem!==undefined) instance.currItem=nextItem;

			var animParamOut = {}, animParamIn = {};

			if (options.orientation === 'v') 
			{
				animParamOut.top = dir*instance.height+ 'px';
				animParamIn.top = '0px';
			} 
			else if (options.orientation === 'h') 
			{
				animParamOut.left = -dir*instance.width	+ 'px';
				animParamIn.left = '0px';
			}

			$current.stop().animate(animParamOut, options.speed,options.fallbackEasing);
			var $next = instance.$images.eq(instance.currItem);
			if (dir == 1) 
			{
				if (options.orientation === 'v')
					$next.css('top', -instance.height + 'px');
				else if (options.orientation === 'h')
					$next.css('left', instance.width + 'px');
			}
			else 
			{
				if (options.orientation === 'v')
					$next.css('top', instance.height + 'px');
				else if (options.orientation === 'h')
					$next.css('left', -instance.width + 'px');
			}

			this.navContainer.children('span').removeClass('current').eq(this.currItem).addClass('current');
			
			instance.$images.eq(instance.currItem).stop().animate(animParamIn, options.speed, options.fallbackEasing, function() {
				instance.animating = false;
				if (callback)	callback.call();
			});
		},
		autoPlay : function() 
		{
			if (!this.autoPlaying)	return false;
			clearTimeout(this.slideshowT);		
			//first start
			var me = this;
			this.slideshowT = setTimeout(function() {
			
				if (me.info)	me.toggleTitle(false);
				me.animating = true;
				me.slide(1, me.settings, function() {
					me.toggleTitle(true);
					me.autoPlay(me.settings);
				});
				
			}, this.settings.autoplayInterval);
		},
		stopAutoPlay : function() 
		{
			this.isSlideshowActive = false;
			clearTimeout(this.slideshowT);
			this.playButton.addClass('cs-nav-play').removeClass('rb-nav-pause');
		}
	};
	
	
	
	/**
	 * Method to set/get options live
	 * @param opt the option to change or get
	 * @param val if is setted then change option to this val, if it is not given than get option value
	 * @returns option value or null
	 */
	CubeSlider.prototype.options = function(opt, val){
		if(val!==undefined && val!==null)
		{
			this.settings[opt] = val;
			if(opt == 'enable')
			{
				this.enable(val);
			}
		}
		else
		{
			return this.settings[opt];
		}
	};
	
	CubeSlider.prototype.enable = function(bool){
		this.settings.enable= bool;
		if(bool)
		{
		}
		else
		{
		}
	};
	
    var globalSettings = 
    {
		orientation: 		'v', 				// set cubes animation orentation v vertical, h horizontal
		perspective: 		1200, 				// 3d perspective value for supported browsers.
		cubesNum: 			{rows:1, cols:1}, 	// set the number of cubes to divide the image
		spreadPixel: 		0, 					// each cube will move x pixels left and top when rotating
		backfacesColor: 	'#222', 			// set the color of backfaces of the cube
		cubeSync: 			0,					// set the cube syncronization in animation, if 0 all will move at same time
		animationSpeed: 	800,	 			// set the animation speed of the single cubes
		fallbackEasing: 	'easeOutExpo', 		// fallback easing for non css3 browsers
		autoplay : 			false, 				// if true the box will be rotating automatically.
		autoplayInterval : 	2000,				// switch image interval in autoplay
		mode3d:				'auto',				// if auto then 3d mode will be used if supported, if false then fallback will be used
		arrows:				true,				// if true shows the left, right arrows
		navigation:			true,				// if true show the navigation bullets
		play:				true,				// if true show the play/pause button
		titleSpeed:			300,				// the animate show speed of the title in ms
		titleEasing:		'easeOutExpo'		//the title easing animation
    };
    
	var methods =
	{
		init : function(options)
		{
    	    return this.each(function() 
    	    {
				var settings = $.extend({}, globalSettings, options);						
				//for avoiding two times call errors
				var $this = $(this);
				if( $this.data('CS')!==undefined )
				{
					return;
				}
				
				$this.data('author','http://www.albanx.com/');
				
				//create the plugin object ad keep reference to it in the data CS variable
				$this.data('CS', new CubeSlider($this, settings));
    	    });
		},
		enable:function()
		{
			return this.each(function()
			{
				var $this = $(this);
				var CS = $this.data('CS');
				CS.enable(true);
			});
		},
		disable:function()
		{
			return this.each(function()
			{
				var $this = $(this);
				var CS = $this.data('CS');
				CS.enable(false);
			});
		},
		destroy : function()
		{
			return this.each(function()
			{
				var $this = $(this);
				var CS = $this.data('CS');//get ajax uploader object
				$this.removeData('CS');//remove object and empty element
			});
		},
		option : function(option, value)
		{
			return this.each(function(){
				var $this=$(this);
				var CS = $this.data('CS');
				return CS.options(option, value);
			});
		}
	};
		
	$.fn.cubeslider = function(method, options)
	{
		if(methods[method])
		{
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if(typeof method === 'object' || !method)
		{
			return methods.init.apply(this, arguments);
		}
		else
		{
			$.error('Method ' + method + ' does not exist on jQuery.CubeSlider');
		}
	};

	
	/*
	 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
	 *
	 * Uses the built in easing capabilities added In jQuery 1.1
	 * to offer multiple easing options
	 *
	 * TERMS OF USE - jQuery Easing
	 * 
	 * Open source under the BSD License. 
	 * 
	 * Copyright © 2008 George McGinley Smith
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

	// t: current time, b: begInnIng value, c: change In value, d: duration
	$.easing['jswing'] = $.easing['swing'];

	$.extend( $.easing,
	{
		def: 'easeOutQuad',
		swing: function (x, t, b, c, d) {
			//alert($.easing.default);
			return $.easing[$.easing.def](x, t, b, c, d);
		},
		easeInQuad: function (x, t, b, c, d) {
			return c*(t/=d)*t + b;
		},
		easeOutQuad: function (x, t, b, c, d) {
			return -c *(t/=d)*(t-2) + b;
		},
		easeInOutQuad: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t + b;
			return -c/2 * ((--t)*(t-2) - 1) + b;
		},
		easeInCubic: function (x, t, b, c, d) {
			return c*(t/=d)*t*t + b;
		},
		easeOutCubic: function (x, t, b, c, d) {
			return c*((t=t/d-1)*t*t + 1) + b;
		},
		easeInOutCubic: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t*t + b;
			return c/2*((t-=2)*t*t + 2) + b;
		},
		easeInQuart: function (x, t, b, c, d) {
			return c*(t/=d)*t*t*t + b;
		},
		easeOutQuart: function (x, t, b, c, d) {
			return -c * ((t=t/d-1)*t*t*t - 1) + b;
		},
		easeInOutQuart: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
			return -c/2 * ((t-=2)*t*t*t - 2) + b;
		},
		easeInQuint: function (x, t, b, c, d) {
			return c*(t/=d)*t*t*t*t + b;
		},
		easeOutQuint: function (x, t, b, c, d) {
			return c*((t=t/d-1)*t*t*t*t + 1) + b;
		},
		easeInOutQuint: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
			return c/2*((t-=2)*t*t*t*t + 2) + b;
		},
		easeInSine: function (x, t, b, c, d) {
			return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
		},
		easeOutSine: function (x, t, b, c, d) {
			return c * Math.sin(t/d * (Math.PI/2)) + b;
		},
		easeInOutSine: function (x, t, b, c, d) {
			return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
		},
		easeInExpo: function (x, t, b, c, d) {
			return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
		},
		easeOutExpo: function (x, t, b, c, d) {
			return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
		},
		easeInOutExpo: function (x, t, b, c, d) {
			if (t==0) return b;
			if (t==d) return b+c;
			if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
			return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
		},
		easeInCirc: function (x, t, b, c, d) {
			return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
		},
		easeOutCirc: function (x, t, b, c, d) {
			return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
		},
		easeInOutCirc: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
			return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
		},
		easeInElastic: function (x, t, b, c, d) {
			var s=1.70158;var p=0;var a=c;
			if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
			if (a < Math.abs(c)) { a=c; var s=p/4; }
			else var s = p/(2*Math.PI) * Math.asin (c/a);
			return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		},
		easeOutElastic: function (x, t, b, c, d) {
			var s=1.70158;var p=0;var a=c;
			if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
			if (a < Math.abs(c)) { a=c; var s=p/4; }
			else var s = p/(2*Math.PI) * Math.asin (c/a);
			return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
		},
		easeInOutElastic: function (x, t, b, c, d) {
			var s=1.70158;var p=0;var a=c;
			if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
			if (a < Math.abs(c)) { a=c; var s=p/4; }
			else var s = p/(2*Math.PI) * Math.asin (c/a);
			if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
			return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
		},
		easeInBack: function (x, t, b, c, d, s) {
			if (s == undefined) s = 1.70158;
			return c*(t/=d)*t*((s+1)*t - s) + b;
		},
		easeOutBack: function (x, t, b, c, d, s) {
			if (s == undefined) s = 1.70158;
			return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
		},
		easeInOutBack: function (x, t, b, c, d, s) {
			if (s == undefined) s = 1.70158; 
			if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
			return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
		},
		easeInBounce: function (x, t, b, c, d) {
			return c - $.easing.easeOutBounce (x, d-t, 0, c, d) + b;
		},
		easeOutBounce: function (x, t, b, c, d) {
			if ((t/=d) < (1/2.75)) {
				return c*(7.5625*t*t) + b;
			} else if (t < (2/2.75)) {
				return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
			} else if (t < (2.5/2.75)) {
				return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
			} else {
				return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
			}
		},
		easeInOutBounce: function (x, t, b, c, d) {
			if (t < d/2) return $.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
			return $.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
		}
	});
	
})(jQuery);




