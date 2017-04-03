/*!
 * fancyBox - jQuery Plugin
 * version: 2.0.6 (16/04/2012)
 * @requires jQuery v1.6 or later
 *
 * Examples at http://fancyapps.com/fancybox/
 * License: www.fancyapps.com/fancybox/#license
 *
 * Copyright 2012 Janis Skarnelis - janis@fancyapps.com
 *
 */
(function(i,k,g,d){var c=g(i),a=g(k),l=g.fancybox=function(){l.open.apply(this,arguments)},m=false,h=null,e=k.createTouch!==d,b=function(n){return g.type(n)==="string"},j=function(n){return b(n)&&n.indexOf("%")>0},f=function(n,o){if(o&&j(n)){n=l.getViewport()[o]/100*parseInt(n,10)}return Math.round(n)+"px"};g.extend(l,{version:"2.0.5",defaults:{padding:15,margin:20,width:800,height:600,minWidth:100,minHeight:100,maxWidth:9999,maxHeight:9999,autoSize:true,autoResize:!e,autoCenter:!e,fitToView:true,aspectRatio:false,topRatio:0.5,fixed:false,scrolling:"auto",wrapCSS:"",arrows:true,closeBtn:true,closeClick:true,nextClick:true,mouseWheel:true,autoPlay:false,playSpeed:3000,preload:3,modal:false,loop:true,ajax:{dataType:"html",headers:{"X-fancyBox":true}},keys:{next:[13,32,34,39,40],prev:[8,33,37,38],close:[27]},index:0,type:null,href:null,content:null,title:null,tpl:{wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',image:'<img class="fancybox-image" src="{href}" alt="" />',iframe:'<iframe class="fancybox-iframe" name="fancybox-frame{rnd}" frameborder="0" hspace="0"'+(g.browser.msie?' allowtransparency="true"':"")+"></iframe>",swf:'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="wmode" value="transparent" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{href}" /><embed src="{href}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%" wmode="transparent"></embed></object>',error:'<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',closeBtn:'<div title="Close" class="fancybox-item fancybox-close"></div>',next:'<a title="Next" class="fancybox-nav fancybox-next"><span></span></a>',prev:'<a title="Previous" class="fancybox-nav fancybox-prev"><span></span></a>'},openEffect:"fade",openSpeed:300,openEasing:"swing",openOpacity:true,openMethod:"zoomIn",closeEffect:"fade",closeSpeed:300,closeEasing:"swing",closeOpacity:true,closeMethod:"zoomOut",nextEffect:"elastic",nextSpeed:300,nextEasing:"swing",nextMethod:"changeIn",prevEffect:"elastic",prevSpeed:300,prevEasing:"swing",prevMethod:"changeOut",helpers:{overlay:{speedIn:0,speedOut:300,opacity:0.4,css:{cursor:"pointer"},closeClick:true},title:{type:"float"}},onCancel:g.noop,beforeLoad:g.noop,afterLoad:g.noop,beforeShow:g.noop,afterShow:g.noop,beforeClose:g.noop,afterClose:g.noop},group:{},opts:{},coming:null,current:null,isOpen:false,isOpened:false,wrap:null,skin:null,outer:null,inner:null,player:{timer:null,isActive:false},ajaxLoad:null,imgPreload:null,transitions:{},helpers:{},open:function(o,n){l.close(true);if(o&&!g.isArray(o)){o=o instanceof g?g(o).get():[o]}l.isActive=true;l.opts=g.extend(true,{},l.defaults,n);if(g.isPlainObject(n)&&n.keys!==d){l.opts.keys=n.keys?g.extend({},l.defaults.keys,n.keys):false}l.group=o;l._start(l.opts.index||0)},cancel:function(){if(l.coming&&false===l.trigger("onCancel")){return}l.coming=null;l.hideLoading();if(l.ajaxLoad){l.ajaxLoad.abort()}l.ajaxLoad=null;if(l.imgPreload){l.imgPreload.onload=l.imgPreload.onabort=l.imgPreload.onerror=null}},close:function(n){l.cancel();if(!l.current||false===l.trigger("beforeClose")){return}l.unbindEvents();if(!l.isOpen||(n&&n[0]===true)){g(".fancybox-wrap").stop().trigger("onReset").remove();l._afterZoomOut()}else{l.isOpen=l.isOpened=false;g(".fancybox-item, .fancybox-nav").remove();l.wrap.stop(true).removeClass("fancybox-opened");l.inner.css("overflow","hidden");l.transitions[l.current.closeMethod]()}},play:function(o){var n=function(){clearTimeout(l.player.timer)},r=function(){n();if(l.current&&l.player.isActive){l.player.timer=setTimeout(l.next,l.current.playSpeed)}},p=function(){n();g("body").unbind(".player");l.player.isActive=false;l.trigger("onPlayEnd")},q=function(){if(l.current&&(l.current.loop||l.current.index<l.group.length-1)){l.player.isActive=true;g("body").bind({"afterShow.player onUpdate.player":r,"onCancel.player beforeClose.player":p,"beforeLoad.player":n});r();l.trigger("onPlayStart")}};if(l.player.isActive||(o&&o[0]===false)){p()}else{q()}},next:function(){if(l.current){l.jumpto(l.current.index+1)}},prev:function(){if(l.current){l.jumpto(l.current.index-1)}},jumpto:function(n){if(!l.current){return}n=parseInt(n,10);if(l.group.length>1&&l.current.loop){if(n>=l.group.length){n=0}else{if(n<0){n=l.group.length-1}}}if(l.group[n]!==d){l.cancel();l._start(n)}},reposition:function(o,n){var p;if(l.isOpen){p=l._getPosition(n);if(o&&o.type==="scroll"){delete p.position;l.wrap.stop(true,true).animate(p,200)}else{l.wrap.css(p)}}},update:function(n){if(!l.isOpen){return}if(!m){h=setTimeout(function(){var p=l.current,o=!n||(n&&n.type==="orientationchange");if(m){m=false;if(!p){return}if((!n||n.type!=="scroll")||o){if(p.autoSize&&p.type!=="iframe"){l.inner.height("auto");p.height=l.inner.height()}if(p.autoResize||o){l._setDimension()}if(p.canGrow&&p.type!=="iframe"){l.inner.height("auto")}}if(p.autoCenter||o){l.reposition(n)}l.trigger("onUpdate")}},200)}m=true},toggle:function(){if(l.isOpen){l.current.fitToView=!l.current.fitToView;l.update()}},hideLoading:function(){a.unbind("keypress.fb");g("#fancybox-loading").remove()},showLoading:function(){l.hideLoading();a.bind("keypress.fb",function(n){if(n.keyCode===27){n.preventDefault();l.cancel()}});g('<div id="fancybox-loading"><div></div></div>').click(l.cancel).appendTo("body")},getViewport:function(){return{x:c.scrollLeft(),y:c.scrollTop(),w:e&&i.innerWidth?i.innerWidth:c.width(),h:e&&i.innerHeight?i.innerHeight:c.height()}},unbindEvents:function(){if(l.wrap){l.wrap.unbind(".fb")}a.unbind(".fb");c.unbind(".fb")},bindEvents:function(){var o=l.current,n=o.keys;if(!o){return}c.bind("resize.fb orientationchange.fb"+(o.autoCenter&&!o.fixed?" scroll.fb":""),l.update);if(n){a.bind("keydown.fb",function(r){var p,q=r.target||r.srcElement;if(!r.ctrlKey&&!r.altKey&&!r.shiftKey&&!r.metaKey&&!(q&&(q.type||g(q).is("[contenteditable]")))){p=r.keyCode;if(g.inArray(p,n.close)>-1){l.close();r.preventDefault()}else{if(g.inArray(p,n.next)>-1){l.next();r.preventDefault()}else{if(g.inArray(p,n.prev)>-1){l.prev();r.preventDefault()}}}}})}if(g.fn.mousewheel&&o.mouseWheel&&l.group.length>1){l.wrap.bind("mousewheel.fb",function(q,r){var p=q.target||null;if(r!==0&&(!p||p.clientHeight===0||(p.scrollHeight===p.clientHeight&&p.scrollWidth===p.clientWidth))){q.preventDefault();l[r>0?"prev":"next"]()}})}},trigger:function(p,r){var n,q=r||l[g.inArray(p,["onCancel","beforeLoad","afterLoad"])>-1?"coming":"current"];if(!q){return}if(g.isFunction(q[p])){n=q[p].apply(q,Array.prototype.slice.call(arguments,1))}if(n===false){return false}if(q.helpers){g.each(q.helpers,function(s,o){if(o&&g.isPlainObject(l.helpers[s])&&g.isFunction(l.helpers[s][p])){l.helpers[s][p](o,q)}})}g.event.trigger(p+".fb")},isImage:function(n){return b(n)&&n.match(/\.(jpe?g|gif|png|bmp)((\?|#).*)?$/i)},isSWF:function(n){return b(n)&&n.match(/\.(swf)((\?|#).*)?$/i)},_start:function(o){var r={},q=l.group[o]||null,p,n,s,t,u;if(q&&(q.nodeType||q instanceof g)){p=true;if(g.metadata){r=g(q).metadata()}}r=g.extend(true,{},l.opts,{index:o,element:q},(g.isPlainObject(q)?q:r));g.each(["href","title","content","type"],function(x,w){r[w]=l.opts[w]||(p&&g(q).attr(w))||r[w]||null});if(typeof r.margin==="number"){r.margin=[r.margin,r.margin,r.margin,r.margin]}if(r.modal){g.extend(true,r,{closeBtn:false,closeClick:false,nextClick:false,arrows:false,mouseWheel:false,keys:null,helpers:{overlay:{css:{cursor:"auto"},closeClick:false}}})}l.coming=r;if(false===l.trigger("beforeLoad")){l.coming=null;return}s=r.type;n=r.href||q;if(!s){if(p){s=g(q).data("fancybox-type");if(!s){t=q.className.match(/fancybox\.(\w+)/);s=t?t[1]:null}}if(!s&&b(n)){if(l.isImage(n)){s="image"}else{if(l.isSWF(n)){s="swf"}else{if(n.match(/^#/)){s="inline"}}}}if(!s){s=p?"inline":"html"}r.type=s}if(s==="inline"||s==="html"){if(!r.content){if(s==="inline"){r.content=g(b(n)?n.replace(/.*(?=#[^\s]+$)/,""):n)}else{r.content=q}}if(!r.content||!r.content.length){s=null}}else{if(!n){s=null}}if(s==="ajax"&&b(n)){u=n.split(/\s+/,2);n=u.shift();r.selector=u.shift()}r.href=n;r.group=l.group;r.isDom=p;switch(s){case"image":l._loadImage();break;case"ajax":l._loadAjax();break;case"inline":case"iframe":case"swf":case"html":l._afterLoad();break;default:l._error("type")}},_error:function(n){l.hideLoading();g.extend(l.coming,{type:"html",autoSize:true,minWidth:0,minHeight:0,padding:15,hasError:n,content:l.coming.tpl.error});l._afterLoad()},_loadImage:function(){var n=l.imgPreload=new Image();n.onload=function(){this.onload=this.onerror=null;l.coming.width=this.width;l.coming.height=this.height;l._afterLoad()};n.onerror=function(){this.onload=this.onerror=null;l._error("image")};n.src=l.coming.href;if(n.complete===d||!n.complete){l.showLoading()}},_loadAjax:function(){l.showLoading();l.ajaxLoad=g.ajax(g.extend({},l.coming.ajax,{url:l.coming.href,error:function(n,o){if(l.coming&&o!=="abort"){l._error("ajax",n)}else{l.hideLoading()}},success:function(n,o){if(o==="success"){l.coming.content=n;l._afterLoad()}}}))},_preloadImages:function(){var t=l.group,s=l.current,n=t.length,r,o,q,p=Math.min(s.preload,n-1);if(!s.preload||t.length<2){return}for(q=1;q<=p;q+=1){r=t[(s.index+q)%n];o=r.href||g(r).attr("href")||r;if(r.type==="image"||l.isImage(o)){new Image().src=o}}},_afterLoad:function(){l.hideLoading();if(!l.coming||false===l.trigger("afterLoad",l.current)){l.coming=false;return}if(l.isOpened){g(".fancybox-item, .fancybox-nav").remove();l.wrap.stop(true).removeClass("fancybox-opened");l.inner.css("overflow","hidden");l.transitions[l.current.prevMethod]()}else{g(".fancybox-wrap").stop().trigger("onReset").remove();l.trigger("afterClose")}l.unbindEvents();l.isOpen=false;l.current=l.coming;l.wrap=g(l.current.tpl.wrap).addClass("fancybox-"+(e?"mobile":"desktop")+" fancybox-type-"+l.current.type+" fancybox-tmp "+l.current.wrapCSS).appendTo("body");l.skin=g(".fancybox-skin",l.wrap).css("padding",f(l.current.padding));l.outer=g(".fancybox-outer",l.wrap);l.inner=g(".fancybox-inner",l.wrap);l._setContent()},_setContent:function(){var u=l.current,r=u.content,o=u.type,n=u.minWidth,t=u.minHeight,q=u.maxWidth,p=u.maxHeight,s;switch(o){case"inline":case"ajax":case"html":if(u.selector){r=g("<div>").html(r).find(u.selector)}else{if(r instanceof g){if(r.parent().hasClass("fancybox-inner")){r.parents(".fancybox-wrap").unbind("onReset")}r=r.show().detach();g(l.wrap).bind("onReset",function(){r.appendTo("body").hide()})}}if(u.autoSize){s=g('<div class="fancybox-wrap '+l.current.wrapCSS+' fancybox-tmp"></div>').appendTo("body").css({minWidth:f(n,"w"),minHeight:f(t,"h"),maxWidth:f(q,"w"),maxHeight:f(p,"h")}).append(r);u.width=s.width();u.height=s.height();s.width(l.current.width);if(s.height()>u.height){s.width(u.width+1);u.width=s.width();u.height=s.height()}r=s.contents().detach();s.remove()}break;case"image":r=u.tpl.image.replace("{href}",u.href);u.aspectRatio=true;break;case"swf":r=u.tpl.swf.replace(/\{width\}/g,u.width).replace(/\{height\}/g,u.height).replace(/\{href\}/g,u.href);break;case"iframe":r=g(u.tpl.iframe.replace("{rnd}",new Date().getTime())).attr("scrolling",u.scrolling).attr("src",u.href);u.scrolling=e?"scroll":"auto";break}if(o==="image"||o==="swf"){u.autoSize=false;u.scrolling="visible"}if(o==="iframe"&&u.autoSize){l.showLoading();l._setDimension();l.inner.css("overflow",u.scrolling);r.bind({onCancel:function(){g(this).unbind();l._afterZoomOut()},load:function(){l.hideLoading();try{if(this.contentWindow.document.location){l.current.height=g(this).contents().find("body").height()}}catch(v){l.current.autoSize=false}l[l.isOpen?"_afterZoomIn":"_beforeShow"]()}}).appendTo(l.inner)}else{l.inner.append(r);l._beforeShow()}},_beforeShow:function(){l.coming=null;l.trigger("beforeShow");l._setDimension();l.wrap.hide().removeClass("fancybox-tmp");l.bindEvents();l._preloadImages();l.transitions[l.isOpened?l.current.nextMethod:l.current.openMethod]()},_setDimension:function(){var p=l.wrap,A=l.inner,t=l.current,u=l.getViewport(),r=t.margin,n=t.padding*2,q=t.width,z=t.height,x=t.maxWidth+n,w=t.maxHeight+n,o=t.minWidth+n,y=t.minHeight+n,v,s;u.w-=(r[1]+r[3]);u.h-=(r[0]+r[2]);if(j(q)){q=(((u.w-n)*parseFloat(q))/100)}if(j(z)){z=(((u.h-n)*parseFloat(z))/100)}v=q/z;q+=n;z+=n;if(t.fitToView){x=Math.min(u.w,x);w=Math.min(u.h,w)}if(t.aspectRatio){if(q>x){q=x;z=((q-n)/v)+n}if(z>w){z=w;q=((z-n)*v)+n}if(q<o){q=o;z=((q-n)/v)+n}if(z<y){z=y;q=((z-n)*v)+n}}else{q=Math.max(o,Math.min(q,x));z=Math.max(y,Math.min(z,w))}q=Math.round(q);z=Math.round(z);g(p.add(A)).width("auto").height("auto");A.width(q-n).height(z-n);p.width(q);s=p.height();if(q>x||s>w){while((q>x||s>w)&&q>o&&s>y){z=z-10;if(t.aspectRatio){q=Math.round(((z-n)*v)+n);if(q<o){q=o;z=((q-n)/v)+n}}else{q=q-10}A.width(q-n).height(z-n);p.width(q);s=p.height()}}t.dim={width:f(q),height:f(s)};t.canGrow=t.autoSize&&z>y&&z<w;t.canShrink=false;t.canExpand=false;if((q-n)<t.width||(z-n)<t.height){t.canExpand=true}else{if((q>u.w||s>u.h)&&q>o&&z>y){t.canShrink=true}}l.innerSpace=s-n-A.height()},_getPosition:function(p){var t=l.current,o=l.getViewport(),r=t.margin,q=l.wrap.width()+r[1]+r[3],n=l.wrap.height()+r[0]+r[2],s={position:"absolute",top:r[0]+o.y,left:r[3]+o.x};if(t.autoCenter&&t.fixed&&!p&&n<=o.h&&q<=o.w){s={position:"fixed",top:r[0],left:r[3]}}s.top=f(Math.max(s.top,s.top+((o.h-n)*t.topRatio)));s.left=f(Math.max(s.left,s.left+((o.w-q)*0.5)));return s},_afterZoomIn:function(){var o=l.current,n=o?o.scrolling:"no";if(!o){return}l.isOpen=l.isOpened=true;l.wrap.addClass("fancybox-opened");l.inner.css("overflow",n==="yes"?"scroll":(n==="no"?"hidden":n));l.trigger("afterShow");l.update();if(o.closeClick||o.nextClick){l.inner.css("cursor","pointer").bind("click.fb",function(p){if(!g(p.target).is("a")&&!g(p.target).parent().is("a")){l[o.closeClick?"close":"next"]()}})}if(o.closeBtn){g(o.tpl.closeBtn).appendTo(l.skin).bind("click.fb",l.close)}if(o.arrows&&l.group.length>1){if(o.loop||o.index>0){g(o.tpl.prev).appendTo(l.outer).bind("click.fb",l.prev)}if(o.loop||o.index<l.group.length-1){g(o.tpl.next).appendTo(l.outer).bind("click.fb",l.next)}}if(l.opts.autoPlay&&!l.player.isActive){l.opts.autoPlay=false;l.play()}},_afterZoomOut:function(){var n=l.current;l.wrap.trigger("onReset").remove();g.extend(l,{group:{},opts:{},current:null,isActive:false,isOpened:false,isOpen:false,wrap:null,skin:null,outer:null,inner:null});l.trigger("afterClose",n)}});l.transitions={getOrigPosition:function(){var s=l.current,p=s.element,r=s.padding,u=g(s.orig),t={},q=50,o=50,n;if(!u.length&&s.isDom&&g(p).is(":visible")){u=g(p).find("img:first");if(!u.length){u=g(p)}}if(u.length){t=u.offset();if(u.is("img")){q=u.outerWidth();o=u.outerHeight()}}else{n=l.getViewport();t.top=n.y+(n.h-o)*0.5;t.left=n.x+(n.w-q)*0.5}t={top:f(t.top-r),left:f(t.left-r),width:f(q+r*2),height:f(o+r*2)};return t},step:function(n,p){var r=p.prop,q,o;if(r==="width"||r==="height"){q=Math.ceil(n-(l.current.padding*2));if(r==="height"){o=(n-p.start)/(p.end-p.start);if(p.start>p.end){o=1-o}q-=l.innerSpace*o}l.inner[r](q)}},zoomIn:function(){var q=l.wrap,t=l.current,p=t.openEffect,s=p==="elastic",r=t.dim,o=g.extend({},r,l._getPosition(s)),n=g.extend({opacity:1},o);delete n.position;if(s){o=this.getOrigPosition();if(t.openOpacity){o.opacity=0}l.outer.add(l.inner).width("auto").height("auto")}else{if(p==="fade"){o.opacity=0}}q.css(o).show().animate(n,{duration:p==="none"?0:t.openSpeed,easing:t.openEasing,step:s?this.step:null,complete:l._afterZoomIn})},zoomOut:function(){var p=l.wrap,r=l.current,o=r.openEffect,q=o==="elastic",n={opacity:0};if(q){if(p.css("position")==="fixed"){p.css(l._getPosition(true))}n=this.getOrigPosition();if(r.closeOpacity){n.opacity=0}}p.animate(n,{duration:o==="none"?0:r.closeSpeed,easing:r.closeEasing,step:q?this.step:null,complete:l._afterZoomOut})},changeIn:function(){var q=l.wrap,s=l.current,p=s.nextEffect,r=p==="elastic",o=l._getPosition(r),n={opacity:1};o.opacity=0;if(r){o.top=f(parseInt(o.top,10)-200);n.top="+=200px"}q.css(o).show().animate(n,{duration:p==="none"?0:s.nextSpeed,easing:s.nextEasing,complete:l._afterZoomIn})},changeOut:function(){var p=l.wrap,r=l.current,o=r.prevEffect,n={opacity:0},q=function(){g(this).trigger("onReset").remove()};p.removeClass("fancybox-opened");if(o==="elastic"){n.top="+=200px"}p.animate(n,{duration:o==="none"?0:r.prevSpeed,easing:r.prevEasing,complete:q})}};l.helpers.overlay={overlay:null,update:function(){var p,n,o;this.overlay.width("100%").height("100%");if(g.browser.msie||e){n=Math.max(k.documentElement.scrollWidth,k.body.scrollWidth);o=Math.max(k.documentElement.offsetWidth,k.body.offsetWidth);p=n<o?c.width():n}else{p=a.width()}this.overlay.width(p).height(a.height())},beforeShow:function(n){if(this.overlay){return}n=g.extend(true,{},l.defaults.helpers.overlay,n);this.overlay=g('<div id="fancybox-overlay"></div>').css(n.css).appendTo("body");if(n.closeClick){this.overlay.bind("click.fb",l.close)}if(l.current.fixed&&!e){this.overlay.addClass("overlay-fixed")}else{this.update();this.onUpdate=function(){this.update()}}this.overlay.fadeTo(n.speedIn,n.opacity)},afterClose:function(n){if(this.overlay){this.overlay.fadeOut(n.speedOut||0,function(){g(this).remove()})}this.overlay=null}};l.helpers.title={beforeShow:function(n){var p,o=l.current.title;if(o){p=g('<div class="fancybox-title fancybox-title-'+n.type+'-wrap">'+o+"</div>").appendTo("body");if(n.type==="float"){p.width(p.width());p.wrapInner('<span class="child"></span>');l.current.margin[2]+=Math.abs(parseInt(p.css("margin-bottom"),10))}p.appendTo(n.type==="over"?l.inner:(n.type==="outside"?l.wrap:l.skin))}}};g.fn.fancybox=function(p){var q=g(this),n=this.selector||"",o,r=function(v){var u=this,s=o,t,w;if(!(v.ctrlKey||v.altKey||v.shiftKey||v.metaKey)&&!g(u).is(".fancybox-wrap")){v.preventDefault();t=p.groupAttr||"data-fancybox-group";w=g(u).attr(t);if(!w){t="rel";w=u[t]}if(w&&w!==""&&w!=="nofollow"){u=n.length?g(n):q;u=u.filter("["+t+'="'+w+'"]');s=u.index(this)}p.index=s;l.open(u,p)}};p=p||{};o=p.index||0;if(n){a.undelegate(n,"click.fb-start").delegate(n,"click.fb-start",r)}else{q.unbind("click.fb-start").bind("click.fb-start",r)}return this};g(k).ready(function(){l.defaults.fixed=g.support.fixedPosition||(!(g.browser.msie&&g.browser.version<=6)&&!e)})}(window,document,jQuery));