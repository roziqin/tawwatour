//return an array of objects according to key, value, or key and value matching
function getObjects(obj, key, val) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i)) continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getObjects(obj[i], key, val));    
        } else 
        //if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
        if (i == key && obj[i] == val || i == key && val == '') { //
            objects.push(obj);
        } else if (obj[i] == val && key == ''){
            //only add if the object is not already in the array
            if (objects.lastIndexOf(obj) == -1){
                objects.push(obj);
            }
        }
    }
    return objects;
}

//return an array of values that match on a certain key
function getValues(obj, key) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i)) continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getValues(obj[i], key));
        } else if (i == key) {
            objects.push(obj[i]);
        }
    }
    return objects;
}

//return an array of keys that match on a certain value
function getKeys(obj, val) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i)) continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getKeys(obj[i], val));
        } else if (obj[i] == val) {
            objects.push(i);
        }
    }
    return objects;
}

function xePrint(title,content,additionalCss=''){
        $('<iframe id="xePrintFrame" style="display:none">').prependTo('body');
        var printFrame = document.getElementById('xePrintFrame');
        var frameDoc = printFrame.contentDocument || printFrame.contentWindow.document;
        var css = 'body{padding:5mm;}table{width:100%} table,tr,td,th{border:thin solid black; border-collapse:collapse} td,th{padding:5px; font-size:10px;} h1{font-size:16px;} h2{font-size:14px;} h3{font-size:12px;}';
        var frameContent = '<html><head><title>'+title+'</title><style>'+css+' '+additionalCss+'</style></head><body><h1>'+title+'</h1>';
        frameContent += content;
        frameContent += '</body></html>';
        frameDoc.write(frameContent);
        frameDoc.close();
        printFrame.focus();
        printFrame.contentWindow.print();
        $('#xePrintFrame').remove();
    }
function formatAngka(angka) {
    if (typeof(angka) != 'string') angka = angka.toString();
    var reg = new RegExp('([0-9]+)([0-9]{3})');
    while(reg.test(angka)) angka = angka.replace(reg, '$1.$2');
    return angka;
}
function ucfirst(text)
{
    return text.charAt(0).toUpperCase() + text.substr(1);
}
function ucwords(text)
{
    return text.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
}