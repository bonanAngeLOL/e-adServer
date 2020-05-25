/* f sdf sdaf sad sa sad f*/
/*
        Script by bonAngeLOL
        This script just insert a new iframe element on every element
        accesible via [ins.e-ad] CSS selector with a "valid data-position" attribute
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
