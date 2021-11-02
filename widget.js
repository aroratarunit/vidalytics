if(document.body !== null){
	widget = document.createElement("div");
	widget.id = "widgetcontainer";
	widget.style='position: absolute;right: 30px;bottom: 50px;height: 501px;width: 500px;z-index: 999999;';
	document.getElementsByTagName('body')[0].appendChild(widget);
	widget.innerHTML='<object style="height: 100%;width: 100%;" type="text/html" data="https://www.hostname.com/widget.html" ></object>';
}