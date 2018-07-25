const pageNumLength = 6;
let pagination = document.getElementById('pagination');

function makePaging(maxPage, currentPage) {
    pageNumeration = [1];

    currentPage = parseInt(currentPage);
    for (let i = 2; i <= pageNumLength / 2; i++) {
        if (maxPage <= i) {
            return pageNumeration;
        }
        pageNumeration.push(i);
    }

    middlePage = currentPage - pageNumLength /2;
    if (pageNumeration[pageNumeration.length - 1] -  middlePage < -1) {
        pageNumeration.push('...');
    }


    for (let i = middlePage; i < currentPage + pageNumLength / 2; i++) {
        if (!pageNumeration.includes(i) && i > 0) {
            pageNumeration.push(i);
        }
        if (maxPage === i) {
            return pageNumeration;
        }
    }

    endingPage = maxPage - pageNumLength / 2;

    if (pageNumeration[pageNumeration.length - 1] -  endingPage < -1) {
        pageNumeration.push('...');
    }
    for (let i = endingPage; i <= maxPage; i++) {
        if (!pageNumeration.includes(i)) {
            pageNumeration.push(i);
        }
    }

    return pageNumeration;
}

function addPages(maxPage, currentPage) {
    let url = window.location.href;
    if (!url.includes('?page=')) {
        url += '?page=';
    } else {
        url = url.substring(0, url.indexOf('=') + 1);
    }

    let pagesHtml = '';
    let pageNumeration = makePaging(maxPage, currentPage);
    pageNumeration.forEach(function(pageNum){
        pagesHtml += '<a ';
        if (pageNum != page && pageNum !== '...') {
            pagesHtml += `href=` + url + pageNum + ` `
        }
        pagesHtml += `>${pageNum} </a>`
    });
    pagination.innerHTML = pagesHtml;
}

function getPageNum() {
    
}