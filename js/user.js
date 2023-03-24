document.getElementById('eyePassword').addEventListener('click', function() {
    let passInput = document.getElementById("passwordAddInput");
    if(passInput.type === "password") {
        passInput.type = "text";
    }
    else {
        passInput.type = "password";
    }
});


