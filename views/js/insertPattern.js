function insertPattern() {
    let request = new XMLHttpRequest();
    request.open(document.patternForm.method, 'api/patterns');
    let data = {
        "pattern": document.patternForm.pattern.value
    };
    request.send(JSON.stringify(data));
}