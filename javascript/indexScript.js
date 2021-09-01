$(window).on("load", function() {
    $('[data-toggle="tooltip"]').tooltip();
    $("#ldap-buttons").hide();
    localStorage.setItem("noLogin", "yes");
    let regInfo = localStorage.getItem("status");
    if (regInfo!=null)
        showRegInfo(regInfo);
    localStorage.removeItem("status");
    checkAlreadyLogin();
    getGoogleLink();
    $("input").on("input",function (){
        $("input").removeClass("is-invalid");
    })
});

function showRegInfo(regInfo){
    let regDiv = $("#reg-"+regInfo);
    regDiv.css("top", 0);
    setTimeout(function (){regDiv.css("top","-100px");},3000)

}
function submitOwnForm(){
    let form = document.getElementById("login-form");
    let email = $("#email");
    let password = $("#password");
    email.removeClass("is-invalid");
    password.removeClass("is-invalid");
    if (checkFormValidation(form)) {
        let request = new Request('api.php?type=login', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                if (!data.error){
                    if (data.login === true){
                        email.removeClass("is-invalid");
                        password.removeClass("is-invalid");
                        window.location.href = '2fa.html';
                    }
                    else if(data.login === -1){
                        $("#email-validation").text("Zadaný email neexistuje, najprv sa zaregistrujte");
                        email.addClass("is-invalid");

                    }
                    else if(data.login === false){
                        $("#password-validation").text("Zadali ste nesprávne heslo");
                        password.addClass("is-invalid");
                    }
                }
                else{
                    if (data.message ==='domain'){
                        $("#email-validation").text("@stuba.sk emailom sa prihlasujte cez LDAP.");
                        email.addClass("is-invalid");

                    }
                }
            });
    }
    return false;
}
function submitLDAPForm(){
    let email = $("#email");
    email.removeClass("is-invalid");
    $("#password").removeClass("is-invalid");
    let form = document.getElementById("login-form");
    if (checkFormValidation(form)) {
        let request = new Request('api.php?type=ldap', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                if (!data.error){
                    if (data.ldapStatus){
                        email.removeClass("is-invalid");
                        $("#password").removeClass("is-invalid");
                        window.location.href = 'account.html';
                    }
                    else{
                        $("#email-validation").text("Zadali ste nesprávne údaje");
                        $("#password-validation").text("Zadali ste nesprávne údaje");
                        email.addClass("is-invalid");
                        $("#password").addClass("is-invalid");
                    }
                }
                else{
                    if (data.message ==='domain'){
                        $("#email-validation").text("Email musí mať doménu @stuba.sk");
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

function checkAlreadyLogin(){
    fetch('api.php?type=loginCheck')
        .then(response => response.json())
        .then(data => {
            if (data.login)
                window.location.replace("account.html");
        });
}
function changeLoginType(loginType){
    let ldapLogin = $("#ldapLogin");
    let ownLogin = $("#ownLogin");
    if (loginType.id === "ownLogin"){
        $(loginType).css({
            "background-color":"white",
            "color": "#000080",
        });
        ldapLogin.css({
            "background-color":"#f0f0f0",
            "color": "black",
        });
        $("#email").attr("placeholder","Zadajte email");
        $("#own-buttons").show();
        $("#ldap-buttons").hide();
    }
    else {
        $(loginType).css({
            "background-color":"white",
            "color": "#000080",
        });
        ownLogin.css({
            "background-color":"#f0f0f0",
            "color": "black",
        });
        $("#email").attr("placeholder","Zadajte @stuba.sk email");
        $("#own-buttons").hide();
        $("#ldap-buttons").show();
    }
}
function getGoogleLink(){
    fetch('api.php?type=google')
        .then(response => response.json())
        .then(data => {
            showGoogleStatus(data.googleStatus);
            $("#google-button-img").on('click',function (){
                window.location.replace(data.googleLink);
            });
        });
}
function showGoogleStatus(googleStatus){
    let googleDiv = $("#google-"+googleStatus);
    googleDiv.css("top",0);
    setTimeout(function (){googleDiv.css("top","-100px");},10000)
}
