/*
	Script by bonAngeLOL
	This script just insert a new iframe element on every element
	accesible via [ins.e-ad] CSS selector with a "valid data-position" attribute
*/
(function(){
	function iterateElement(elements){
	    if(elements===undefined||elements.length==0)
	    {return false;}
	    for(var i=0 ; i<elements.length ; i++){
	        elId = elements[i].getAttribute("data-position");
	        console.log(elements[i]);
	        var niframe = document.createElement("iframe");
	        niframe.src = "https://ad.e-consulta.com/position/serve/"+elements[i].getAttribute("data-position");
	        niframe.width = "100%";
	        niframe.tabIndex = 0;
	        niframe.scrolling = "no";
	        niframe.frameBorder = 0;
	        niframe.style.border = "none";
	        niframe.style.overflow = "hidden";
	        niframe.style.userSelect = "none";
	        niframe.allowTranparency = true;
		function messageCatch(Ev,eId){
	            console.log("eId",elId);
	            console.log("Ev.data.id",Ev.data.id);
	            if(eId!=Ev.data.id){
	                return false;
	            }
	            niframe.style.height = Ev.data.frameHeight+"px";
		}
	        window.addEventListener("message",function(Ev){
				messageCatch(Ev,elId)
	        });
	        elements[i].appendChild(niframe);
	    }
	}

	var elements = document.querySelectorAll("ins.e-ad");
	iterateElement(xxx)
})()