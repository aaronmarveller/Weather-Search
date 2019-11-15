var states = ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "District Of Columbia", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"];

initializeSelect = function(){
    var select = document.getElementById('state');
    for(let i = 0; i < states.length; i++) {
        var opt = document.createElement('option');
        opt.innerHTML = states[i];
        select.appendChild(opt);
    }

}


isCorrect = function(){
    clearAlertBox();
    clearResult();
    if(document.getElementById("checkbox").checked){
        return true;
    } else {
        var street = document.getElementById("street").value;
        var city = document.getElementById("city").value;
        var re = /^\s*$/;
        if (street == null || street == "" || city == null || city == "" || re.test(street) || re.test(city)) {
            createAlertBox();
            return false;
        }
    }
}

clearForm = function() {
    document.getElementById("checkbox").checked = false;
    clearLeftBlock();
    clearResult();
    clearAlertBox();
    removeDisabled();
}

clearLeftBlock = function(){
    Array.from(document.getElementsByTagName("input")).slice(0,2).forEach((input) => {
        input.value = "";
    });
    document.getElementById("state").options[0].selected = true
}


clearResult = function() {
    var result = document.getElementById("result");
    if(result) result.parentNode.removeChild(result);

}

clearAlertBox = function(){
    var alertBox = document.getElementsByClassName("alertBox")[0];
    if(alertBox) alertBox.parentNode.removeChild(alertBox);
}

createAlertBox = function(){
    var alertBox = document.createElement("div");
    alertBox.innerText = "Please Check the input address.";
    alertBox.setAttribute("class", "alertBox");
    document.getElementById("searchModule").parentNode.appendChild(alertBox);
}

lockForm = function () {
    if(!document.getElementsByClassName("info")[0].getAttribute("disabled")){
        clearLeftBlock();
        getIP();
        document.getElementsByClassName("info")[0].setAttribute("disabled", true);
        document.getElementsByClassName("info")[1].setAttribute("disabled", true);
        document.getElementsByTagName("select")[0].setAttribute("disabled", true);
        document.getElementsByTagName("select")[0].setAttribute("background-color", "rgb(218,218,218");
    } else {
        removeDisabled();
    }
}

getIP = function(){
    var ip;
    var request = new XMLHttpRequest();
    request.open('GET', "http://ip-api.com/json", false);  // `false` makes the request synchronous
    request.send(null);
    if (request.status === 200) {
        try {
            ip = JSON.parse(request.responseText);
            document.getElementById("lat").value = ip.lat;
            document.getElementById("lng").value = ip.lon;
            document.getElementById("current_city").value = ip.city;
        } catch (e) {
            ip = null;
        }
    }
}

removeDisabled = function(){
    document.getElementsByClassName("info")[0].removeAttribute("disabled");
    document.getElementsByClassName("info")[1].removeAttribute("disabled");
    document.getElementsByTagName("select")[0].removeAttribute("disabled");
    document.getElementsByTagName("select")[0].removeAttribute("background-color");
}

showChart = function () {
    if(document.getElementById("resultChart").style.display == ""){
        document.getElementById("resultChart").style.display = "block";
        document.getElementsByClassName("arrow")[0].src
            = "https://cdn0.iconfinder.com/data/icons/navigation-set-arrows-part-one/32/ExpandLess-512.png";
    } else {
        document.getElementById("resultChart").style.display = "";
        document.getElementsByClassName("arrow")[0].src
            = "https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png";
    }
}

submitTime = function(time, lat, lng){
    var xhr = new XMLHttpRequest();
    xhr.open("POST","forecast.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var street = document.getElementById("street").value;
    var city = document.getElementById("city").value;
    var state = document.getElementById("state").value;
    var checkbox = document.getElementById("checkbox").checked;
    xhr.send("street="+street+"&city="+city+"&state="+state+"&checkbox="+checkbox+"&time="+time+"&lat="+lat+"&lng="+lng);
    xhr.onreadystatechange = function() {
        if(this.readyState==4&&this.status==200) {
            var body = document.getElementsByTagName("body")[0];
            var rawHTML = xhr.responseText;
            body.innerHTML = rawHTML;
            const scripts = body.querySelectorAll('script');
            for (var i=0; i<scripts.length; i++) {
                runJS(scripts[i]);
            }
            function runJS(script){
                const newScript = document.createElement('script');
                newScript.innerHTML = script.innerHTML;
                document.head.appendChild(newScript);
                document.head.removeChild(newScript);
            }
        }
    }
}