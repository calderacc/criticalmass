!function(a){var b;if("function"==typeof define&&define.amd)define(["leaflet"],a);else if("object"==typeof module&&"object"==typeof module.exports)b=require("leaflet"),module.exports=a(b);else{if("undefined"==typeof window.L)throw"Leaflet must be loaded first";a(window.L)}}(function(a){return a.Playback=a.Playback||{},a.Playback.Util=a.Class.extend({statics:{DateStr:function(a){return new Date(a).toDateString()},TimeStr:function(a){var b=new Date(a),c=b.getHours(),d=b.getMinutes(),e=b.getSeconds(),f=a/1e3,g=(f-Math.floor(f)).toFixed(2).slice(1),h="AM";return c>11&&(c%=12,h="PM"),0===c&&(c=12),10>d&&(d="0"+d),10>e&&(e="0"+e),c+":"+d+":"+e+g+" "+h},ParseGPX:function(a){for(var b={type:"Feature",geometry:{type:"MultiPoint",coordinates:[]},properties:{time:[],speed:[],altitude:[]},bbox:[]},c=$.parseXML(a),d=$(c).find("trkpt"),e=0,f=d.length;f>e;e++){var g=d[e],h=parseFloat(g.getAttribute("lat")),i=parseFloat(g.getAttribute("lon")),j=$(g).find("time").text(),k=$(g).find("ele").text(),l=new Date(j).getTime(),m=parseFloat(k),n=b.geometry.coordinates,o=b.properties,p=o.time,q=b.properties.altitude;n.push([i,h]),p.push(l),q.push(m)}return b}}}),a.Playback=a.Playback||{},a.Playback.MoveableMarker=a.Marker.extend({initialize:function(b,c,d){var e=c.marker||{};jQuery.isFunction(e)&&(e=e(d)),a.Marker.prototype.initialize.call(this,b,e),this.popupContent="",this.feature=d,e.getPopup&&(this.popupContent=e.getPopup(d)),c.popups&&this.bindPopup(this.getPopupContent()+b.toString()),c.labels&&(this.bindLabel?this.bindLabel(this.getPopupContent()):console.log("Label binding requires leaflet-label (https://github.com/Leaflet/Leaflet.label)"))},getPopupContent:function(){return""!==this.popupContent?"<b>"+this.popupContent+"</b><br/>":""},move:function(b,c){a.DomUtil.TRANSITION&&(this._icon&&(this._icon.style[a.DomUtil.TRANSITION]="all "+c+"ms linear",this._popup&&this._popup._wrapper&&(this._popup._wrapper.style[a.DomUtil.TRANSITION]="all "+c+"ms linear")),this._shadow&&(this._shadow.style[a.DomUtil.TRANSITION]="all "+c+"ms linear")),this.setLatLng(b),this._popup&&this._popup.setContent(this.getPopupContent()+this._latlng.toString())},_old__setPos:a.Marker.prototype._setPos,_updateImg:function(b,c,d){c=a.point(d).divideBy(2)._subtract(a.point(c));var e="";e+=" translate("+-c.x+"px, "+-c.y+"px)",e+=" rotate("+this.options.iconAngle+"deg)",e+=" translate("+c.x+"px, "+c.y+"px)",b.style[a.DomUtil.TRANSFORM]+=e},setIconAngle:function(a){this.options.iconAngle=a,this._map&&this.update()},_setPos:function(b){if(this._icon&&(this._icon.style[a.DomUtil.TRANSFORM]=""),this._shadow&&(this._shadow.style[a.DomUtil.TRANSFORM]=""),this._old__setPos.apply(this,[b]),this.options.iconAngle){var c,d=this.options.icon.options.iconAnchor,e=this.options.icon.options.iconSize;this._icon&&(c=this._icon,this._updateImg(c,d,e)),this._shadow&&(e=this.options.icon.options.shadowSize,c=this._shadow,this._updateImg(c,d,e))}}}),a.Playback=a.Playback||{},a.Playback.Track=a.Class.extend({initialize:function(a,b){b=b||{};var c=b.tickLen||250;this._staleTime=b.staleTime||36e5,this._fadeMarkersWhenStale=b.fadeMarkersWhenStale||!1,this._geoJSON=a,this._tickLen=c,this._ticks=[],this._marker=null,this._orientations=[];var d=a.properties.time;this._orientIcon=b.orientIcons;var e,f,g,h=a.geometry.coordinates,i=h[0],j=h[1],k=d[0],l=k,m=d[1],n=l%c;if(1===d.length)return 0!==n&&(l+=c-n),this._ticks[l]=h[0],this._orientations[l]=0,this._startTime=l,void(this._endTime=l);for(0!==n?(f=c-n,g=f/(m-k),l+=f,this._ticks[l]=this._interpolatePoint(i,j,g),this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]):(this._ticks[l]=i,this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]),this._startTime=l,l+=c;m>l;)g=(l-k)/(m-k),this._ticks[l]=this._interpolatePoint(i,j,g),this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l],l+=c;for(var o=1,p=h.length;p>o;o++)for(i=h[o],j=h[o+1],l=k=d[o],m=d[o+1],n=l%c,0!==n&&m?(f=c-n,g=f/(m-k),l+=f,this._ticks[l]=this._interpolatePoint(i,j,g),j?(this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]):this._orientations[l]=e):(this._ticks[l]=i,j?(this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]):this._orientations[l]=e),l+=c;m>l;)g=(l-k)/(m-k),m-k>b.maxInterpolationTime?(this._ticks[l]=i,j?(this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]):this._orientations[l]=e):(this._ticks[l]=this._interpolatePoint(i,j,g),j?(this._orientations[l]=this._directionOfPoint(i,j),e=this._orientations[l]):this._orientations[l]=e),l+=c;this._endTime=l-c,this._lastTick=this._ticks[this._endTime]},_interpolatePoint:function(a,b,c){try{var d=[b[0]-a[0],b[1]-a[1]],e=[d[0]*c,d[1]*c];return[a[0]+e[0],a[1]+e[1]]}catch(f){console.log("err: cant interpolate a point"),console.log(["start",a]),console.log(["end",b]),console.log(["ratio",c])}},_directionOfPoint:function(a,b){return this._getBearing(a[1],a[0],b[1],b[0])},_getBearing:function(a,b,c,d){a=this._radians(a),b=this._radians(b),c=this._radians(c),d=this._radians(d);var e=d-b,f=Math.log(Math.tan(c/2+Math.PI/4)/Math.tan(a/2+Math.PI/4));return Math.abs(e)>Math.PI&&(e=e>0?-(2*Math.PI-e):2*Math.PI+e),(this._degrees(Math.atan2(e,f))+360)%360},_radians:function(a){return a*(Math.PI/180)},_degrees:function(a){return a*(180/Math.PI)},getFirstTick:function(){return this._ticks[this._startTime]},getLastTick:function(){return this._ticks[this._endTime]},getStartTime:function(){return this._startTime},getEndTime:function(){return this._endTime},getTickMultiPoint:function(){for(var a=this.getStartTime(),b=this.getEndTime(),c=[],d=[];b>=a;)d.push(a),c.push(this.tick(a)),a+=this._tickLen;return{type:"Feature",geometry:{type:"MultiPoint",coordinates:c},properties:{time:d}}},trackPresentAtTick:function(a){return a>=this._startTime},trackStaleAtTick:function(a){return this._endTime+this._staleTime<=a},tick:function(a){return a>this._endTime&&(a=this._endTime),a<this._startTime&&(a=this._startTime),this._ticks[a]},courseAtTime:function(a){return a>this._endTime&&(a=this._endTime),a<this._startTime&&(a=this._startTime),this._orientations[a]},setMarker:function(b,c){var d=null;if(d=b?this.tick(b):this.getFirstTick()){var e=new a.LatLng(d[1],d[0]);this._marker=new a.Playback.MoveableMarker(e,c,this._geoJSON),c.mouseOverCallback&&this._marker.on("mouseover",c.mouseOverCallback),c.clickCallback&&this._marker.on("click",c.clickCallback),this._fadeMarkersWhenStale&&!this.trackPresentAtTick(b)&&this._marker.setOpacity(0)}return this._marker},moveMarker:function(a,b,c){this._marker&&(this._fadeMarkersWhenStale&&(this.trackPresentAtTick(c)?this._marker.setOpacity(1):this._marker.setOpacity(0),this.trackStaleAtTick(c)&&this._marker.setOpacity(.25)),this._orientIcon&&this._marker.setIconAngle(this.courseAtTime(c)),this._marker.move(a,b))},getMarker:function(){return this._marker}}),a.Playback=a.Playback||{},a.Playback.TrackController=a.Class.extend({initialize:function(a,b,c){this.options=c||{},this._map=a,this._tracks=[],this.setTracks(b)},clearTracks:function(){for(;this._tracks.length>0;){var a=this._tracks.pop(),b=a.getMarker();b&&this._map.removeLayer(b)}},setTracks:function(a){this.clearTracks(),this.addTracks(a)},addTracks:function(a){if(a)if(a instanceof Array)for(var b=0,c=a.length;c>b;b++)this.addTrack(a[b]);else this.addTrack(a)},addTrack:function(a,b){if(a){var c=a.setMarker(b,this.options);c&&(c.addTo(this._map),this._tracks.push(a))}},tock:function(b,c){for(var d=0,e=this._tracks.length;e>d;d++){var f=this._tracks[d].tick(b),g=new a.LatLng(f[1],f[0]);this._tracks[d].moveMarker(g,c,b)}},getStartTime:function(){var a=0;if(this._tracks.length>0){a=this._tracks[0].getStartTime();for(var b=1,c=this._tracks.length;c>b;b++){var d=this._tracks[b].getStartTime();a>d&&(a=d)}}return a},getEndTime:function(){var a=0;if(this._tracks.length>0){a=this._tracks[0].getEndTime();for(var b=1,c=this._tracks.length;c>b;b++){var d=this._tracks[b].getEndTime();d>a&&(a=d)}}return a},getTracks:function(){return this._tracks}}),a.Playback=a.Playback||{},a.Playback.Clock=a.Class.extend({initialize:function(b,c,d){this._trackController=b,this._callbacksArry=[],c&&this.addCallback(c),a.setOptions(this,d),this._speed=this.options.speed,this._tickLen=this.options.tickLen,this._cursor=b.getStartTime(),this._transitionTime=this._tickLen/this._speed},_tick:function(a){return a._cursor>a._trackController.getEndTime()?void clearInterval(a._intervalID):(a._trackController.tock(a._cursor,a._transitionTime),a._callbacks(a._cursor),void(a._cursor+=a._tickLen))},_callbacks:function(a){for(var b=this._callbacksArry,c=0,d=b.length;d>c;c++)b[c](a)},addCallback:function(a){this._callbacksArry.push(a)},start:function(){this._intervalID||(this._intervalID=window.setInterval(this._tick,this._transitionTime,this))},stop:function(){this._intervalID&&(clearInterval(this._intervalID),this._intervalID=null)},getSpeed:function(){return this._speed},isPlaying:function(){return this._intervalID?!0:!1},setSpeed:function(a){this._speed=a,this._transitionTime=this._tickLen/a,this._intervalID&&(this.stop(),this.start())},setCursor:function(a){var b=parseInt(a);if(b){var c=b%this._tickLen;0!==c&&(b+=this._tickLen-c),this._cursor=b,this._trackController.tock(this._cursor,0),this._callbacks(this._cursor)}},getTime:function(){return this._cursor},getStartTime:function(){return this._trackController.getStartTime()},getEndTime:function(){return this._trackController.getEndTime()},getTickLen:function(){return this._tickLen}}),a.Playback=a.Playback||{},a.Playback.TracksLayer=a.Class.extend({initialize:function(b,c){var d=c.layer||{};jQuery.isFunction(d)&&(d=d(feature)),d.pointToLayer||(d.pointToLayer=function(b,c){return new a.CircleMarker(c,{radius:5})}),this.layer=new a.GeoJSON(null,d);var e={"GPS Tracks":this.layer};a.control.layers(null,e,{collapsed:!1}).addTo(b)},clearLayer:function(){for(var a in this.layer._layers)this.layer.removeLayer(this.layer._layers[a])},addLayer:function(a){this.layer.addData(a)}}),a.Playback=a.Playback||{},a.Playback.DateControl=a.Control.extend({options:{position:"bottomleft",dateFormatFn:a.Playback.Util.DateStr,timeFormatFn:a.Playback.Util.TimeStr},initialize:function(b,c){a.setOptions(this,c),this.playback=b},onAdd:function(b){this._container=a.DomUtil.create("div","leaflet-control-layers leaflet-control-layers-expanded");var c=this,d=this.playback,e=d.getTime(),f=a.DomUtil.create("div","datetimeControl",this._container);return this._date=a.DomUtil.create("p","",f),this._time=a.DomUtil.create("p","",f),this._date.innerHTML=this.options.dateFormatFn(e),this._time.innerHTML=this.options.timeFormatFn(e),d.addCallback(function(a){c._date.innerHTML=c.options.dateFormatFn(a),c._time.innerHTML=c.options.timeFormatFn(a)}),this._container}}),a.Playback.PlayControl=a.Control.extend({options:{position:"bottomright"},initialize:function(a){this.playback=a},onAdd:function(b){function c(){e.isPlaying()?(e.stop(),d._button.innerHTML="Play"):(e.start(),d._button.innerHTML="Stop")}this._container=a.DomUtil.create("div","leaflet-control-layers leaflet-control-layers-expanded");var d=this,e=this.playback;e.setSpeed(100);var f=a.DomUtil.create("div","playControl",this._container);this._button=a.DomUtil.create("button","",f),this._button.innerHTML="Play";var g=a.DomEvent.stopPropagation;return a.DomEvent.on(this._button,"click",g).on(this._button,"mousedown",g).on(this._button,"dblclick",g).on(this._button,"click",a.DomEvent.preventDefault).on(this._button,"click",c,this),this._container}}),a.Playback.SliderControl=a.Control.extend({options:{position:"bottomleft"},initialize:function(a){this.playback=a},onAdd:function(b){function c(a){var b=Number(a.target.value);e.setCursor(b)}this._container=a.DomUtil.create("div","leaflet-control-layers leaflet-control-layers-expanded");var d=this,e=this.playback;this._slider=a.DomUtil.create("input","slider",this._container),this._slider.type="range",this._slider.min=e.getStartTime(),this._slider.max=e.getEndTime(),this._slider.value=e.getTime();var f=a.DomEvent.stopPropagation;return a.DomEvent.on(this._slider,"click",f).on(this._slider,"mousedown",f).on(this._slider,"dblclick",f).on(this._slider,"click",a.DomEvent.preventDefault).on(this._slider,"change",c,this).on(this._slider,"mousemove",c,this),e.addCallback(function(a){d._slider.value=a}),b.on("playback:add_tracks",function(){d._slider.min=e.getStartTime(),d._slider.max=e.getEndTime(),d._slider.value=e.getTime()}),this._container}}),a.Playback=a.Playback.Clock.extend({statics:{MoveableMarker:a.Playback.MoveableMarker,Track:a.Playback.Track,TrackController:a.Playback.TrackController,Clock:a.Playback.Clock,Util:a.Playback.Util,TracksLayer:a.Playback.TracksLayer,PlayControl:a.Playback.PlayControl,DateControl:a.Playback.DateControl,SliderControl:a.Playback.SliderControl},options:{tickLen:250,speed:1,maxInterpolationTime:3e5,tracksLayer:!0,playControl:!1,dateControl:!1,sliderControl:!1,layer:{},marker:{}},initialize:function(b,c,d,e){a.setOptions(this,e),this._map=b,this._trackController=new a.Playback.TrackController(b,null,this.options),a.Playback.Clock.prototype.initialize.call(this,this._trackController,d,this.options),this.options.tracksLayer&&(this._tracksLayer=new a.Playback.TracksLayer(b,e)),this.setData(c),this.options.playControl&&(this.playControl=new a.Playback.PlayControl(this),this.playControl.addTo(b)),this.options.sliderControl&&(this.sliderControl=new a.Playback.SliderControl(this),this.sliderControl.addTo(b)),this.options.dateControl&&(this.dateControl=new a.Playback.DateControl(this,e),this.dateControl.addTo(b))},clearData:function(){this._trackController.clearTracks(),this._tracksLayer&&this._tracksLayer.clearLayer()},setData:function(a){this.clearData(),this.addData(a,this.getTime()),this.setCursor(this.getStartTime())},addData:function(b,c){if(b){if(b instanceof Array)for(var d=0,e=b.length;e>d;d++)this._trackController.addTrack(new a.Playback.Track(b[d],this.options),c);else this._trackController.addTrack(new a.Playback.Track(b,this.options),c);this._map.fire("playback:set:data"),this.options.tracksLayer&&this._tracksLayer.addLayer(b)}},destroy:function(){this.clearData(),this.playControl&&this._map.removeControl(this.playControl),this.sliderControl&&this._map.removeControl(this.sliderControl),this.dateControl&&this._map.removeControl(this.dateControl)}}),a.Map.addInitHook(function(){this.options.playback&&(this.playback=new a.Playback(this))}),a.playback=function(b,c,d,e){return new a.Playback(b,c,d,e)},a.Playback});