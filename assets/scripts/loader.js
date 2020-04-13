/*
	Ads loader main JS file
	by bonAngeLOL

*/
(function (){
let adElement = document.getElementById("ad-container");
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
function iterateElement(element,parent){
    children = element.childNodes;
    for(let child of children){
        var nParent = createElement(child,parent);
        iterateElement(child,nParent);
    }
}
function setImg(image,element){
	var nW = image.width;
	var nH = image.height;
	var content = '<img src="http://127.0.0.1:8081/adserver/project/adServer/images/'+image.src+'" style="width:'+(nW??'auto') +' !important; max-width:100%; height:'+(nH??'auto')+'; !important" alt="'+(image.alt??'')+'">';
	if(image.link)
		content = '<a href="'+image.link+'" target="_blank" >'+content+'</a>';
	element.innerHTML = content;
}
function setCode(code,element){
	var realCode = atob(code.code);
	var doc = (new DOMParser()).parseFromString(realCode, 'text/html');
	iterateElement(doc.querySelector("html"),adElement);
}
function setAd(ad){
	objInf = Object.keys(ad);
	randomN = Math.floor(Math.random()*objInf.length);
	elected = ad[objInf[randomN]];
	if(elected.type=="image"){
		setImg(elected,adElement);
		return true;
	}
	else if(elected.type=="code"){
		setCode(elected,adElement);
		return true;
	}
	console.error("Undefined ad type, only image and code types are accepted");
	return false;
}
ads = JSON.parse(document.querySelector("noscript#data").innerText);
setAd(ads);
})();
