$(document).ready(function() {
    $.post('controller.php', {
        submit: "getUserPasswords"
        }, 
        function(data, status) {
            if(data !== null){
                console.log(data);
                displayUserPasswords(data);
            }
        },
        "json"
    );
});

function displayUserPasswords(data) {
    let passwordPage = document.getElementById("savedPasswords");
    let HTML = "";
    if(data.address.length === 0) {
        HTML += '<div class="h4 text-warning mx-auto">You haven\'t got any added passwords yet!</div>';
    }
    else {
        for(let i=0; i<data.address.length; i++) {
            let owner = "";
            if(data.isOwnPassword[i] === false) owner = " (Owner: "+data.passwordOwner[i]+")";
            HTML += '<div class="col-sm-10 col-xl-8 mx-auto row bg-light my-3 border border-success">'+
                        '<div class="col-9 row py-3 mt-1">'+
                            '<h5>Web address: &nbsp '+data.address[i]+'<br/>'+owner+'</h5>'+
                        '</div>'+
                        '<div class="col-3 my-2 p-2 d-flex justify-content-end">'+
                            '<button class="btn btn-success col-6" type="button" data-toggle="collapse" data-target="#collapseContent'+data.passwordID[i]+ '" aria-expanded="false" aria-controls="collapseContent'+data.passwordID[i]+'"><b>↓</b></button>'+
                        '</div>'+

                        '<div class="collapse col-12" id="collapseContent'+data.passwordID[i]+'">'+
                            '<div class="card card-body">'+
                                '<div class="h6 py-2">Login: &nbsp '+data.login[i]+'</div>'+
                                '<div class="h6 py-2" id="userPassword'+data.passwordID[i]+'">Password: &nbsp **********</div>';
                        if(data.description[i] !== "") HTML += '<div class="h6 py-2">Description: &nbsp '+data.description[i]+'</div>'; 
                        if(data.sharedPassLogin[i].length !== 0) {
                            HTML += '<div class="h6 py-2">Shared for users: '+
                                        '<table class="table text-center"><thead><tr><th scope="col">Login</th><th scope="col">Action</th></tr></thead><tbody>';
                                for(let j=0; j<data.sharedPassLogin[i].length; j++) {
                                    HTML += '<tr><td>'+data.sharedPassLogin[i][j]+'</td><td><button class="btn btn-outline-danger col-sm-6 mx-auto" onClick="removeUserFromSharing('+"'"+data.sharedPassLogin[i][j]+'\', '+data.passwordID[i]+')">Remove</button></td></tr>';
                                }
                                HTML += '</tbody></table></div>';
                        } 
                  
                  HTML += '</div>';
                  if(data.isOwnPassword[i] === true) {
                      HTML += '<form method="POST" action="controller.php">'+
                                '<div class="py-2 row">'+                              
                                    '<input type="hidden" name="passwordID" value="'+data.passwordID[i]+'"/>'+
                                    '<button id="showBTN'+data.passwordID[i]+'" type="button" value="show" class="btn btn-secondary mx-auto col-3" onClick="showUserPassword('+data.passwordID[i]+')">Show password</button>'+
                                    '<a href="sharePassword.php?id='+data.passwordID[i]+'" role="button" class="btn btn-info mx-auto col-3">Share password</a>'+
                                    '<button type="submit" name="submit" value="deleteUserPassword" class="btn btn-danger mx-auto col-3">Delete</button>'+
                                '</div>'+
                            '</form>';
                  }
                  HTML +=         
                        '</div>'+
                    '</div>';
        }
        
    }
    passwordPage.innerHTML = HTML;
}

function showUserPassword(passwordID) {
    let showBTN = document.getElementById("showBTN"+passwordID);
    let passDIV = document.getElementById("userPassword"+passwordID);
    if(showBTN.value === "show") {
        $.post('controller.php', {
            submit: "showUserPassword",
            passwordID: passwordID
            }, 
            function(data, status) {
                if(data !== null) {
                    showBTN.value = "hide";
                    showBTN.innerHTML = "Hide password";
                    passDIV.innerHTML = "Password: &nbsp " + data;
                }

            }
        );
    }
    else if(showBTN.value === "hide") {
        showBTN.value = "show";
        showBTN.innerHTML = "Show password";
        passDIV.innerHTML = "Password: &nbsp **********";
    }
    
    
}


