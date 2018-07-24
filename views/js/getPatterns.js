let bio = document.getElementById('patterns');
let request = new XMLHttpRequest();

request.onreadystatechange = function() {
    if (request.readyState === 4) {
        if (this.readyState === 4 && this.status === 200) {
            let myObj = JSON.parse(this.responseText);
            let txt = '<table class="table table-hover table-bordered">' +
                "<tr>" +
                "<th>Id</th>" +
                "<th>Pattern</th>" +
                "<th>Delete</th>" +
                "</tr>";
            for (let row in myObj) {
                txt +=
                    `<tr><td>${myObj[row].id}</td>` +
                    `<td>${myObj[row].pattern}</td>` +
                    `<td><button type="button" class="btn btn-danger" value="${myObj[row].pattern}" ` +
                    `onclick="deletePattern(this.value)">` + `Delete</button></td>` +
                    `</tr>`;
            }
            txt += "</table>";
            bio.innerHTML = txt;
        }
    }
};

request.open('Get', 'api/patterns');
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

