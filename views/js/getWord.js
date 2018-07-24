let pattern = document.getElementById('word');
let request = new XMLHttpRequest();
let url = window.location.href;

let splitted = url.split('/');
let id = splitted[splitted.length - 1];

request.open('GET', '../api/words/' + id);

request.onreadystatechange = function() {
    if (request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            let myObj = JSON.parse(this.responseText);
            pattern.innerHTML = '<span class="bigFont">Hyphenated form of word \'' + myObj[0].word + '\' is: ' + myObj[0].hyphenated + '</span>';
        }
    }
};

request.send();
