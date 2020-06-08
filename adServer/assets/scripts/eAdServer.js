/*
        eAdServer.js loader
            by bonAngeLOL
        This script just insert a new iframe element on every element
        accesible via [ins.e-ad] CSS selector with a "valid data-position" attribute.
        iFrames are resized when content within emits "load" event.
        Last function injects a modal element to display "FoOhlLsiSeHd" ads when a [ins.e-ad-mod]
        element is found xD.
*/
(function(){
        iframeList = [];
        function iterateElement(elements){
            if(elements===undefined||elements.length==0)
            {return false;}
            for(var i=0 ; i<elements.length ; i++){
                elId = elements[i].getAttribute("data-position");
                console.log(elements[i]);
                var niframe = document.createElement("iframe");
                //niframe.setAttribute("data-position");
                niframe.src = "https://ad.e-consulta.com/position/serve/"+elements[i].getAttribute("data-position");
                niframe.width = "100%";
                niframe.tabIndex = 0;
                niframe.scrolling = "no";
                niframe.frameBorder = 0;
                niframe.style.border = "none";
                niframe.style.overflow = "hidden";
                niframe.style.userSelect = "none";
                niframe.allowTranparency = true;
                //niframe.style.display = 'none';
                /*function messageCatch(Ev,eId){
                    console.log("eId",elId);
                    console.log("Ev.data.id",Ev.data.id);
                    if(eId!=Ev.data.id){
                        return false;
                    }
                    document.querySelector('ins.e-ad[data-position="'+eId+'"] > iframe').style.height = Ev.data.frameHeight+"px";
                    console.log("setting to",document.querySelector('ins.e-ad[data-position="'+eId+'"] > iframe'));
                    console.log("height",Ev.data.frameHeight);
                }
                window.addEventListener("message",function(Ev){
                        console.log("creating a listener for ");
                                messageCatch(Ev,elId);
                });*/
                elements[i].appendChild(niframe);
                niframe = null;
            }
        }


        function messageCatch(Ev){
                    //console.log("eId",elId);
                    //console.log("Ev.data.id",Ev.data.id);
                    //if(eId!=Ev.data.id){
                    //    return false;
                    //}
            try{
                var el = document.querySelector('ins.e-ad[data-position="'+Ev.data.id+'"] > iframe');
                if((Ev.data.frameHeight==0||Ev.data.frameHeight==null||Ev.data.frameHeight=='')&&Ev.data.id)
                {
                        console.log("size was 0","and id "+Ev.data.id);
                        try{
                                console.log(document.querySelector('ins.e-ad[data-position="'+Ev.data.id+'"] > iframe'));
                                el.style.display = "none";
                                return false;
                        }
                        catch(e){}
                }
                el.style.height = Ev.data.frameHeight+"px";
                el.style.display = 'block';
                console.log(el,'ins.e-ad[data-position="'+Ev.data.id+'"] > iframe');
                console.log(Ev.data.frameHeight);
                document.querySelector('ins.e-ad[data-position="'+Ev.data.id+'"]').style.height = "auto";
            }
            catch(e){
            }
        }
        window.addEventListener("message",function(Ev){
                messageCatch(Ev);
        });


        var elements = document.querySelectorAll("ins.e-ad");
        iterateElement(elements)
})()
(function(){
el = document.querySelector("ins.e-ad-mod");
if(el!=null){
  element = document.createElement('div');
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      img = false;
      //console.log(this.responseText);
      element.innerHTML = this.responseText;
      json = JSON.parse(element.querySelector("#data").innerHTML);
      for(var i = 0; i<json.length ; i++ ){
        
        if(json[i].type=="image"){
            img = json[i];
            break;
        }
      }
      console.log(img);
      if(img){
        nImage = '<img src="https://ad.e-consulta.com/images/'+img.src+'" style="width: auto;overflow: hidden;max-width: 950px;margin-top: 100px;">';
        if(img.link){
            nImage = '<a href="'+img.link+'" target="_blank">'+nImage+'</a>';
        }
        el.innerHTML = '<div class="modalAdKill blind" style="position: fixed; top: 0px; right: 0px; bottom: 0px; left: 0px; z-index: 1050; outline: 0px; width: 100%; height: 100%; overflow: hidden auto; background: rgba(0, 0, 0, 0.18); text-align: center; display: block;">'+
        nImage+
        '<button type="button" style="position: absolute;margin-top: 100px;color: white;background-color: black;border: solid 2px white;border-radius: 15px;width: 30px;height: 30px;text-align: center;font-size: 16px;font-weight: bold;" class="modalAdKill">x</button></div>';
      }
    }
  };
  xhttp.open("GET", "https://ad.e-consulta.com/position/serve/"+el.getAttribute("data-position"), true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();
}

document.addEventListener("click",function(x){
    if(x.target.classList.contains("modalAdKill")){
        document.querySelector(".blind").style.display = "none";
    }
});
})()