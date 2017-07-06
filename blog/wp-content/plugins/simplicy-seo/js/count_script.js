
var maxLength=100;
function charLimit(el) {
    function charLimit(el) {
    if (el.value.length == maxLength) return false;
    return true;
}
}
function characterCount(el) {
    var charCount = document.getElementById('charCount');
    if (el.value.length == maxLength) el.value = el.value.substring(0,maxLength);
    if (charCount) charCount.innerHTML =  maxLength - el.value.length;
    return true;
}




var maxLength2=200;
function charLimit2(el) {
    function charLimit2(el) {
    if (el.value.length == maxLength2) return false;
    return true;
}
}
function characterCount2(el) {
    var charCount2 = document.getElementById('charCount2');
    if (el.value.length == maxLength2) el.value = el.value.substring(0,maxLength2);
    if (charCount2) charCount2.innerHTML =  maxLength2 - el.value.length;
    return true;
}
