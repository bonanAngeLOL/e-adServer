/*
	Ads loader main JS file
	by bonAngeLOL
*/
(function (){
var adElement = document.getElementById("ad-container");
function sendPostMessage(){
	height = document.getElementById('ad-container').offsetHeight;
	window.parent.postMessage(
		{
			frameHeight: height,
			id: id
		}, 
		'*'
	);
	console.log(height);
}
function createElement(element,parent){
    var tag = element.tagName;
    var nElement = document.createElement(tag);
    try{
       var attribNum = element.attributes.length;
    }
    catch(e){
        parent.appendChild(element);
        return false;
    }
    for (var i = 0; i < attribNum; i++) {
        var attrib = element.attributes[i];
        if (attrib.specified) {
            nElement.setAttribute(attrib.name, attrib.value);
        }
    }
    parent.appendChild(nElement);
    return nElement;
}
function waitAdSense(){
	var wait = setInterval(
		function(){
	        try{
	            if(adsbygoogle.loaded==true){
            		clearInterval(wait);
        			sendPostMessage();
	            }
	        }
	        catch(e){
	            
	        }
	    }, 250);
}
function waitCode(){
	/*var wait = setInterval(
		function(){
	        try{
	            if(adsbygoogle.loaded==true){
            		clearInterval(wait);
        			sendPostMessage();
	            }
	        }
	        catch(e){
	            
	        }
	    }, 250);*/
}
/*
This options is not currently supported by most of old browsers, and it maight never be a polyfill
function iterateElement(element,parent){
	children = element.childNodes;
	for(let child of children){
	    var nParent = createElement(child,parent);
	    iterateElement(child,nParent);
	}
}
*/
function iterateElement(element,parent){
    if(element===undefined)
    {return false;}
    for(var i=0 ; i<element.childNodes.length ; i++){
        var nParent = createElement(element.childNodes[i],parent);
        iterateElement(element.childNodes[i],nParent);
    }
}
function setImg(image,element){
	var nW = '100%';
	var nH = 'auto';
	var alt = "";
	var complemento = "";
	if(!(image.width==undefined||image.width==null)){
		nW = image.width;
		complemento = "";
		document.getElementById("ad-container").style.textAlign = "center";
	}
	if(!(image.height==undefined||image.height==null)){
		nH = image.height;
	}
	if(!(image.alt==undefined||image.alt==null)){
		nH = image.alt;
	}
	var content = '<img src="/images/'+image.src+'" style="width:'+nW+' !important; max-width:100%; height:'+nH+' !important; '+complemento+'" alt="'+image.alt+'" onload="sendPostMessage()">';
	if(image.link)
		content = '<a href="'+image.link+'" target="_blank" >'+content+'</a>';
	element.innerHTML = content;
}
function setCode(code,element){
	var realCode = atob(code.code);
	var doc = (new DOMParser()).parseFromString(realCode, 'text/html');
	iterateElement(doc.querySelector("html"),adElement);
	if(code.code.indexOf("adsbygoogle")>=0){
		waitAdSense();
	}
}
function feedH(imageList){
    var result = "";
    var percentage = 100/imageList.length;
    var height = '';
    var width = '';
    console.log(imageList);
    for(var i = 0; i<imageList.length; i++)
    {
        var image = '';
        if(imageList[i].width!=''){
            width = "width:100%;max-width:"+imageList[i].width;
        }
        else{
            width = "width:100%";
        }
        if(imageList[i].height!=''){
            height = ""+imageList[i].height;
        }
        else{
            height = 'height:auto';
        }
        image = '<img style="'+width+';'+height+'" src="/images/'+imageList[i].src+'" alt="'+imageList[i].alt+'" onload="sendPostMessage()">';
        if(imageList[i].url!=""){
            image = '<a href="'+imageList[i].url+'" target="_blank">'+image+'</a>';
        }
        result += '<div style="width:'+percentage+'%;display:inline-block"><div style="padding:10px">'+image+'</div></div>';
    }
    return result;
}
function feedV(imageList){
    var result = "";
    var percentage = 100/imageList.length;
    var height = '';
    var width = '';
    console.log(imageList);
    for(var i = 0; i<imageList.length; i++)
    {
        //console.log("iterate "+i,imageList[i]);
        var image = '';
        if(imageList[i].width!=''){
            width = "width:100%;max-width:"+imageList[i].width;
        }
        else{
            width = "width:100%";
        }
        if(imageList[i].height!=''){
            height = ""+imageList[i].height;
        }
        else{
            height = 'height:auto';
        }
        image = '<img style="width:100%" src="/images/'+imageList[i].src+'" alt="'+imageList[i].alt+'" onload="sendPostMessage()">';
        if(imageList[i].url!=""){
            image = '<a href="'+imageList[i].url+'" target="_blank">'+image+'</a>';
        }
        result += '<div style="width:100%;margin-bottom:10px">'+image+'</div>';
    }
    return result;
}
function setFeed(feed,element){
    var result = '';
    if(feed.direction==0){
        result = feedH(feed.imageList);
    }
    else{
        result = feedV(feed.imageList);
    }
    element.innerHTML = result;
}
function setAd(ad){
	objInf = Object.keys(ad);
	randomN = Math.floor(Math.random()*objInf.length);
	elected = ad[objInf[randomN]];
	console.log("is type defined in",ad);
	isTypeDefined = true;
	try{
	    console.log(elected.type);
	}
	catch(e){
	    isTypeDefined = false;
	}
	if(!isTypeDefined){
		console.log("There's no type defined ",id,ad);
		document.getElementById('ad-container').style.height = '0px';
		sendPostMessage();
		return false;
	}
	if(elected.type=="image"){
		setImg(elected,adElement);
		return true;
	}
	else if(elected.type=="code"){
		setCode(elected,adElement);
		//document.querySelector("body").style.textAlign = "center";
		return true;
	}
	else if(elected.type=="feed"){
		//setCode(elected,adElement);
		//document.querySelector("body").style.textAlign = "center";
        setFeed(elected,adElement);
		return true;
	}
	console.error("Undefined ad type, only image, code and feed types are accepted");
	return false;
}
ads = JSON.parse(document.querySelector("noscript#data").innerText);
setAd(ads);
})();
function sendPostMessage(){
	height = document.getElementById('ad-container').offsetHeight;
	window.parent.postMessage(
		{
			frameHeight: height,
			id: id
		}, 
		'*'
	);
	console.log(height);
}
window.onresize = sendPostMessage();
