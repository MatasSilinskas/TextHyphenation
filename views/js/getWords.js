let words = document.getElementById('words');
let request = new XMLHttpRequest();

request.onreadystatechange = function() {
    if(request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("loader").style.display = "none";
            let myObj = JSON.parse(this.responseText);
            let txt = '<table class="table table-hover table-bordered">' +
                "<tr>" +
                "<th>Id</th>" +
                "<th>Word</th>" +
                "<th>Hyphenated</th>\n" +
                "<th>Delete</th>\n" +
                "</tr>";
            for (let row in myObj) {
                txt +=
                    `<tr><td>${myObj[row].id}</td>` +
                    `<td>${myObj[row].word}</td>` +
                    `<td>${myObj[row].hyphenated}</td>` +
                    `<td><button type="button" class="btn btn-danger" value="${myObj[row].word}" ` +
                    `onclick="deleteWord(this.value)">` + `Delete</button></td>` +
                    `</tr>`;
            }
            txt += "</table>";
            words.innerHTML = txt;
        }
    }
};

request.open('Get', 'api/words');
request.send();

function deleteWord(word){
    let deleteRequest = new XMLHttpRequest();
    deleteRequest.onload = function() {
        if (deleteRequest.status === 200) {
            location.reload();
            alert('Delete was successful!');
        } else {
            alert('Something went wrong');
        }
    };
    deleteRequest.open('Delete', 'api/words');
    let data = {word:word};
    deleteRequest.send(JSON.stringify(data));
}