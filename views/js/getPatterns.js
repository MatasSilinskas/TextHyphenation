let patterns = document.getElementById('patterns');
let request = new XMLHttpRequest();

request.onreadystatechange = function() {
    if (request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("loader").style.display = "none";
            let myObj = JSON.parse(this.responseText);
            let txt = '<table class="table table-hover table-bordered">' +
                "<tr>" +
                "<th>Id</th>" +
                "<th>Pattern</th>" +
                "<th>Delete</th>" +
                "<th>Update</th>" +
                "</tr>";

            myObj.patterns.forEach(function (pattern) {
                txt +=
                    `<tr><td>${pattern.id}</td>` +
                    `<td id='pattern${pattern.id}' contenteditable="true">${pattern.pattern}</td>` +
                    `<td><button type="button" class="btn btn-danger" value="${pattern.pattern}" ` +
                    `onclick="deletePattern(this.value)">` + `Delete</button></td>` +
                    `<td><button type="button" class="btn btn-info" value="${pattern.pattern}" ` +
                    `onclick="updatePattern(this.value, document.getElementById('pattern${pattern.id}').innerText)">` + `Update</button></td>` +
                    `</tr>`;
            });
            txt += "</table>";
            patterns.innerHTML = txt;

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
request.open('Get', 'api/patterns?page=' + page);
request.send();

function deletePattern(pattern){
    let deleteRequest = new XMLHttpRequest();
    deleteRequest.onload = function() {
        if (deleteRequest.status === 200) {
            location.reload();
            alert('Delete was successful!');
        } else {
            alert('Something went wrong');
        }
    };
    deleteRequest.open('Delete', 'api/patterns');
    let data = {pattern:pattern};
    deleteRequest.send(JSON.stringify(data));
}

function updatePattern(oldPattern, newPattern) {
    let updateRequest = new XMLHttpRequest();
    updateRequest.onload = function() {
        if (updateRequest.status === 200) {
            location.reload();
            alert('Update was successful!');
        } else {
            alert('Something went wrong');
        }
    };

    updateRequest.open('Put', 'api/patterns');
    let data = {oldPattern:oldPattern, newPattern:newPattern};
    alert(JSON.stringify(data));
    updateRequest.send(JSON.stringify(data));
}

