!function($){"use strict";var e=function(e,a,t){return e?(t=t||"","object"===$.type(t)&&(t=$.param(t,!0)),$.each(a,function(a,t){e=e.replace("$"+a,t||"")}),t.length&&(e+=(e.indexOf("?")>0?"&":"?")+t),e):void 0},a={youtube_playlist:{matcher:/^http:\/\/(?:www\.)?youtube\.com\/watch\?((v=[^&\s]*&list=[^&\s]*)|(list=[^&\s]*&v=[^&\s]*))(&[^&\s]*)*$/,params:{autoplay:1,autohide:1,fs:1,rel:0,hd:1,wmode:"transparent",enablejsapi:1,html5:1},paramPlace:8,type:"iframe",url:"//www.youtube.com/embed/videoseries?list=$4",thumb:"//img.youtube.com/vi/$4/hqdefault.jpg"},youtube:{matcher:/(youtube\.com|youtu\.be|youtube\-nocookie\.com)\/(watch\?(.*&)?v=|v\/|u\/|embed\/?)?(videoseries\?list=(.*)|[\w-]{11}|\?listType=(.*)&list=(.*))(.*)/i,params:{autoplay:1,autohide:1,fs:1,rel:0,hd:1,wmode:"transparent",enablejsapi:1,html5:1},paramPlace:8,type:"iframe",url:"//www.youtube.com/embed/$4",thumb:"//img.youtube.com/vi/$4/hqdefault.jpg"},vimeo:{matcher:/^.+vimeo.com\/(.*\/)?([\d]+)(.*)?/,params:{autoplay:1,hd:1,show_title:1,show_byline:1,show_portrait:0,fullscreen:1,api:1},paramPlace:3,type:"iframe",url:"//player.vimeo.com/video/$2"},metacafe:{matcher:/metacafe.com\/watch\/(\d+)\/(.*)?/,type:"iframe",url:"//www.metacafe.com/embed/$1/?ap=1"},dailymotion:{matcher:/dailymotion.com\/video\/(.*)\/?(.*)/,params:{additionalInfos:0,autoStart:1},type:"iframe",url:"//www.dailymotion.com/embed/video/$1"},facebook:{matcher:/facebook.com\/facebook\/videos\/(.*)\/?(.*)/,type:"genericDiv",subtype:"facebook",url:"//www.facebook.com/facebook/videos/$1"},instagram:{matcher:/(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/i,type:"image",url:"//$1/p/$2/media/?size=l"},instagram_tv:{matcher:/(instagr\.am|instagram\.com)\/tv\/([a-zA-Z0-9_\-]+)\/?/i,type:"iframe",url:"//$1/p/$2/media/?size=l"},wistia:{matcher:/wistia.com\/medias\/(.*)\/?(.*)/,type:"iframe",url:"//fast.wistia.net/embed/iframe/$1"},twitch:{matcher:/player.twitch.tv\/[\\?&]video=([^&#]*)/,type:"iframe",url:"//player.twitch.tv/?video=$1"},videopress:{matcher:/videopress.com\/v\/(.*)\/?(.*)/,type:"iframe",url:"//videopress.com/embed/$1"},gmap_place:{matcher:/(maps\.)?google\.([a-z]{2,3}(\.[a-z]{2})?)\/(((maps\/(place\/(.*)\/)?\@(.*),(\d+.?\d+?)z))|(\?ll=))(.*)?/i,type:"iframe",url:function(e){return"//maps.google."+e[2]+"/?ll="+(e[9]?e[9]+"&z="+Math.floor(e[10])+(e[12]?e[12].replace(/^\//,"&"):""):e[12])+"&output="+(e[12]&&e[12].indexOf("layer=c")>0?"svembed":"embed")}},gmap_search:{matcher:/(maps\.)?google\.([a-z]{2,3}(\.[a-z]{2})?)\/(maps\/search\/)(.*)/i,type:"iframe",url:function(e){return"//maps.google."+e[2]+"/maps?q="+e[5].replace("query=","q=").replace("api=1","")+"&output=embed"}}};$(document).on("onInit.eb",function(t,o){$.each(o.group,function(t,o){var i=o.src||"",m=!1,r=!1,p=!1,c,s,l,u,n,d,h=!1;o.type||(c=$.extend(!0,{},a,o.opts.media),$.each(c,function(a,t){if(l=i.match(t.matcher),d={},l){if(h=a,m=t.type,void 0!==t.subtype&&(r=t.subtype),t.paramPlace&&l[t.paramPlace]){n=l[t.paramPlace],"?"==n[0]&&(n=n.substring(1)),n=n.split("&");for(var c=0;c<n.length;++c){var y=n[c].split("=",2);2==y.length&&(d[y[0]]=decodeURIComponent(y[1].replace(/\+/g," ")))}}return u=$.extend(!0,{},t.params,o.opts[a],d),p="function"===$.type(t.url)?t.url.call(this,l,u,o):e(t.url,l,u),s="function"===$.type(t.thumb)?t.thumb.call(this,l,u,o):e(t.thumb,l),"vimeo"===h&&(p=p.replace("&%23","#")),!1}}),m?(o.src=p,o.type=m,o.subtype=r,o.opts.thumb||o.opts.$thumb&&o.opts.$thumb.length||(o.opts.thumb=s),"iframe"===m&&($.extend(!0,o.opts,{iframe:{preload:!1,provider:h,attr:{scrolling:"no"}}}),o.contentProvider=h,o.opts.slideClass+=" envirabox-slide--"+("gmap_place"==h||"gmap_search"==h?"map":"video"))):o.type="image")})})}(window.jQuery);