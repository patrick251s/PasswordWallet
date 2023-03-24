let usersToShare = [];

$(document).on('click', '#addUserBTN', function(){ 
    let userLogin = $("#userLoginInput").val();
    if(userLogin.trim() === "") return;
    console.log(userLogin);
    $.post('controller.php', {
        submit: "isUserExist",
        login: userLogin
        }, 
        function(data, status) {
            displayUsers(data, userLogin);
        }
    );
    $("#userLoginInput").val("");
});

function displayUsers(data, userLogin) {
    if(data == 0 || usersToShare.includes(userLogin)) return;
    let elementHTML = '<div class="col-3 mx-auto alert alert-danger alert-dismissible fade show" role="alert"><b>'+userLogin+'</b>'+
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                          '<span aria-hidden="true">&times;</span>'+
                        '</button>'+
                      '</div>&nbsp;';
    $("#addedUsers").append(elementHTML);
    usersToShare.push(userLogin);
}

$(document).on('click', '#sharePasswordBTN', function(){ 
    if(usersToShare.length === 0) return;
    let url = new URL(window.location.href);
    let urlParam = url.searchParams.get("id");
    console.log(usersToShare);
    $.post('controller.php', {
        submit: "saveSharingPassword",
        idPass: urlParam,
        usersToShare: JSON.stringify(usersToShare)
        }, 
        function(data, status) {
            $(location).prop('href', 'passwords.php');
        }
    );
});

function removeUserFromSharing(removedUserLogin, passID) {
    $.post('controller.php', {
        submit: "removeUserFromSharing",
        userLogin: removedUserLogin,
        passID: passID
        }, 
        function(data, status) {
            $(location).prop('href', 'passwords.php');
        }
    );
}
