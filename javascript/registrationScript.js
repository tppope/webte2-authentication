$(window).on("load", function() {
    $('[data-toggle="tooltip"]').tooltip();
    show2FA();
    $("input").on("input",function (){
        $(this).removeClass("is-invalid");
    })
});
function checkSamePasswords(){
    let password2 = $("#password-repeat");
    if ($("#password").val()!==password2.val())
        password2.get(0).setCustomValidity("Heslá sa musia zhodovať");
    else
        password2.get(0).setCustomValidity("");
}
function submitForm(){
    let email = $("#email");
    let form = document.getElementById("registration-form");
    if (checkFormValidation(form)) {
        let request = new Request('api.php?type=reg', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    localStorage.setItem("status", data.status);
                    email.removeClass("is-invalid");
                    $("#code").removeClass("is-invalid");
                    window.location.replace("index.html");
                }
                else {
                    if(data.message ==='domain'){
                        $("#email-validation").text("@stuba.sk email je používaný pri LDAP prihlásení");
                        email.addClass("is-invalid");
                    }
                    else if(data.message ==='2FA-error'){
                        $("#code").addClass("is-invalid");
                    }
                    else{
                        $("#email-validation").text("Email je už použitý pre iné konto");
                        email.addClass("is-invalid");
                    }
                }
            });
    }
}
function checkFormValidation(form){
    let inputs = $(form).find("input");
    for (let i = 0; i < inputs.length; i++) {
        if (!inputs.get(i).checkValidity())
            return false;
    }
    return true;
}

function show2FA(){
    fetch('api.php?type=get2FA')
        .then(response => response.json())
        .then(data => {
            showQrCode(data.qrCode);
        });
}
function showQrCode(qrCode){
    let qrCodeImg = document.createElement("img");
    $(qrCodeImg).attr("src",qrCode);
    $("#qr-code").append(qrCodeImg);
}
