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

            myObj.words.forEach(function (word) {
                txt +=
                    `<tr><td>${word.id}</td>` +
                    `<td>${word.word}</td>` +
                    `<td>${word.hyphenated}</td>` +
                    `<td><button type="button" class="btn btn-danger" value="${word.word}" ` +
                    `onclick="deleteWord(this.value)">` + `Delete</button></td>` +
                    `</tr>`;
            });
            txt += "</table>";
            words.innerHTML = txt;

            let maxPage = Math.ceil(myObj.count / myObj.limit);
            addPages(maxPage, page);
        }
    }
};

let regex = /page=(\d+)/;
let pageParams = regex.exec(window.location.href);
if (pageParams === null) {
    page = 1;
} else {
    page = pageParams[1];
}
request.open('Get', 'api/words?page=' + page);
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