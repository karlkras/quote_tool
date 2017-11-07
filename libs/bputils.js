function $_GET() {
    var vars = {};
    window.location.href.replace(location.hash, '').replace(
            /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
            function (m, key, value) { // callback
                vars[key] = value !== undefined ? cleanGetValue(value) : '';
            }
    );
    return vars;
}

function cleanGetValue(val) {
    var s = val.replace(/\+/g, '%20');
    return  decodeURIComponent(s);
}
