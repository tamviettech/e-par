tileMosaicFlip = function(length,r,m,af){
	var f = false;
	var i = 0;
	var s = 300;
	while(!f){	
		var g = Math.floor((Math.random()*m)+1);
		if($.inArray(g,af)==-1){f=true;}
		i++;
		if(i>length){f=true;g=0;}
	}	
	af.push(g);
	var $id = $("#tileMosaic"+m+"_"+g);
	var $id_back = $("#tileMosaic"+m+"_"+g+"b");
	if(r==0){ // if big image is front now
		var margin = $id_back.width()/2;
		var height= $id_back.width();
		$id_back.css({height:''+height+"px",marginTop:'0px'});
		$id.css({height:'0px',marginTop:''+margin+'px'});
		$id_back.animate({height:'0px',marginTop:''+margin+'px'},150,function(){
			$id.animate({height:''+height+'px',marginTop:'0px'},150);
		});	
		if(af.length>length){
			af = new Array();
			r=1;
			s=650;
		}
	}else{
		var margin = $id.width()/2;
		var height= $id.width();
		$id.css({height:''+height+"px",marginTop:'0px'});
		$id_back.css({height:'0px',marginTop:''+margin+'px'});
		$id.animate({height:'0px',marginTop:''+margin+'px'},150,function(){
			$id_back.animate({height:height+'px',marginTop:'0px'},150);
		});
		if(af.length>length){
			af = new Array();
			r=0;
			s=1000;
		}
	}
	setTimeout(function(){tileMosaicFlip(length,r,m,af)},s);
}