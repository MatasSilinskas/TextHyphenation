let pattern = document.getElementById('pattern');
let request = new XMLHttpRequest();
let url = window.location.href;

let splitted = url.split('/');
let id = splitted[splitted.length - 1];

request.open('GET', '../api/patterns/' + id);

request.onreadystatechange = function() {
    if (request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            let myObj = JSON.parse(this.responseText);
            pattern.innerHTML = 'The pattern`s value is: ' + myObj[0].pattern;
        }
    }
};

request.send();
