// load css
var cssId = 'simotelCss';
if (!document.getElementById(cssId)) {
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.id = cssId;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = rootWebUrl + '/modules/addons/simotel/templates/css/simotel.css?ver=' + window.SimotelConnectVersion;
    link.media = 'all';
    head.appendChild(link);
}
