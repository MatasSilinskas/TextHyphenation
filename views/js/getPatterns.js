let bio = document.getElementById('patterns');

let request = new XMLHttpRequest();

request.onreadystatechange = function() {
    if(request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            myObj = JSON.parse(this.responseText);
            let txt = '<table class="table table-hover table-bordered">' +
                "<tr>" +
                "<th>Id</th>" +
                "<th>Pattern</th>" +
                "</tr>";
            for (let row in myObj) {
                txt +=
                    `<tr><td>${myObj[row].id}</td>` +
                    `<td>${myObj[row].pattern}</td></tr>`;
            }
            txt += "</table>";
            bio.innerHTML = txt;
        }
    }
};

request.open('Get', 'api/patterns');
request.send();