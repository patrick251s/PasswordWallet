$(document).ready(function() {
    console.log("Hej");
    $.post('controller.php', {
        submit: "getUserLoginActivity"
        }, 
        function(data, status) {
            console.log(data);
            displayUserLoginActivity(data);
        },
        "json"
    );
});

function displayUserLoginActivity(data) {
    let HTML = '<table class="table table-dark"><thead><tr><th scope="col">Time</th><th scope="col">Correctness</th></tr></thead><tbody>';
    for(let i=0; i<data.time.length; i++) {
        HTML += '<tr><td>'+data.time[i]+'</td><td>'+getCorrectness(data.isCorrect[i])+'</td></tr>';
    }
    HTML += '</tbody></tbody>';
    document.getElementById("userLoginActivity").innerHTML = HTML;
}

function getCorrectness(boolean) {
    if(boolean == "1") {
        return "True";
    }
    else {
        return "False";
    }
}
