let bio = document.getElementById('words');

let request = new XMLHttpRequest();

request.onreadystatechange = function() {
    if(request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            myObj = JSON.parse(this.responseText);
            let txt = "<table>" +
                "<tr>" +
                "<th>Id</th>" +
                "<th>Word</th>" +
                "<th>Hyphenated</th>\n" +
                "</tr>";
            for (let row in myObj) {
                txt +=
                    `<tr><td>${myObj[row].id}</td>` +
                    `<td>${myObj[row].word}</td>` +
                    `<td>${myObj[row].hyphenated}</td></tr>`;
            }
            txt += "</table>";
            bio.innerHTML = txt;
        }
    }
};

request.open('Get', 'api/words');
request.send();